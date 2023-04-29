<?php
// session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $utilisateur_id = $_POST['utilisateur_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if (insertReservation($utilisateur_id, $vehicule_id, $date_debut, $date_fin)) {
        header('Location: calendrier.php');
        exit;
    } else {
        // Vous pouvez définir un message d'erreur ici si vous le souhaitez.
        header('Location: calendrier.php');
        exit;
    }
}
?>