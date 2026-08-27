<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromArray, WithHeadings, WithStyles, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // Add header
        $rows[] = ['FINANCIAL DASHBOARD REPORT'];
        $rows[] = ['Generated: ' . $this->data['exported_at']];
        $rows[] = ['Period: ' . $this->data['period']];
        $rows[] = ['Location: ' . ($this->data['location']->name ?? 'All Locations')];
        $rows[] = [];

        // Core Metrics
        $rows[] = ['CORE METRICS', '', 'Current', 'Previous', 'Growth'];
        $metrics = $this->data['core_metrics'];
        $compare = $this->data['compare_metrics'];

        $metricRows = [
            ['Total Sales', '$', $metrics['total_sales'], $compare['total_sales'],
                $compare['total_sales'] > 0 ? (($metrics['total_sales'] - $compare['total_sales']) / $compare['total_sales'] * 100) . '%' : 'N/A'],
            ['Net Sales', '$', $metrics['net_sales'], $compare['net_sales'],
                $compare['net_sales'] > 0 ? (($metrics['net_sales'] - $compare['net_sales']) / $compare['net_sales'] * 100) . '%' : 'N/A'],
            ['Gross Profit', '$', $metrics['gross_profit'], $compare['gross_profit'],
                $compare['gross_profit'] > 0 ? (($metrics['gross_profit'] - $compare['gross_profit']) / $compare['gross_profit'] * 100) . '%' : 'N/A'],
            ['Total Orders', '', $metrics['total_orders'], $compare['total_orders'],
                $compare['total_orders'] > 0 ? (($metrics['total_orders'] - $compare['total_orders']) / $compare['total_orders'] * 100) . '%' : 'N/A'],
            ['Avg Order Value', '$', $metrics['avg_order_value'], $compare['avg_order_value'],
                $compare['avg_order_value'] > 0 ? (($metrics['avg_order_value'] - $compare['avg_order_value']) / $compare['avg_order_value'] * 100) . '%' : 'N/A'],
            ['Collection Rate', '%', $metrics['collection_rate'], $compare['collection_rate'],
                number_format($metrics['collection_rate'] - $compare['collection_rate'], 1) . 'pp'],
            ['Refund Rate', '%', $metrics['refund_rate'], $compare['refund_rate'],
                number_format($metrics['refund_rate'] - $compare['refund_rate'], 1) . 'pp'],
        ];

        foreach ($metricRows as $row) {
            $rows[] = $row;
        }

        $rows[] = [];

        // Top Products
        $rows[] = ['TOP PRODUCTS BY REVENUE'];
        $rows[] = ['Product', 'Quantity', 'Revenue', 'Avg Cost', 'Profit', 'Margin %'];

        foreach ($this->data['product_analytics']['top_products'] as $product) {
            $rows[] = [
                $product->name,
                $product->total_quantity,
                $product->total_revenue,
                $product->avg_cost,
                $product->total_profit,
                $product->profit_margin
            ];
        }

        $rows[] = [];

        // Inventory Summary
        $rows[] = ['INVENTORY SUMMARY'];
        $inventory = $this->data['inventory_analytics'];
        $rows[] = ['Total Value', 'Total Units', 'Unique Products', 'Out of Stock', 'Low Stock', 'Turnover Rate'];
        $rows[] = [
            '$' . number_format($inventory['total_value'], 2),
            $inventory['total_units'],
            $inventory['unique_products'],
            $inventory['out_of_stock'],
            $inventory['low_stock'],
            number_format($inventory['turnover_rate'], 1) . 'x'
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['size' => 12]],
            3 => ['font' => ['size' => 12]],
            4 => ['font' => ['size' => 12]],
            6 => ['font' => ['bold' => true, 'size' => 14]],
            7 => ['font' => ['bold' => true]],
            15 => ['font' => ['bold' => true, 'size' => 14]],
            16 => ['font' => ['bold' => true]],
            24 => ['font' => ['bold' => true, 'size' => 14]],
            25 => ['font' => ['bold' => true]],
        ];
    }
}
