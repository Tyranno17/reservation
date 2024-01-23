<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est un administrateur
if (!checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Ici, vous pouvez ajouter des fonctionnalités pour traiter les requêtes POST du tableau de bord de l'administrateur.
// Par exemple, vous pouvez ajouter, modifier ou supprimer des éléments tels que des utilisateurs, des réservations, des véhicules, etc.
