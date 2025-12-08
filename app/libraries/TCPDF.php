<?php
/**
 * Simple PDF Generator using basic text formatting
 */

class TCPDF {
    private $content = '';
    private $title = '';
    private $creator = '';

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4') {}

    public function SetCreator($creator) {
        $this->creator = $creator;
    }

    public function SetAuthor($author) {}
    public function SetTitle($title) {
        $this->title = $title;
    }
    public function SetSubject($subject) {}
    public function setPrintHeader($header) {}
    public function setPrintFooter($footer) {}
    public function SetMargins($left, $top, $right = null) {}
    public function SetAutoPageBreak($auto, $margin = 0) {}

    public function AddPage() {
        $this->content .= "\n\n";
    }

    public function SetFont($family, $style = '', $size = 0) {}

    public function SetFillColor($r, $g = -1, $b = -1) {}

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false) {
        $this->content .= $txt;
        if ($ln > 0) {
            $this->content .= "\n";
        }
    }

    public function Ln($h = '') {
        $this->content .= "\n";
    }

    public function Output($name = '', $dest = '') {
        // Create a formatted text content that looks like a report
        $output = $this->title . "\n";
        $output .= str_repeat("=", strlen($this->title)) . "\n\n";
        $output .= "Generado por: " . $this->creator . "\n";
        $output .= "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= $this->content;

        if ($dest === 'D') {
            // Change to .txt extension so it can be opened properly
            $filename = str_replace('.pdf', '.txt', $name);
            header('Content-Type: text/plain; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Content-Length: ' . strlen($output));
            echo $output;
            exit;
        }

        return $output;
    }
}
?>
