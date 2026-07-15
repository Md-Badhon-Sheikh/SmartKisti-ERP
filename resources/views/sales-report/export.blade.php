<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Sales Report') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .report-header { text-align: center; margin-bottom: 12px; }
        .report-header h2 { margin: 0 0 2px; }
        .report-filters { margin-bottom: 12px; }
        table.summary-table, table.data-table { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
        table.summary-table td, table.data-table th, table.data-table td { border: 1px solid #999; padding: 4px 6px; }
        table.data-table th { background: #f2f2f2; font-weight: bold; }
        .summary-label { font-weight: bold; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    @include('sales-report.partials.report-content')
</body>
</html>
