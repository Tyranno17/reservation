<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si tous les champs sont remplis
    if (empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($confirm_password)) {
        header('Location: index.php#register?error=missing');
        exit;
    }

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        header('Location: index.php#register?error=password_mismatch');
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (addUser($prenom, $nom, $email, $hashed_password, 'utilisateur')) {
        $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        header('Location: index.php');
        exit;
    } else {
        header('Location: index.php#register?error=insertion_error');
        exit;
    }
}
?>