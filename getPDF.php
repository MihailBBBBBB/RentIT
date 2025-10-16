<?php
require('../fpdf/fpdf.php');

$dbPath = __DIR__ . '/dbh.inc.php';
if (!file_exists($dbPath)) {
    die("File dbh.inc.php not found at: $dbPath. Please ensure dbh.inc.php is in C:\xampp\htdocs\flatok\api/");
}
require_once 'dbh.inc.php';

global $pdo;

if (isset($_GET['list']) && $_GET['list'] === 'all') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservation");
        $stmt->execute();
        $reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($reservation);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Query error: ' . $e->getMessage()]);
        exit;
    }
}

$Resid = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($Resid <= 0) {
    die('Invalid Res ID');
}

try {
    $stmt = $pdo->prepare("SELECT 
    r.Res_id,
    r.Type,
    r.Res_start,
    r.Res_finish,
    r.User_id,
    r.Place_id,
    CONCAT(u.Name, ' ', u.Surname) AS reserved_by
FROM reservation r
LEFT JOIN Users u ON r.User_id = u.User_ID
WHERE r.Res_id = :Res_id");

    $stmt->bindParam(':Res_id', $Resid, PDO::PARAM_INT);
    $stmt->execute();
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die('Reservation not found');
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, 'Information about your reservation', 0, 1, 'C');
$pdf->Ln(10);

$pdf->Cell(0, 10, "Reservation ID: {$reservation['Res_id']}", 0, 1);
$pdf->Cell(0, 10, "Type: {$reservation['Type']}", 0, 1);
$pdf->Cell(0, 10, "Start Date: {$reservation['Res_start']}", 0, 1);
$pdf->Cell(0, 10, "Finish Date: {$reservation['Res_finish']}", 0, 1);
$pdf->Cell(0, 10, "User ID: {$reservation['User_id']}", 0, 1);
$pdf->Cell(0, 10, "Place ID: {$reservation['Place_id']}", 0, 1);
$pdf->Cell(0, 10, "Reserved By: {$reservation['reserved_by']}", 0, 1);

$pdf->Output("resservation_{$Resid}.pdf", 'D');
?>