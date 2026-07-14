# PDF Letterhead & Excel Export Enhancement Guide

## Overview
This guide provides instructions for implementing Perumda Tirta Musi letterhead in PDF reports and improving Excel export formatting.

## 1. PDF Letterhead Implementation

### Required Libraries
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

### PDF Helper Function
Create `app/Helpers/PdfHelper.php`:

```php
<?php
namespace App\Helpers;

class PdfHelper {
    public static function getLetterheadHtml() {
        return view('components.pdf-letterhead')->render();
    }

    public static function formatReportHeader($title, $description = '') {
        $html = '<div style="text-align: center; margin-bottom: 30px; border-bottom: 3px double #1e3a8a; padding-bottom: 20px;">';
        
        // Letterhead
        $html .= '<div style="margin-bottom: 15px;">';
        $html .= '<img src="' . public_path('assets/logo_perumda.png') . '" style="height: 50px; margin-bottom: 10px;">';
        $html .= '</div>';
        
        // Company Info
        $html .= '<div style="margin-bottom: 8px;">';
        $html .= '<h3 style="margin: 0; font-size: 16px; font-weight: bold; color: #1e3a8a;">PERUSAHAAN UMUM DAERAH TIRTA MUSI PALEMBANG</h3>';
        $html .= '</div>';
        
        $html .= '<div style="color: #475569; font-size: 12px; line-height: 1.5; margin-bottom: 8px;">';
        $html .= '<p style="margin: 0;">Jl. Rambutan Ujung No. 01, 30 Ilir, Ilir Barat II, 30144</p>';
        $html .= '<p style="margin: 0;">Telp. 0711 355089 - Website: www.perumdatirtamusi.co.id</p>';
        $html .= '<p style="margin: 0;">E-mail: sekretariat@perumdatirtamusi.co.id</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Report Title
        $html .= '<div style="margin-bottom: 20px;">';
        $html .= '<h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: bold; color: #0f172a;">' . $title . '</h2>';
        if ($description) {
            $html .= '<p style="margin: 0; font-size: 12px; color: #64748b;">' . $description . '</p>';
        }
        $html .= '</div>';
        
        return $html;
    }

    public static function formatReportFooter() {
        $html = '<div style="margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 20px;">';
        
        // Signature Section
        $html .= '<div style="display: flex; justify-content: space-between; margin-top: 50px; text-align: center;">';
        
        $html .= '<div style="width: 30%;">';
        $html .= '<p style="margin-bottom: 60px; font-size: 12px; color: #475569;">Disetujui oleh,</p>';
        $html .= '<p style="margin: 0; font-weight: bold; font-size: 12px;">Kepala Bagian</p>';
        $html .= '</div>';
        
        $html .= '<div style="width: 30%;">';
        $html .= '<p style="margin-bottom: 60px; font-size: 12px; color: #475569;">Diperiksa oleh,</p>';
        $html .= '<p style="margin: 0; font-weight: bold; font-size: 12px;">Teknisi Senior</p>';
        $html .= '</div>';
        
        $html .= '<div style="width: 30%;">';
        $html .= '<p style="margin-bottom: 60px; font-size: 12px; color: #475569;">Mengetahui,</p>';
        $html .= '<p style="margin: 0; font-weight: bold; font-size: 12px;">Kepala Perusahaan</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Report Date
        $html .= '<div style="margin-top: 30px; text-align: center; color: #64748b; font-size: 11px;">';
        $html .= '<p>Laporan ini digenerate pada: ' . now()->locale('id')->format('d F Y H:i') . '</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
}
```

### Usage in Controller

```php
<?php
namespace App\Http\Controllers;

use App\Helpers\PdfHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller {
    public function cetakPdf($jenis) {
        // Get report data
        $data = $this->getReportData($jenis);
        
        // Build PDF
        $pdf = PDF::loadHTML(
            PdfHelper::formatReportHeader($data['title'], $data['description']) .
            $this->buildReportTable($data['rows'], $data['columns']) .
            PdfHelper::formatReportFooter()
        );
        
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Laporan_' . $jenis . '_' . date('Y-m-d') . '.pdf');
    }

    private function buildReportTable($rows, $columns) {
        $html = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        
        // Header
        $html .= '<thead>';
        $html .= '<tr style="background-color: #1d4ed8; color: white;">';
        foreach ($columns as $col) {
            $html .= '<th style="padding: 12px; border: 1px solid #cbd5e1; text-align: left; font-weight: bold; font-size: 12px;">' . $col . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        
        // Body
        $html .= '<tbody>';
        $rowNum = 1;
        foreach ($rows as $row) {
            $bg = $rowNum % 2 == 0 ? '#f8fafc' : '#ffffff';
            $html .= '<tr style="background-color: ' . $bg . ';">';
            foreach ($row as $cell) {
                $html .= '<td style="padding: 10px; border: 1px solid #e2e8f0; font-size: 12px;">' . $cell . '</td>';
            }
            $html .= '</tr>';
            $rowNum++;
        }
        $html .= '</tbody>';
        
        $html .= '</table>';
        
        return $html;
    }
}
```

## 2. Excel Export Enhancement

### Create Excel Export Helper

