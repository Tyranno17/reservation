<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    echo json_encode(['error' => 'Vous devez être connecté pour supprimer une réservation.']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['error' => 'ID de réservation manquant.']);
    exit;
}

$reservationId = $_POST['id'];
$currentUserId = $_SESSION['utilisateur_id'];

$stmt = $mysqli->prepare("DELETE FROM reservation WHERE id = ? AND utilisateur_id = ?");
$stmt->bind_param("ii", $reservationId, $currentUserId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Erreur lors de la suppression de la réservation.']);
}

$stmt->close();
$mysqli->close();
