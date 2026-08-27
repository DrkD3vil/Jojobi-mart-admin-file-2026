<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Generic single-sheet export: a report is a list of titled sections, each
 * with an optional column header row and its own data rows. Used for both
 * the financial dashboards and the Reports & Analytics exports so there's
 * one export class instead of one per report.
 */
class SectionsExport implements FromArray, ShouldAutoSize, WithStyles
{
    /** @var array<int, array{heading: string, columns?: array, rows: array}> */
    private array $sections;

    private array $boldRows = [];

    public function __construct(array $sections)
    {
        $this->sections = $sections;
    }

    public function array(): array
    {
        $rows = [];
        $line = 1;

        foreach ($this->sections as $section) {
            $rows[] = [$section['heading']];
            $this->boldRows[] = $line++;

            if (!empty($section['columns'])) {
                $rows[] = $section['columns'];
                $this->boldRows[] = $line++;
            }

            foreach ($section['rows'] as $row) {
                $rows[] = $row;
                $line++;
            }

            $rows[] = [];
            $line++;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ($this->boldRows as $rowNumber) {
            $styles[$rowNumber] = ['font' => ['bold' => true]];
        }

        return $styles;
    }
}
