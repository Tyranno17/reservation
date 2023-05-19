<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Récupérer l'utilisateur par email
    $user = getUserByEmail($email);

    if ($user) {
        // Vérifier le mot de passe
        if (password_verify($password, $user['mot_de_passe'])) {
            // Connecter l'utilisateur
            $_SESSION['utilisateur_id'] = $user['id'];
            $_SESSION['utilisateur_role'] = $user['role'];

            // Afficher le message de bienvenue selon le rôle de l'utilisateur
            if ($user['role'] == 'admin') {
                $_SESSION['success'] = "Bienvenue, vous êtes connecté en tant qu'administrateur.";
                header("Location: admin/dashboard.php");
            } else {
                $_SESSION['success'] = "Bienvenue, vous êtes connecté en tant qu'utilisateur.";
                header("Location: reservations.php");
            }

            exit;
        } else {
            $_SESSION['error'] = "Mot de passe incorrect. Veuillez réessayer.";
            header('Location: index.php#login?error=insertion_error');
            exit;
        }
    }
}
