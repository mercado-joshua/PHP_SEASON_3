<?php
// include library
include('TCPDF/tcpdf.php');
include('connect.php');

// make tcpdf object
$pdf = new TCPDF('P', 'mm', 'A4');

// remove default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// add page
$pdf->AddPage();

## add content
// add cell
// $pdf->Cell(190, 10, "this is a cell", 1, 1, 'C');

// using HTML cell
// $pdf->WriteHTMLCell(190, 0, '', '', "<h1>this is a html cell</h1>", 1, 1);
// output
$pdf->SetFont('Helvetica', '', 14);
$pdf->Cell(190, 10, "this is a cell", 1, 1, 'C');
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(190, 5, "Student list", 1, 1, 'C');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(30, 5, "Class", 1);
$pdf->Cell(160, 5, ": programming 101", 1);
$pdf->Ln();
$pdf->Cell(30, 5, "Teacher Name", 1);
$pdf->Cell(160, 5, ": Prof. John Smith", 1);
$pdf->Ln();
$pdf->Ln(2);

// make the table

// ** WARNING: HTML takes more PROCESSING POWER than cell
$html = "<table border='1'>";
$html .= "<tr>
    <th>Product Name</th>
    <th>Price</th>
</tr>";

$query = mysqli_query($connect, "SELECT * FROM `product`");
while($row = mysqli_fetch_assoc($query)) {
    $product = $row["product"];
    $price = $row["price"];

    $html .= "<tr>
        <th>".ucfirst($product)."</th>
        <th>"."P ".number_format($price).".00"."</th>
    </tr>";
}

$html .= "</table>";

$pdf->WriteHTMLCell(192, 0, 9, '', $html, 1);
$pdf->Output();