<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'email_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etablissement_id = $_POST['etablissement_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $destination = $_POST['destination'];
    $date_debut = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_debut'])->format('Y-m-d H:i:s');
    $date_fin = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_fin'])->format('Y-m-d H:i:s');
    $sAMAccountName = $_SESSION['sAMAccountName'];
 
    
    // Convertir les dates de début et de fin en objets DateTime
    $date_debut_obj = new DateTime($date_debut);
    $date_fin_obj = new DateTime($date_fin);

    // Vérifier si la durée de la réservation est d'au moins une heure
    if ($date_fin_obj->getTimestamp() - $date_debut_obj->getTimestamp() < 3600) {
        // Afficher un message d'erreur à l'utilisateur
        echo json_encode(['success' => false, 'message' => 'La réservation doit être d\'au moins une heure.']);
        exit;
    }


    if (check_reservation($conn, $etablissement_id, $vehicule_id, $date_debut, $date_fin)) {
        echo json_encode(['success' => false, 'message' => 'La date et l\'horaire sont déjà pris pour ce véhicule.']);
    } else {

        $sql = "INSERT INTO reservation (etablissement_id, vehicule_id, sAMAccountName, date_debut, date_fin, destination) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $etablissement_id, $vehicule_id, $sAMAccountName, $date_debut, $date_fin, $destination);
        $stmt->execute();

        // Récupérer les détails du etablissement et du véhicule
        $etablissement = getEtablissementById($etablissement_id);
        $vehicule = getVehicleById($vehicule_id);

        // Créer un formateur de date en français
        $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
        $formatter->setPattern('d MMMM yyyy à HH:mm');

        // Formater les dates en français
        $date_debut_fr = $formatter->format($date_debut_obj);
        $date_fin_fr = $formatter->format($date_fin_obj);


        //Préparez le sujet et le message de l'email
        $userEmail = getEmailFromDB($conn, $_SESSION['sAMAccountName']);
        $subject = 'Confirmation de votre réservation';
        $message = "Votre réservation a été créée avec succès. Voici les détails de vôtre réservation :<br><br>"
            . "Etablissement: {$etablissement['nom']}<br>"
            . "Véhicule: {$vehicule['modele']} - {$vehicule['immatriculation']}<br>"
            . "Date de début: $date_debut_fr<br>"
            . "Date de fin: $date_fin_fr<br>"
            . "Destination: $destination";

        // Envoyez l'email à l'utilisateur
        sendEmail($userEmail, $subject, $message);


        // echo json_encode(['success' => true]);
        echo json_encode(['success' => true, 'message' => 'Votre réservation a été créée avec succès. Un email a été envoyé']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'La méthode de requête n\'est pas POST.']);
}
