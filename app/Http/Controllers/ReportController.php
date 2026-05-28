<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        return view('reports.index', compact('products'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:inventory,movements,suppliers,ledger'],
            'product_id' => ['required_if:type,ledger', 'exists:products,id'],
            'month' => ['required_if:type,ledger', 'date_format:Y-m'],
        ]);

        $type = $validated['type'];
        $data = $this->getReportData($type, $validated);

        return view('reports.preview', compact('data', 'type'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:inventory,movements,suppliers,ledger'],
            'product_id' => ['required_if:type,ledger', 'exists:products,id'],
            'month' => ['required_if:type,ledger', 'date_format:Y-m'],
        ]);

        $type = $validated['type'];
        $data = $this->getReportData($type, $validated);

        $pdf = Pdf::loadView("reports.pdf.{$type}", compact('data'));

        if ($type === 'movements') {
            $pdf->setPaper('A4', 'landscape');
        } else {
            $pdf->setPaper('A4', 'portrait');
        }

        $filename = "FarmFlow_{$type}_report_" . now()->format('Y_m_d_His') . ".pdf";

        return $pdf->download($filename);
    }

    private function getReportData(string $type, array $params = [])
    {
        switch ($type) {
            case 'inventory':
                return Product::with('category', 'supplier')
                    ->orderBy('name')
                    ->get();
            
            case 'movements':
                return StockMovement::with('product', 'user')
                    ->orderByDesc('created_at')
                    ->take(500)
                    ->get();

            case 'suppliers':
                return Supplier::with('contacts')
                    ->orderBy('name')
                    ->get();
            
            case 'ledger':
                $product = Product::findOrFail($params['product_id']);
                $startDate = \Carbon\Carbon::parse($params['month'] . '-01')->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                
                if ($endDate->isFuture()) {
                    $endDate = now()->endOfDay();
                }

                $dailyData = [];
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');
                    $dailyData[$dateStr] = [
                        'date' => $dateStr,
                        'start_stock' => 0,
                        'in' => 0,
                        'out' => 0,
                        'end_stock' => 0,
                    ];
                }

                $lastMovementBefore = StockMovement::where('product_id', $product->id)
                    ->whereNull('voided_at')
                    ->where('created_at', '<', $startDate)
                    ->latest('created_at')
                    ->latest('id')
                    ->first();

                $currentStock = $lastMovementBefore ? $lastMovementBefore->stock_after : 0;

                $movements = StockMovement::where('product_id', $product->id)
                    ->whereNull('voided_at')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->get();

                foreach ($dailyData as $dateStr => &$dayData) {
                    $dayData['start_stock'] = $currentStock;
                    
                    $dayMovements = $movements->filter(fn($m) => $m->created_at->format('Y-m-d') === $dateStr);
                    
                    foreach ($dayMovements as $m) {
                        if ($m->type === 'in') {
                            $dayData['in'] += $m->quantity;
                        } elseif ($m->type === 'out') {
                            $dayData['out'] += $m->quantity;
                        } elseif ($m->type === 'adjustment') {
                            if ($m->quantity > 0) $dayData['in'] += $m->quantity;
                            else $dayData['out'] += abs($m->quantity);
                        }
                        $currentStock = $m->stock_after;
                    }
                    
                    $dayData['end_stock'] = $currentStock;
                }

                return [
                    'product' => $product,
                    'month' => $startDate->format('F Y'),
                    'ledger' => array_reverse($dailyData),
                ];
        }

        return collect();
    }
}
