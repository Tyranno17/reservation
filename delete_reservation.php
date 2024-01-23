<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'email_functions.php'; // Assurez-vous que ce chemin est correct

if (!isset($_SESSION['sAMAccountName'])) {
    echo json_encode(['error' => 'Vous devez être connecté pour supprimer une réservation.']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['error' => 'ID de réservation manquant.']);
    exit;
}

$reservationId = $_POST['id'];
$currentUserId = $_SESSION['sAMAccountName'];

// Récupérer les informations de réservation avant de les supprimer
$stmt = $mysqli->prepare("SELECT reservation.*, etablissement.nom as etablissement_nom, vehicule.marque as vehicule_marque, vehicule.modele as vehicule_modele, vehicule.immatriculation as vehicule_immatriculation FROM reservation 
    INNER JOIN etablissement ON reservation.etablissement_id = etablissement.id 
    INNER JOIN vehicule ON reservation.vehicule_id = vehicule.id 
    WHERE reservation.id = ? AND reservation.sAMAccountName = ?");
$stmt->bind_param("is", $reservationId, $currentUserId);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();

if ($reservation) {
    // Préparer le sujet et le message de l'e-mail
    $userEmail = getEmailFromDB($conn, $_SESSION['sAMAccountName']);
    $subject = 'Annulation de votre réservation';

    // Formater les dates en français
    $date_debut_obj = new DateTime($reservation['date_debut']);
    $date_fin_obj = new DateTime($reservation['date_fin']);

    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
    $formatter->setPattern('d MMMM yyyy à HH:mm');

    $date_debut = $formatter->format($date_debut_obj);
    $date_fin = $formatter->format($date_fin_obj);

    $message = "Votre réservation a été supprimée. Voici les détails de la réservation supprimée : Etablissement: {$reservation['etablissement_nom']}, Véhicule: {$reservation['vehicule_marque']} {$reservation['vehicule_modele']} - {$reservation['vehicule_immatriculation']}, Date de début: $date_debut, Date de fin: $date_fin.";

    // Envoyer l'e-mail à l'utilisateur
    sendEmail($userEmail, $subject, $message);

    // Supprimer la réservation
    $stmt = $mysqli->prepare("DELETE FROM reservation WHERE id = ? AND sAMAccountName = ?");
    $stmt->bind_param("is", $reservationId, $currentUserId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Votre réservation a été supprimée. Un email a été envoyé']);
    } else {
        echo json_encode(['error' => 'Erreur lors de la suppression de la réservation.']);
    }
} else {
    echo json_encode(['error' => 'Réservation non trouvée.']);
}

$stmt->close();
$mysqli->close();