Create `app/Exports/ReportExport.php`:

```php
<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportExport implements FromArray, WithHeadings, WithStyles {
    protected $data;
    protected $headings;
    protected $title;

    public function __construct($data, $headings, $title = 'Laporan') {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function array(): array {
        return $this->data;
    }

    public function headings(): array {
        return $this->headings;
    }

    public function styles(Worksheet $sheet) {
        // Add title row
        $sheet->insertRows(1, 3);
        $sheet->mergeCells('A1:' . chr(65 + count($this->headings) - 1) . '1');
        
        $titleCell = $sheet->getCell('A1');
        $titleCell->setValue('LAPORAN PREVENTIVE MAINTENANCE ASET - PERUMDA TIRTA MUSI');
        $titleCell->getStyle()->setFont(new Font([
            'bold' => true,
            'size' => 14,
            'color' => ['rgb' => '0f172a'],
        ]));
        $titleCell->getStyle()->setAlignment(new Alignment([
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ]));
        
        // Add company info
        $sheet->mergeCells('A2:' . chr(65 + count($this->headings) - 1) . '2');
        $infoCell = $sheet->getCell('A2');
        $infoCell->setValue('Perusahaan Umum Daerah Tirta Musi Palembang - ' . date('d F Y'));
        $infoCell->getStyle()->setAlignment(new Alignment([
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ]));
        $infoCell->getStyle()->setFont(new Font(['size' => 10, 'color' => ['rgb' => '475569']]));
        
        // Style header row (now at row 4)
        for ($i = 0; $i < count($this->headings); $i++) {
            $col = chr(65 + $i);
            $cell = $sheet->getCell($col . '4');
            $cell->getStyle()->setFill(new Fill([
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1d4ed8'],
            ]));
            $cell->getStyle()->setFont(new Font([
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'ffffff'],
            ]));
            $cell->getStyle()->setAlignment(new Alignment([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ]));
            $cell->getStyle()->setBorder(new Border([
                'allBorders' => new Border\BorderEdge([
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'cbd5e1'],
                ]),
            ]));
        }
        
        // Auto-size columns
        for ($i = 0; $i < count($this->headings); $i++) {
            $sheet->getColumnDimensionByColumn($i + 1)->setAutoSize(true);
        }
        
        // Style data rows
        $lastRow = count($this->data) + 4;
        for ($row = 5; $row <= $lastRow; $row++) {
            $bgColor = ($row % 2 == 0) ? 'f1f5f9' : 'ffffff';
            for ($i = 0; $i < count($this->headings); $i++) {
                $col = chr(65 + $i);
                $cell = $sheet->getCell($col . $row);
                $cell->getStyle()->setFill(new Fill([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ]));
                $cell->getStyle()->setBorder(new Border([
                    'allBorders' => new Border\BorderEdge([
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'e2e8f0'],
                    ]),
                ]));
                $cell->getStyle()->setAlignment(new Alignment([
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]));
            }
        }
        
        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(18);
        
        return [];
    }
}
```

### Usage in Controller

```php
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

public function exportExcel($jenis) {
    $data = $this->getReportData($jenis);
    
    return Excel::download(
        new ReportExport(
            $data['rows'],
            $data['columns'],
            'LAPORAN ' . strtoupper($jenis)
        ),
        'Laporan_' . $jenis . '_' . date('Y-m-d') . '.xlsx'
    );
}
```

## 3. Implementation Checklist

- [ ] Install DomPDF package
- [ ] Install Excel package
- [ ] Create PdfHelper.php
- [ ] Create ReportExport.php
- [ ] Update LaporanController methods
- [ ] Test PDF generation with letterhead
- [ ] Test Excel export with formatting
- [ ] Verify logo path in `public/assets/logo_perumda.png`
- [ ] Test responsive layout in PDF
- [ ] Verify footer signature areas print correctly

## 4. Design Standards

### PDF Layout
- **Page Size**: A4 (210 × 297 mm)
- **Margins**: 20mm all sides
- **Font**: Arial/Helvetica, 12px body, 10px table
- **Header**: Company logo (50px height) + company info
- **Separator**: Double line (3px) in company blue (#1e3a8a)
- **Footer**: Signature area with 3 columns

### Excel Export
- **Title**: Bold, 14px, centered
- **Company Info**: 10px, centered
- **Headers**: Blue background (#1d4ed8), white text, 11px
- **Data**: Alternating row colors (white, light gray)
- **Borders**: Thin, light gray (#cbd5e1)
- **Auto-fit**: Columns auto-size to content

## 5. Color Palette

- Primary Blue: #1e3a8a
- Dark Blue: #1d4ed8
- Text Dark: #0f172a
- Text Light: #475569
- Border: #e2e8f0
- Background Light: #f8fafc
- Background Lighter: #f1f5f9

## 6. Testing

Test the following scenarios:
- [ ] PDF with 1-100 rows
- [ ] PDF with special characters
- [ ] Excel with various data types (numbers, dates, text)
- [ ] Excel with long text (ensure wrapping)
- [ ] Print preview of PDF
- [ ] Mobile responsiveness (if applicable)

---

**Last Updated**: June 2026  
**Version**: 1.0
