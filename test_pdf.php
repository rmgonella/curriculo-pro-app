<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

$pdf = new Dompdf();
$pdf->loadHtml('<h1>DOMPDF funcionando perfeitamente 🚀</h1>');
$pdf->render();

header('Content-Type: application/pdf');
echo $pdf->output();

