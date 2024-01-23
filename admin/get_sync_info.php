<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

setlocale(LC_TIME, 'fr_FR.UTF-8');
$formatter = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::LONG,
    IntlDateFormatter::SHORT,
    'Europe/Paris',
    IntlDateFormatter::GREGORIAN
);

$stmt = $conn->prepare("SELECT last_sync, sync_status, users_synced FROM sync_info ORDER BY id DESC LIMIT 1");
$stmt->execute();
$syncInfo = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Formatage de la date si elle est disponible
$lastSyncFormatted = 'Non disponible';
if (isset($syncInfo['last_sync']) && $syncInfo['last_sync'] !== null) {
    $date = new DateTime($syncInfo['last_sync']);
    $lastSyncFormatted = $formatter->format($date);
}

// Construction de la réponse
$response = [
    'last_sync' => $lastSyncFormatted,
    'users_synced' => $syncInfo['users_synced'] ?? '0',
    'sync_status' => $syncInfo['sync_status'] ?? 'Non disponible'
];

echo json_encode($response);

/* // Connexion à la base de données
// ...
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';


$response = ['last_sync' => 'Non disponible', 'users_synced' => '0', 'sync_status' => 'Non disponible'];

$result = $conn->query("SELECT * FROM sync_info ORDER BY id DESC LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $response['last_sync'] = $row['last_sync'];
    $response['users_synced'] = $row['users_synced'];
    $response['sync_status'] = $row['sync_status'];
}

header('Content-Type: application/json');
echo json_encode($response); */
