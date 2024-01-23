<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Récupérer l'ID du etablissement à supprimer
if (isset($_GET['id'])) {
    $etablissement_id = $_GET['id'];
} else {
    header("Location: etablissement.php");
    exit;
}

// Supprimer le etablissement de la base de données en utilisant une fonction, par exemple deleteEtablissement()
if (deleteEtablissement($etablissement_id)) {
    $_SESSION['success'] = "Le etablissement a été supprimé avec succès.";
} else {
    $_SESSION['error'] = "Une erreur s'est produite lors de la suppression du etablissement. Veuillez réessayer.";
}

header("Location: etablissement.php");
exit;
?>
