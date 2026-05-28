@extends('reports.pdf.layout')

@section('title', 'Recent Stock Movements Report')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Type</th>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th>Notes</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $movement->reference_number ?: 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $movement->type)) }}</td>
                    <td>{{ $movement->product->name ?? 'N/A' }}</td>
                    <td class="text-right">
                        @if(in_array($movement->type, ['stock_in', 'return']))
                            +{{ $movement->quantity }}
                        @elseif(in_array($movement->type, ['stock_out', 'spoilage', 'damage']))
                            -{{ $movement->quantity }}
                        @else
                            {{ $movement->quantity }}
                        @endif
                    </td>
                    <td>{{ $movement->notes ?: '-' }}</td>
                    <td>{{ $movement->user->name ?? 'System' }}</td>
                </tr>
            @endforeach
            @if($data->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No recent stock movements found.</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
