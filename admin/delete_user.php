<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/functions.php');

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Récupérer l'ID de l'utilisateur à supprimer
if (isset($_GET['id'])) {
    $sAMAccountName = $_GET['id'];
} else {
    header("Location: utilisateurs.php");
    exit;
}

// Supprimer l'utilisateur
if (deleteUser($sAMAccountName)) {
    $_SESSION['success'] = "L'utilisateur a été supprimé avec succès.";
} else {
    $_SESSION['error'] = "Une erreur s'est produite lors de la suppression de l'utilisateur. Veuillez réessayer.";
}

header("Location: utilisateurs.php");
exit;
