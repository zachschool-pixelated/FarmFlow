<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FarmFlow Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #166534;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>FarmFlow</h1>
        <p>@yield('title')</p>
        <p>Generated on {{ now()->format('F j, Y, g:i a') }}</p>
    </div>

    @yield('content')

    <div class="footer">
        FarmFlow Management System &copy; {{ now()->year }}
    </div>

</body>
</html>
