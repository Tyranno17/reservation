<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Ajoutez cette ligne
    $port = $_POST['port'];

    // Chiffrement du mot de passe
    $method = "AES-128-CTR";
    $key = openssl_digest(php_uname(), 'MD5', TRUE);
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted_password = openssl_encrypt($password, $method, $key, 0, $iv) . "::" . bin2hex($iv);

    // Stockage du mot de passe chiffré dans la base de données
    $sql = "INSERT INTO email_config (id, host, username, password, port) VALUES (1, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE host = ?, username = ?, password = ?, port = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssi", $host, $username, $encrypted_password, $port, $host, $username, $encrypted_password, $port);
    $stmt->execute();
}
// Après l'enregistrement des paramètres
$_SESSION['email_config_success'] = 'Les paramètres de messagerie ont été enregistrés avec succès.';
header('Location: email_config.php');
exit;
