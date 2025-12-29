<?php
header('Content-Type: application/json');

require_once '../dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // Generate available carpass IDs (CP001 to CP999)
    $availableCarpass = [];
    
    // Get used carpass IDs
    $usedQuery = "SELECT carpassid FROM vehicle WHERE carpassid IS NOT NULL AND carpassid != ''";
    $usedResult = $conn->query($usedQuery);
    $usedCarpass = [];
    
    if ($usedResult) {
        while ($row = $usedResult->fetch_assoc()) {
            $usedCarpass[] = $row['carpassid'];
        }
    }
    
    // Generate available carpass IDs
    for ($i = 1; $i <= 999; $i++) {
        $carpassId = 'CP' . str_pad($i, 3, '0', STR_PAD_LEFT);
        if (!in_array($carpassId, $usedCarpass)) {
            $availableCarpass[] = $carpassId;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $availableCarpass]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $db->closeConnection();
}
?>