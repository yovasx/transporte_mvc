<?php
/**
 * Simple XLSX Generator
 * Simplified version for basic Excel generation
 */

class SimpleXLSXGen {
    private $rows = [];
    private $sheetName = 'Sheet1';

    public function __construct() {
        $this->rows = [];
    }

    public function addRow($row) {
        $this->rows[] = $row;
    }

    public function addRows($rows) {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    public function saveAs($filename) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        foreach ($this->rows as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    public function downloadAs($filename) {
        $this->saveAs($filename);
    }
}
?>
