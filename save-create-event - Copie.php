<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_debut'])->format('Y-m-d H:i:s');
    $date_fin = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_fin'])->format('Y-m-d H:i:s');
    $utilisateur_id = $_SESSION['utilisateur_id'];

    // Convertir les dates de début et de fin en objets DateTime
    $date_debut_obj = new DateTime($date_debut);
    $date_fin_obj = new DateTime($date_fin);

    // Vérifier si la durée de la réservation est d'au moins une heure
    if ($date_fin_obj->getTimestamp() - $date_debut_obj->getTimestamp() < 3600) {
        // Afficher un message d'erreur à l'utilisateur
        echo json_encode(['success' => false, 'message' => 'La réservation doit être d\'au moins une heure.']);
        exit;
    }

    // if (check_reservation($conn, $service_id, $vehicule_id, $date_debut, $date_fin, $utilisateur_id)) {
    if (check_reservation($conn, $service_id, $vehicule_id, $date_debut, $date_fin)) {
        echo json_encode(['success' => false, 'message' => 'La date et l\'horaire sont déjà pris pour ce véhicule.']);
    } else {

        $sql = "INSERT INTO reservation (service_id, vehicule_id, utilisateur_id, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $service_id, $vehicule_id, $utilisateur_id, $date_debut, $date_fin);
        $stmt->execute();

        //Préparez le sujet et le message de l'email
        $userEmail = "patrice.robert@escale-larochelle.com"; // Remplacez ceci par l'adresse e-mail de l'utilisateur
        $subject = "Confirmation de votre réservation";
        $message = "Votre réservation a été créée avec succès. Voici les détails de votre réservation : Service ID: $service_id, Véhicule ID: $vehicule_id, Date de début: $date_debut, Date de fin: $date_fin.";

        // Envoyez l'email à l'utilisateur
        sendEmail($userEmail, $subject, $message);

        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'La méthode de requête n\'est pas POST.']);
}
