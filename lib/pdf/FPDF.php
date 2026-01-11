<?php
// FPDF Minimal Standalone - Versão simplificada para geração de PDF
class FPDF {
    private $pdf = '';
    private $objects = [];
    private $n = 0;
    private $offsets = [];
    private $buffer = '';
    private $pages = [];
    private $page = 0;
    private $w = 210;
    private $h = 297;
    private $wPt;
    private $hPt;
    private $x = 0;
    private $y = 0;
    private $lMargin = 10;
    private $tMargin = 10;
    private $rMargin = 10;
    private $bMargin = 10;
    private $cMargin = 0;
    private $lineWidth = 0.2;
    private $fontFamily = 'Arial';
    private $fontSize = 12;
    private $fontStyle = '';
    private $currentFont = [];
    private $fonts = [];
    private $fontSizePt = 12;
    private $textColor = [0, 0, 0];
    private $drawColor = [0, 0, 0];
    private $fillColor = [255, 255, 255];
    private $lastPageBreak = 0;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        if ($unit == 'mm') {
            $this->wPt = $this->w * 2.834645669;
            $this->hPt = $this->h * 2.834645669;
        }
        $this->addFont('Arial', '', 'helvetica');
        $this->addFont('Arial', 'B', 'helveticab');
        $this->addFont('Arial', 'I', 'helveticai');
        $this->addPage();
    }

    public function addPage() {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    public function setFont($family, $style = '', $size = 0) {
        $this->fontFamily = $family;
        $this->fontStyle = $style;
        if ($size > 0) {
            $this->fontSize = $size;
            $this->fontSizePt = $size * 2.834645669;
        }
    }

    public function setFontSize($size) {
        $this->fontSize = $size;
        $this->fontSizePt = $size * 2.834645669;
    }

    public function setTextColor($r, $g = null, $b = null) {
        if ($g === null) {
            $this->textColor = [$r, $r, $r];
        } else {
            $this->textColor = [$r, $g, $b];
        }
    }

    public function setDrawColor($r, $g = null, $b = null) {
        if ($g === null) {
            $this->drawColor = [$r, $r, $r];
        } else {
            $this->drawColor = [$r, $g, $b];
        }
    }

    public function setFillColor($r, $g = null, $b = null) {
        if ($g === null) {
            $this->fillColor = [$r, $r, $r];
        } else {
            $this->fillColor = [$r, $g, $b];
        }
    }

    public function setLineWidth($width) {
        $this->lineWidth = $width;
    }

    public function cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false) {
        $k = 2.834645669;
        $x = $this->x * $k;
        $y = ($this->hPt - $this->y * $k);
        $w = $w * $k;
        if ($h == 0) {
            $h = $this->fontSize * 1.5;
        } else {
            $h = $h * $k;
        }

        // Adicionar texto ao buffer
        if ($txt) {
            $this->buffer .= "BT /F1 {$this->fontSizePt} Tf {$x} {$y} Td ({$txt}) Tj ET\n";
        }

        if ($ln == 1) {
            $this->x = $this->lMargin;
            $this->y += $h / $k;
        } else {
            $this->x += $w / $k;
        }
    }

    public function multiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        $lines = explode("\n", $txt);
        foreach ($lines as $line) {
            $this->cell($w, $h, $line, $border, 1, $align, $fill);
        }
    }

    public function ln($h = null) {
        $this->x = $this->lMargin;
        if ($h !== null) {
            $this->y += $h;
        } else {
            $this->y += $this->fontSize * 1.5 / 2.834645669;
        }
    }

    public function output($dest = '', $name = '') {
        // Criar PDF básico
        $pdf = "%PDF-1.4\n";
        
        // Adicionar objetos
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->wPt} {$this->hPt}] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        
        // Stream de conteúdo
        $content = $this->buffer;
        $pdf .= "4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n";
        
        // Font
        $pdf .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        
        // Xref
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n";
        $pdf .= "0000000000 65535 f\n";
        $pdf .= "0000000009 00000 n\n";
        $pdf .= "0000000058 00000 n\n";
        $pdf .= "0000000115 00000 n\n";
        $pdf .= sprintf("%010d 00000 n\n", $xref - strlen("4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n"));
        $pdf .= sprintf("%010d 00000 n\n", $xref);
        
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        if ($dest == 'D') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            echo $pdf;
        } elseif ($dest == 'S') {
            return $pdf;
        } else {
            echo $pdf;
        }
    }

    private function addFont($family, $style, $name) {
        $key = strtolower($family) . $style;
        $this->fonts[$key] = ['name' => $name, 'type' => 'Type1'];
    }
}
?>
