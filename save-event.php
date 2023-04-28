<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';


if (isset($_POST['utilisateur_id']) && isset($_POST['vehicule_id']) && isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
    $utilisateur_id = $_POST['utilisateur_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Insérer la nouvelle réservation dans la base de données
    $result = insertReservation($utilisateur_id, $vehicule_id, $date_debut, $date_fin);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error']);
}
?>