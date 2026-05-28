<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        if ($user?->isSupplier()) {
            $supplierId = $user->supplier_id;

            if ($supplierId) {
                return redirect()->route('suppliers.dashboard', $supplierId);
            }

            return redirect()->route('stock-requests.index');
        }

        if ($user?->role === 'admin') {
            $totalManagers = \App\Models\User::where('role', 'manager')->count();
            $restrictedManagers = \App\Models\User::where('role', 'manager')->where('is_restricted', true)->count();
            $pendingRestorations = \App\Models\DataRestorationRequest::where('status', 'pending')->count();
            $totalAuditLogs = \App\Models\AuditLog::count();

            // Last 7 days audit log counts
            $auditLogs = \App\Models\AuditLog::where('created_at', '>=', now()->subDays(6)->startOfDay())->get();
            $adminLogLabels = [];
            $adminLogValues = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateString = $date->format('Y-m-d');
                $adminLogLabels[] = $date->format('M d');
                $adminLogValues[] = $auditLogs->filter(function ($l) use ($dateString) {
                    return $l->created_at->format('Y-m-d') === $dateString;
                })->count();
            }

            // Restoration breakdown
            $restorationBreakdown = [
                \App\Models\DataRestorationRequest::where('status', 'pending')->count(),
                \App\Models\DataRestorationRequest::where('status', 'approved')->count(),
                \App\Models\DataRestorationRequest::where('status', 'rejected')->count(),
            ];

            $recentAuditLogs = \App\Models\AuditLog::with('user')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', [
                'totalManagers' => $totalManagers,
                'restrictedManagers' => $restrictedManagers,
                'pendingRestorations' => $pendingRestorations,
                'totalAuditLogs' => $totalAuditLogs,
                'adminLogLabels' => $adminLogLabels,
                'adminLogValues' => $adminLogValues,
                'restorationBreakdown' => $restorationBreakdown,
                'recentAuditLogs' => $recentAuditLogs,
            ]);
        }

        $lowStockQuery = Product::whereColumn('stock_quantity', '<=', 'reorder_level')
            ->whereDoesntHave('stockRequests', function ($query) {
                $query->whereIn('status', ['pending', 'processing', 'shipped']);
            });

        $lowStockCount = $lowStockQuery->count();

        // 1. Stock volume by Category
        $categoryStockData = Category::with('products')->get()->map(function ($category) {
            return [
                'name' => $category->name,
                'total_stock' => (int) $category->products->sum('stock_quantity'),
            ];
        })->sortByDesc('total_stock')->values();

        $categoryLabels = $categoryStockData->pluck('name');
        $categoryValues = $categoryStockData->pluck('total_stock');

        // 2. Product Stock Status Breakdown
        $outOfStockCount = Product::where('stock_quantity', '=', 0)->count();
        $lowStockCountForChart = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->count();
        $healthyStockCount = Product::whereColumn('stock_quantity', '>', 'reorder_level')->count();
        $stockBreakdown = [$healthyStockCount, $lowStockCountForChart, $outOfStockCount];

        // 3. Stock Movement Trends (last 7 days)
        $movements = StockMovement::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->whereNull('voided_at')
            ->get();

        $movementLabels = [];
        $movementIn = [];
        $movementOut = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $movementLabels[] = $date->format('M d');
            
            $dailyMovements = $movements->filter(function ($m) use ($dateString) {
                return $m->created_at->format('Y-m-d') === $dateString;
            });

            $movementIn[] = (int) $dailyMovements->where('type', 'in')->sum('quantity');
            $movementOut[] = (int) $dailyMovements->where('type', 'out')->sum('quantity');
        }

        $productsDetail = Product::with(['category', 'supplier'])->orderBy('stock_quantity', 'asc')->get();

        $recentMovements = StockMovement::with(['product', 'user'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->whereNull('voided_at')
            ->latest()
            ->get();

        return view('dashboard', [
            'totalCategories' => Category::count(),
            'totalProducts' => Product::count(),
            'lowStockCount' => $lowStockCount,
            'totalSuppliers' => Supplier::count(),
            'categoryLabels' => $categoryLabels,
            'categoryValues' => $categoryValues,
            'stockBreakdown' => $stockBreakdown,
            'movementLabels' => $movementLabels,
            'movementIn' => $movementIn,
            'movementOut' => $movementOut,
            'productsDetail' => $productsDetail,
            'recentMovements' => $recentMovements,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function notifications(): View
    {
        $user = Auth::user();
        $notifications = collect();

        if ($user) {
            if (!$user->isSupplier()) {
                // Manager Notifications: Arriving Today or Overdue Delivery Requests
                $deliveries = collect();
                if ($user->role === 'manager') {
                    $deliveries = \App\Models\StockRequest::with('product')
                        ->whereIn('status', ['processing', 'shipped'])
                        ->whereNotNull('expected_delivery_at')
                        ->whereDate('expected_delivery_at', '<=', now())
                        ->get()
                        ->map(function ($notif) {
                            return [
                                'type' => 'delivery',
                                'title' => $notif->product->name,
                                'message' => $notif->expected_delivery_at->isToday() ? 'Delivery arriving today' : 'Delivery overdue',
                                'badge_text' => $notif->expected_delivery_at->isToday() ? 'TODAY' : 'OVERDUE',
                                'badge_class' => $notif->expected_delivery_at->isToday() 
                                    ? 'notif-badge-delivery-today' 
                                    : 'notif-badge-delivery-overdue',
                                'link' => route('stock-requests.index'),
                                'time' => $notif->updated_at ?? $notif->created_at,
                            ];
                        });
                }

                // Manager Notifications: Low Stock Alert
                $lowStockProducts = collect();
                if ($user->role === 'manager') {
                    $lowStockProducts = \App\Models\Product::whereColumn('stock_quantity', '<=', 'reorder_level')
                        ->whereDoesntHave('stockRequests', function ($query) {
                            $query->whereIn('status', ['pending', 'processing', 'shipped']);
                        })
                        ->get()
                        ->map(function ($product) {
                            $isOut = $product->stock_quantity == 0;
                            return [
                                'type' => 'low_stock',
                                'title' => $product->name,
                                'message' => $isOut 
                                    ? 'Out of stock (Reorder level: ' . $product->reorder_level . ')' 
                                    : 'Low stock: ' . $product->stock_quantity . ' left (Reorder level: ' . $product->reorder_level . ')',
                                'badge_text' => $isOut ? 'OUT' : 'LOW',
                                'badge_class' => $isOut 
                                    ? 'notif-badge-out' 
                                    : 'notif-badge-low',
                                'link' => route('products.index', ['filter' => 'low_stock']),
                                'time' => $product->updated_at ?? $product->created_at,
                            ];
                        });
                }

                $productEditReqs = collect();
                $profileEditReqs = collect();
                $dataRestorations = collect();

                if ($user->role === 'manager') {
                    // Product Edit Requests (Visible to Admin only)
                    $productEditReqs = \App\Models\ProductEditRequest::where('status', 'pending')
                        ->with(['product', 'supplier'])
                        ->get()
                        ->map(function ($req) {
                            return [
                                'type' => 'product_edit_request',
                                'title' => "Product Edit: " . ($req->product->name ?? 'Unknown'),
                                'message' => "Requested by " . ($req->supplier->name ?? 'Supplier') . ": " . \Illuminate\Support\Str::limit($req->reason, 40),
                                'badge_text' => 'EDIT',
                                'badge_class' => 'notif-badge-edit',
                                'link' => route('supplier-requests.index'),
                                'time' => $req->created_at,
                            ];
                        });

                    // Supplier Profile Edit Requests (Visible to Admin only)
                    $profileEditReqs = \App\Models\SupplierProfileEditRequest::where('status', 'pending')
                        ->with('supplier')
                        ->get()
                        ->map(function ($req) {
                            return [
                                'type' => 'supplier_profile_edit_request',
                                'title' => "Profile Edit: " . ($req->supplier->name ?? 'Supplier'),
                                'message' => "Changes pending approval",
                                'badge_text' => 'PROFILE',
                                'badge_class' => 'notif-badge-profile',
                                'link' => route('supplier-profile-requests.index'),
                                'time' => $req->created_at,
                            ];
                        });
                }

                if ($user->role === 'admin') {
                    // Data Restoration Requests (Visible to Admin only)
                    $dataRestorations = \App\Models\DataRestorationRequest::where('status', 'pending')
                        ->with('user')
                        ->get()
                        ->map(function ($req) {
                            $modelName = class_basename($req->model_type);
                            return [
                                'type' => 'data_restoration_request',
                                'title' => "Restore: " . $modelName,
                                'message' => "Requested by " . ($req->user->name ?? 'User') . ": " . \Illuminate\Support\Str::limit($req->reason, 40),
                                'badge_text' => 'RESTORE',
                                'badge_class' => 'notif-badge-restore',
                                'link' => route('data-restorations.index'),
                                'time' => $req->created_at,
                            ];
                        });
                }

                $notifications = $deliveries
                    ->concat($lowStockProducts)
                    ->concat($productEditReqs)
                    ->concat($profileEditReqs)
                    ->concat($dataRestorations)
                    ->sortByDesc('time');
            } else {
                // Supplier Notifications: New/Pending requests
                $notifications = \App\Models\StockRequest::with('product')
                    ->where('supplier_id', $user->supplier_id)
                    ->where('status', 'pending')
                    ->get()
                    ->map(function ($notif) {
                        return [
                            'type' => 'supplier_request',
                            'title' => $notif->product->name,
                            'message' => 'New request: ' . $notif->quantity_requested . ' units',
                            'badge_text' => 'NEW',
                            'badge_class' => 'notif-badge-new',
                            'link' => route('stock-requests.index'),
                            'time' => $notif->created_at,
                        ];
                    });
            }
        }

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}