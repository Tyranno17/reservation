<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_debut'])->format('Y-m-d H:i:s');
    $date_fin = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_fin'])->format('Y-m-d H:i:s');
    // $utilisateur_id = $_SESSION['utilisateur_id'];

    // if (check_reservation($conn, $service_id, $vehicule_id, $date_debut, $date_fin, $utilisateur_id)) {
    //     echo json_encode(['success' => false, 'message' => 'La date et l\'horaire sont déjà pris pour ce véhicule.']);
    // } else {

    $sql = "UPDATE reservation SET service_id = ?, vehicule_id = ?, date_debut = ?, date_fin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $service_id, $vehicule_id, $date_debut, $date_fin, $reservation_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'La méthode de requête n\'est pas POST.']);
}
