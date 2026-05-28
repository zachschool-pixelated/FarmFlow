@extends('reports.pdf.layout')

@section('title', 'Current Inventory Report')

@section('content')
    <table>
        <thead>
            <tr>
                <!-- <th>Code</th> -->
                <th>Product Name</th>
                <th>Category</th>
                <th>Supplier</th>
                <!-- <th class="text-right">Price</th> -->
                <th class="text-right">Stock Qty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $product)
                <tr>
                    <!-- <td>{{ $product->product_code }}</td> -->
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                    <!-- <td class="text-right">₱{{ number_format($product->unit_price, 2) }}</td> -->
                    <td class="text-right">{{ $product->stock_quantity }} {{ $product->unit_of_measure }}</td>
                    <td>
                        @if($product->stock_quantity <= 0)
                            <span style="color: red;">Out of Stock</span>
                        @elseif($product->stock_quantity <= $product->reorder_level)
                            <span style="color: orange;">Low Stock</span>
                        @else
                            <span style="color: green;">In Stock</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if($data->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No inventory records found.</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
