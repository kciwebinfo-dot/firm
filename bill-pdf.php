<?php
require_once __DIR__ . '/auth/auth_check.php';
require_once __DIR__ . '/config/billing_helpers.php';
require_once __DIR__ . '/lib/fpdf/fpdf.php';

$bill = find_bill_by_token($_GET['token'] ?? '');
if (!$bill) exit('Invalid bill.');
$stmt = $pdo->prepare("SELECT * FROM bill_items WHERE bill_id=? ORDER BY id");
$stmt->execute([$bill['id']]);
$items = $stmt->fetchAll();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,8,firm('firm_name','Tax Firm'),0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,firm('tagline',''),0,1);
$pdf->MultiCell(0,5,trim(firm('address').' '.firm('city').' '.firm('state').' '.firm('pincode')));
$pdf->Cell(0,6,'Mobile: '.firm('mobile').'  Email: '.firm('email').'  Website: '.firm('website'),0,1);
$pdf->Cell(0,6,'GSTIN: '.firm('gstin').'  PAN: '.firm('pan'),0,1);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Bill / Invoice',0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(95,6,'Bill No: '.$bill['bill_no'],0,0);
$pdf->Cell(95,6,'Bill Date: '.$bill['bill_date'],0,1);
$pdf->Ln(2);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,6,'Bill To',0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,$bill['client_name'].' '.$bill['trade_name'],0,1);
$pdf->Cell(0,6,'Mobile: '.$bill['mobile'].' PAN: '.$bill['client_pan'].' GSTIN: '.$bill['client_gstin'],0,1);
$pdf->MultiCell(0,5,trim($bill['client_address'].' '.$bill['client_city'].' '.$bill['client_state'].' '.$bill['client_pincode']));
$pdf->Ln(3);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,7,'Sr',0,0); $pdf->Cell(45,7,'Service / Work',0,0); $pdf->Cell(60,7,'Description',0,0); $pdf->Cell(15,7,'Qty',0,0); $pdf->Cell(25,7,'Rate',0,0); $pdf->Cell(30,7,'Amount',0,1);
$pdf->SetFont('Arial','',10);
$i=1; foreach($items as $it){ $pdf->Cell(10,7,$i++,0,0); $pdf->Cell(45,7,$it['service_name'],0,0); $pdf->Cell(60,7,substr((string)$it['description'],0,34),0,0); $pdf->Cell(15,7,$it['qty'],0,0); $pdf->Cell(25,7,money($it['rate']),0,0); $pdf->Cell(30,7,money($it['amount']),0,1); }
$pdf->Ln(4);
$summary = ['Subtotal' => $bill['subtotal'], 'Discount' => $bill['discount'], 'Taxable Amount' => $bill['taxable_amount'], 'Tax Amount' => $bill['tax_amount'], 'Grand Total' => $bill['grand_total'], 'Paid' => $bill['paid_amount'], 'Due' => $bill['due_amount']];
foreach($summary as $k=>$v){ $pdf->Cell(130,6,$k,0,0); $pdf->Cell(40,6,money($v),0,1); }
$pdf->Ln(3);
$pdf->MultiCell(0,5,'Notes: '.$bill['notes']);
$pdf->Ln(10);
$pdf->Cell(120,6,firm('footer_text','Thank you.'),0,0);
$pdf->Cell(60,6,'Authorized Signature',0,1);
$pdf->Output('I', $bill['bill_no'].'.pdf');
