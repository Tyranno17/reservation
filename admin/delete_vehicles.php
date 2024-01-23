<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Récupérer l'ID du véhicule à supprimer
if (isset($_GET['id'])) {
    $vehicle_id = $_GET['id'];
} else {
    header("Location: vehicules.php");
    exit;
}

// Supprimer le véhicule en utilisant la fonction deleteVehicle()
if (deleteVehicle($vehicle_id)) {
    $_SESSION['success'] = "Le véhicule a été supprimé avec succès.";
} else {
    $_SESSION['error'] = "Une erreur s'est produite lors de la suppression du véhicule. Veuillez réessayer.";
}

header("Location: vehicules.php");
exit;
?>
