<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Sales Report') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            color: #181c32;
            background: #f4f4f4;
            margin: 0;
            padding: 30px;
        }
        .report-box {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            padding: 32px;
        }
        .report-header {
            text-align: center;
            border-bottom: 2px dashed #e4e6ef;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .report-header h2 { margin: 0 0 4px; }
        .report-header .report-subtitle { font-size: 1.1rem; font-weight: bold; color: #009ef7; }
        .report-header .report-meta { color: #7e8299; font-size: 0.9rem; margin-top: 4px; }
        .report-filters {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        table.summary-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.summary-table td { padding: 8px 12px; border: 1px solid #e4e6ef; }
        table.summary-table .summary-label { color: #7e8299; font-weight: 600; width: 20%; }
        table.summary-table .summary-value { font-weight: 700; width: 30%; }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        table.data-table th, table.data-table td { border: 1px solid #e4e6ef; padding: 6px 8px; text-align: left; }
        table.data-table th { background: #f8f9fa; }
        .text-center { text-align: center; }
        .actions { text-align: center; margin-top: 24px; }
        .actions button, .actions a {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 6px;
            border: 1px solid #009ef7;
            background: #009ef7;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .actions a.back {
            background: #fff;
            color: #181c32;
            border-color: #e4e6ef;
            margin-left: 8px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .report-box { border: none; max-width: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="report-box">
        @include('sales-report.partials.report-content')

        <div class="actions">
            <button type="button" onclick="window.print()">{{ __('Print') }}</button>
            <a href="javascript:void(0)" onclick="window.close()" class="back">{{ __('Close') }}</a>
        </div>
    </div>
</body>
</html>
