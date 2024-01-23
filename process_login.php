
<?php

require_once 'includes/db.php';
define('ENCRYPTION_KEY', 'sn5vdq811512A/');
require_once 'includes/functions.php';

session_start();

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Authentifiez l'utilisateur avec l'AD
    $ldapConfig = getLDAPConfig();
    $ldapConfig['ldap_user_dn'] = decrypt($ldapConfig['ldap_user_dn'], ENCRYPTION_KEY);
    $ldapConfig['ldap_password'] = decrypt($ldapConfig['ldap_password'], ENCRYPTION_KEY);

    $isAuthenticated = authenticateWithAD($username, $password, $ldapConfig);

    // Si l'authentification AD est réussie
    if ($isAuthenticated) {
        // Récupérer le rôle de l'utilisateur dans la BDD
        $userDetails = getUserRoleFromDB($conn, $username);

        if ($userDetails) {
            // Stocker les détails de l'utilisateur dans la session
            $_SESSION['utilisateur_role'] = $userDetails['role'];
            $_SESSION['sAMAccountName'] = $userDetails['sAMAccountName'];

            // Afficher le message de bienvenue selon le rôle de l'utilisateur
            if ($userDetails['role'] == 'admin') {
                $_SESSION['success'] = "Bienvenue, vous êtes connecté en tant qu'administrateur.";
                header("Location: admin/dashboard.php");
                //header("Location: phpinfo.php");
            } else {
                $_SESSION['success'] = "Bienvenue, vous êtes connecté en tant qu'utilisateur.";
                header("Location: reservations.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la récupération des détails de l'utilisateur.";
            header('Location: index.php#login?error=insertion_error');
            exit;
        }
    } else {
        $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
        header('Location: index.php#login?error=login_error');
        exit;
    }
}
?>
