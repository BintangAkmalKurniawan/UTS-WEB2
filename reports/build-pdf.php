<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

$root = dirname(__DIR__);
$reportsDir = __DIR__;
$screenshotsDir = $reportsDir . DIRECTORY_SEPARATOR . 'screenshots';
$outputPath = $reportsDir . DIRECTORY_SEPARATOR . 'laporan-screenshot.pdf';
$githubUrl = 'https://github.com/BintangAkmalKurniawan/UTS-WEB2.git';

$pages = [
    [
        'title' => 'Halaman Login',
        'description' => 'Form login admin aplikasi MyCampus.',
        'file' => '01-login.png',
    ],
    [
        'title' => 'Dashboard Admin',
        'description' => 'Ringkasan jumlah mahasiswa, jurusan, dan chart distribusi jurusan.',
        'file' => '02-dashboard.png',
    ],
    [
        'title' => 'CRUD Mahasiswa - Data Mahasiswa',
        'description' => 'Tabel data mahasiswa beserta filter, pagination, edit, dan hapus.',
        'file' => '03-crud-mahasiswa-data.png',
    ],
    [
        'title' => 'CRUD Mahasiswa - Tambah Mahasiswa',
        'description' => 'Form tambah data mahasiswa baru.',
        'file' => '04-crud-mahasiswa-tambah.png',
    ],
    [
        'title' => 'CRUD Jurusan',
        'description' => 'Form tambah/edit jurusan dan tabel daftar jurusan.',
        'file' => '05-crud-jurusan.png',
    ],
    [
        'title' => 'CRUD Mata Kuliah',
        'description' => 'Form tambah/edit mata kuliah dan tabel daftar mata kuliah.',
        'file' => '06-crud-matakuliah.png',
    ],
];

function pdfEscape(string $text): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function rgb(float $r, float $g, float $b): string
{
    return sprintf("%.3F %.3F %.3F rg\n", $r, $g, $b);
}

function textLine(string $text, float $x, float $y, int $size = 12, string $font = 'F1'): string
{
    return sprintf(
        "BT /%s %d Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
        $font,
        $size,
        $x,
        $y,
        pdfEscape($text)
    );
}

function wrapText(string $text, int $maxChars): array
{
    $words = preg_split('/\s+/', trim($text)) ?: [];
    $lines = [];
    $line = '';

    foreach ($words as $word) {
        $candidate = $line === '' ? $word : $line . ' ' . $word;
        if (strlen($candidate) > $maxChars && $line !== '') {
            $lines[] = $line;
            $line = $word;
        } else {
            $line = $candidate;
        }
    }

    if ($line !== '') {
        $lines[] = $line;
    }

    return $lines;
}

function imageToJpegData(string $path): array
{
    if (!extension_loaded('gd')) {
        throw new RuntimeException('Extension PHP GD tidak tersedia.');
    }

    if (!is_file($path)) {
        throw new RuntimeException("Screenshot tidak ditemukan: {$path}");
    }

    $source = imagecreatefrompng($path);
    if (!$source) {
        throw new RuntimeException("Gagal membaca screenshot: {$path}");
    }

    $width = imagesx($source);
    $height = imagesy($source);
    $canvas = imagecreatetruecolor($width, $height);
    if (!$canvas) {
        imagedestroy($source);
        throw new RuntimeException("Gagal membuat canvas untuk: {$path}");
    }

    imagealphablending($canvas, true);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
    imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

    ob_start();
    imagejpeg($canvas, null, 88);
    $jpeg = ob_get_clean();

    imagedestroy($source);
    imagedestroy($canvas);

    if ($jpeg === false || $jpeg === '') {
        throw new RuntimeException("Gagal mengubah screenshot ke JPEG: {$path}");
    }

    return [$jpeg, $width, $height];
}

class PdfBuilder
{
    private array $objects = [null];

    public function reserve(): int
    {
        $this->objects[] = null;
        return count($this->objects) - 1;
    }

    public function add(string $data): int
    {
        $this->objects[] = $data;
        return count($this->objects) - 1;
    }

    public function set(int $id, string $data): void
    {
        $this->objects[$id] = $data;
    }

    public function stream(string $data, string $dictionary = ''): string
    {
        $dictionary = trim($dictionary);
        $dictionary = $dictionary === '' ? '' : $dictionary . ' ';

        return "<< {$dictionary}/Length " . strlen($data) . " >>\nstream\n" . $data . "\nendstream";
    }

