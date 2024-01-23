<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!checkUserLogged()) {
    header("Location: index.php");
    exit;
}

// Vérifier si toutes les données requises ont été soumises
if (isset($_POST['submit']) && isset($_POST['etablissement_id']) && isset($_POST['vehicule_id']) && isset($_POST['date_debut']) && isset($_POST['date_fin']) && isset($_POST['destination'])) {
    $sAMAccountName = (int)$_SESSION['sAMAccountName'];
    $etablissement_id = (int)$_POST['etablissement_id'];
    $vehicule_id = (int)$_POST['vehicule_id'];
    $date_debut = htmlspecialchars($_POST['date_debut']);
    $date_fin = htmlspecialchars($_POST['date_fin']);
    $destination = htmlspecialchars($_POST['destination']);

    // Vérification de la validité des champs de formulaire

    // Comparaison des dates et heures de début et de fin
    if ($date_fin < $date_debut) {
        $errors[] = "La date ou l'heure de fin doit être supérieure à la date ou l'heure de début.";
    } elseif ($date_fin == $date_debut) {
        $heure_debut = substr($date_debut, 11);
        $heure_fin = substr($date_fin, 11);
        if ($heure_fin <= $heure_debut) {
            $errors[] = "La date ou l'heure de fin doit être supérieure à la date ou l'heure de début.";
        }
    }

    if (!isset($errors)) {
        // Vérifier si une réservation similaire existe déjà
        if (!checkExistingReservation($sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination)) {
            // Vérifier la disponibilité du véhicule pour les dates choisies
            if (isVehicleAvailable($vehicule_id, $date_debut, $date_fin, $sAMAccountName)) {
                // Si le véhicule est disponible, insérer la réservation dans la base de données
                if (insertReservation($sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination)) {
                    $_SESSION['flash']['success'] = "Réservation effectuée avec succès.";
                } else {
                    echo "Erreur lors de l'insertion de la réservation."; // Affiche l'erreur
                    $_SESSION['flash']['danger'] = "Erreur lors de la réservation. Veuillez réessayer.";
                }
            } else {
                $_SESSION['flash']['warning'] = "Le véhicule n'est pas disponible pour les dates choisies.";
            }
        } else {
            $_SESSION['flash']['warning'] = "Une réservation similaire existe déjà.";
        }
    } else {
        $_SESSION['flash']['danger'] = implode("<br>", $errors);
    }
}

header("Location: reservations.php");
exit;
