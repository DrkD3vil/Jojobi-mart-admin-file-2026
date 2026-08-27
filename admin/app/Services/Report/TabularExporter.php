<?php

namespace App\Services\Report;

use App\Exports\SectionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as HttpResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Turns a report -- a list of {heading, columns?, rows} sections -- into a
 * downloadable CSV, XLSX, or PDF. Shared by every export endpoint so a
 * report only has to be shaped once, not re-implemented per file format.
 */
class TabularExporter
{
    public static function csv(string $filename, array $sections): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($sections) {
            $out = fopen('php://output', 'w');

            foreach ($sections as $section) {
                fputcsv($out, [$section['heading']]);

                if (!empty($section['columns'])) {
                    fputcsv($out, $section['columns']);
                }

                foreach ($section['rows'] as $row) {
                    fputcsv($out, $row);
                }

                fputcsv($out, []);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public static function excel(string $filename, array $sections): BinaryFileResponse
    {
        return Excel::download(new SectionsExport($sections), "{$filename}.xlsx");
    }

    public static function pdf(string $filename, string $title, array $sections): HttpResponse
    {
        $pdf = Pdf::loadView('reports.pdf.summary', [
            'title' => $title,
            'sections' => $sections,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ]);

        return $pdf->download("{$filename}.pdf");
    }

    public static function respond(string $format, string $filename, string $title, array $sections)
    {
        return match ($format) {
            'xlsx' => self::excel($filename, $sections),
            'pdf' => self::pdf($filename, $title, $sections),
            default => self::csv($filename, $sections),
        };
    }
}
