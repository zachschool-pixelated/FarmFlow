@extends('reports.pdf.layout')

@section('title', 'Supplier Directory Report')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Supplier Name</th>
                <th>Status</th>
                <th>Primary Contact</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $supplier)
                <tr>
                    <td>{{ $supplier->supplier_code }}</td>
                    <td>
                        <strong>{{ $supplier->name }}</strong>
                        @if($supplier->is_blacklisted)
                            <br><span style="color: red; font-size: 9px;">(Blacklisted)</span>
                        @endif
                    </td>
                    <td>{{ $supplier->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $supplier->contact_person ?: 'N/A' }}</td>
                    <td>{{ $supplier->phone ?: 'N/A' }}</td>
                    <td>{{ $supplier->email ?: 'N/A' }}</td>
                </tr>
            @endforeach
            @if($data->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No suppliers found.</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
