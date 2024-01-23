<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!checkUserLogged()) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Récupérer l'ID de l'utilisateur en cours à partir de la session
$sAMAccountName = getUserIdFromSession();

// Récupérer les réservations de l'utilisateur
if ($sAMAccountName !== false) {
    $reservations = getReservationsByUserId($sAMAccountName);

    echo json_encode($reservations);
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Bad Request"]);
}
