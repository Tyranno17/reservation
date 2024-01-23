<?php
require_once 'config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
// Définir l'encodage de caractères pour la connexion
mysqli_set_charset($mysqli, 'utf8mb4'); 
// Connexion à la base de données