    public function output(int $catalogId): string
    {
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        $count = count($this->objects) - 1;

        for ($i = 1; $i <= $count; $i++) {
            if ($this->objects[$i] === null) {
                throw new RuntimeException("Object PDF {$i} belum diisi.");
            }

            $offsets[$i] = strlen($pdf);
            $pdf .= "{$i} 0 obj\n{$this->objects[$i]}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . ($count + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . ($count + 1) . " /Root {$catalogId} 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF\n";

        return $pdf;
    }
}

$pdf = new PdfBuilder();
$catalogId = $pdf->reserve();
$pagesId = $pdf->reserve();
$fontId = $pdf->reserve();
$boldFontId = $pdf->reserve();

$pdf->set($fontId, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>');
$pdf->set($boldFontId, '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>');

$pageWidth = 595.28;
$pageHeight = 841.89;
$margin = 40.0;
$pageIds = [];

$months = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];
$dateText = (int) date('j') . ' ' . $months[(int) date('n')] . ' ' . date('Y');

$cover = '';
$cover .= rgb(0.000, 0.067, 0.227);
$cover .= sprintf("%.2F %.2F %.2F %.2F re f\n", $margin, 790.0, $pageWidth - ($margin * 2), 7.0);
$cover .= rgb(0.322, 0.400, 0.694);
$cover .= textLine('Laporan UTS Web 2', $margin, 720, 12, 'F2');
$cover .= rgb(0.000, 0.067, 0.227);
$cover .= textLine('Laporan Screenshot', $margin, 672, 34, 'F2');
$cover .= textLine('Aplikasi MyCampus', $margin, 632, 34, 'F2');
$cover .= rgb(0.290, 0.330, 0.400);
$cover .= textLine('Tanggal pembuatan: ' . $dateText, $margin, 590, 12, 'F1');
$cover .= textLine('Dokumentasi halaman login, dashboard, dan fitur CRUD mahasiswa, jurusan, serta mata kuliah.', $margin, 568, 12, 'F1');
$cover .= rgb(0.000, 0.067, 0.227);
$cover .= textLine('Link GitHub: ' . $githubUrl, $margin, 544, 11, 'F1');

$y = 488.0;
foreach (['Halaman Login', 'Dashboard Admin', 'Data Mahasiswa', 'Tambah Mahasiswa', 'CRUD Jurusan', 'CRUD Mata Kuliah'] as $item) {
    $cover .= rgb(0.970, 0.977, 0.984);
    $cover .= sprintf("%.2F %.2F %.2F %.2F re f\n", $margin, $y - 12, 250.0, 30.0);
    $cover .= rgb(0.118, 0.161, 0.216);
    $cover .= textLine($item, $margin + 12, $y - 1, 11, 'F2');
    $y -= 42.0;
}

$coverContentId = $pdf->add($pdf->stream($cover));
$coverLinkId = $pdf->add("<< /Type /Annot /Subtype /Link /Rect [{$margin} 536 390 556] /Border [0 0 0] /A << /S /URI /URI (" . pdfEscape($githubUrl) . ") >> >>");
$coverPageId = $pdf->add("<< /Type /Page /Parent {$pagesId} 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 {$fontId} 0 R /F2 {$boldFontId} 0 R >> >> /Annots [{$coverLinkId} 0 R] /Contents {$coverContentId} 0 R >>");
$pageIds[] = $coverPageId;

foreach ($pages as $index => $page) {
    $imagePath = $screenshotsDir . DIRECTORY_SEPARATOR . $page['file'];
    [$jpeg, $imageWidth, $imageHeight] = imageToJpegData($imagePath);

    $imageId = $pdf->add(
        $pdf->stream(
            $jpeg,
            "/Type /XObject /Subtype /Image /Width {$imageWidth} /Height {$imageHeight} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode"
        )
    );

    $imageTop = 720.0;
    $availableWidth = $pageWidth - ($margin * 2);
    $availableHeight = $imageTop - 44.0;
    $scale = min($availableWidth / $imageWidth, $availableHeight / $imageHeight);
    $drawWidth = $imageWidth * $scale;
    $drawHeight = $imageHeight * $scale;
    $x = ($pageWidth - $drawWidth) / 2;
    $y = $imageTop - $drawHeight;

    $content = '';
    $content .= rgb(0.000, 0.067, 0.227);
    $content .= textLine(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT), $margin, 790, 14, 'F2');
    $content .= textLine($page['title'], $margin + 46, 792, 20, 'F2');
    $content .= rgb(0.357, 0.392, 0.447);

    $lineY = 770.0;
    foreach (wrapText($page['description'], 78) as $line) {
        $content .= textLine($line, $margin + 46, $lineY, 11, 'F1');
        $lineY -= 15.0;
    }

    $content .= rgb(0.850, 0.855, 0.875);
    $content .= sprintf("%.2F %.2F %.2F %.2F re f\n", $x - 1.0, $y - 1.0, $drawWidth + 2.0, $drawHeight + 2.0);
    $content .= "q\n";
    $content .= sprintf("%.4F 0 0 %.4F %.4F %.4F cm\n", $drawWidth, $drawHeight, $x, $y);
    $content .= "/Im1 Do\n";
    $content .= "Q\n";

    $contentId = $pdf->add($pdf->stream($content));
    $pageId = $pdf->add("<< /Type /Page /Parent {$pagesId} 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 {$fontId} 0 R /F2 {$boldFontId} 0 R >> /XObject << /Im1 {$imageId} 0 R >> >> /Contents {$contentId} 0 R >>");
    $pageIds[] = $pageId;
}

$kids = implode(' ', array_map(static fn (int $id): string => "{$id} 0 R", $pageIds));
$pdf->set($pagesId, "<< /Type /Pages /Kids [{$kids}] /Count " . count($pageIds) . " >>");
$pdf->set($catalogId, "<< /Type /Catalog /Pages {$pagesId} 0 R >>");

file_put_contents($outputPath, $pdf->output($catalogId));

echo "PDF dibuat: {$outputPath}\n";
