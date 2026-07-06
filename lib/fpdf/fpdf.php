<?php
class FPDF
{
    protected $pages = [];
    protected $current = -1;
    protected $fontSize = 10;
    public $x = 10;
    public $y = 10;

    public function AddPage($orientation = 'P', $size = 'A4')
    {
        $this->pages[] = [];
        $this->current = count($this->pages) - 1;
        $this->x = 10;
        $this->y = 10;
    }

    public function SetFont($family, $style = '', $size = 10)
    {
        $this->fontSize = (int)$size;
    }

    public function SetXY($x, $y) { $this->x = $x; $this->y = $y; }
    public function SetX($x) { $this->x = $x; }
    public function GetY() { return $this->y; }
    public function Ln($h = null) { $this->y += $h ?: 6; $this->x = 10; }
    public function SetFillColor($r, $g = null, $b = null) {}
    public function SetTextColor($r, $g = null, $b = null) {}
    public function SetDrawColor($r, $g = null, $b = null) {}
    public function SetLineWidth($width) {}
    public function Rect($x, $y, $w, $h, $style = '') {}
    public function Line($x1, $y1, $x2, $y2) {}
    public function Image($file, $x = null, $y = null, $w = 0, $h = 0) {}

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $this->writeText($this->x, $this->y, (string)$txt, $this->fontSize);
        if ($ln > 0) {
            $this->Ln($h ?: 6);
        } else {
            $this->x += $w;
        }
    }

    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $lines = explode("\n", wordwrap((string)$txt, max(20, (int)($w / 2.4))));
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, $border, 1, $align, $fill);
        }
    }

    protected function writeText($x, $y, $txt, $size)
    {
        if ($this->current < 0) {
            $this->AddPage();
        }
        $this->pages[$this->current][] = [$x, $y, $txt, $size];
    }

    protected function esc($text)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string)$text);
    }

    public function Output($dest = 'I', $name = 'document.pdf')
    {
        $objects = [
            "<< /Type /Catalog /Pages 2 0 R >>",
            '',
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
        ];
        $kids = [];
        foreach ($this->pages as $i => $texts) {
            $stream = "BT\n/F1 10 Tf\n";
            foreach ($texts as [$x, $y, $txt, $size]) {
                $pdfY = 842 - ($y * 2.83465);
                $pdfX = $x * 2.83465;
                $stream .= "/F1 {$size} Tf 1 0 0 1 " . number_format($pdfX, 2, '.', '') . ' ' . number_format($pdfY, 2, '.', '') . " Tm (" . $this->esc($txt) . ") Tj\n";
            }
            $stream .= "ET";
            $contentNo = count($objects) + 1;
            $objects[] = "<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream";
            $pageNo = count($objects) + 1;
            $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents {$contentNo} 0 R >>";
            $kids[] = "{$pageNo} 0 R";
        }
        $objects[1] = "<< /Type /Pages /Kids [" . implode(' ', $kids) . "] /Count " . count($kids) . " >>";
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n{$obj}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string)$offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
        if ($dest === 'S') return $pdf;
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $name . '"');
        echo $pdf;
        exit;
    }
}
