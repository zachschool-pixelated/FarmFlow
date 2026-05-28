@extends('reports.pdf.layout')

@section('title', 'Daily Stock Ledger: ' . $data['product']->name)

@section('content')
    <div style="margin-bottom: 20px;">
        <p><strong>Product:</strong> {{ $data['product']->name }}</p>
        <p><strong>Month:</strong> {{ $data['month'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Starting Stock</th>
                <th class="text-right" style="color: green;">Units Added (In)</th>
                <th class="text-right" style="color: red;">Units Removed (Out)</th>
                <th class="text-right">Ending Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['ledger'] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y (l)') }}</td>
                    <td class="text-right">{{ number_format($day['start_stock']) }}</td>
                    <td class="text-right" style="color: green;">
                        {{ $day['in'] > 0 ? '+' . number_format($day['in']) : '-' }}
                    </td>
                    <td class="text-right" style="color: red;">
                        {{ $day['out'] > 0 ? '-' . number_format($day['out']) : '-' }}
                    </td>
                    <td class="text-right font-bold">{{ number_format($day['end_stock']) }}</td>
                </tr>
            @endforeach
            @if(empty($data['ledger']))
                <tr>
                    <td colspan="5" class="text-center">No ledger records found for this month.</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
