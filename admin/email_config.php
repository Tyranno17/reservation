<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}



// Récupérez les paramètres de configuration de l'email de la base de données
$sql = "SELECT * FROM email_config LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si des paramètres de configuration de l'email existent, utilisez-les pour définir les variables
    $row = $result->fetch_assoc();
    $host = $row['host'];
    $username = $row['username'];
    $password = $row['password'];
    $port = $row['port'];
} else {
    // Sinon, définissez les variables à une valeur vide
    $host = '';
    $username = '';
    $password = '';
    $port = '';
}

if (isset($_SESSION['email_config_success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['email_config_success'] . '</div>';
    unset($_SESSION['email_config_success']);
}

include 'header.php';
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuration SMTP</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="my-4">Configuration SMTP</h1>
        <form method="post" action="save_email_config.php">
            <div class="form-group">
                <label for="host" class="form-label">Hôte</label>
                <input type="text" class="form-control" id="host" name="host" value="<?php echo $host; ?>" required>
            </div>
            <div class="form-group">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
            </div>
            <div class="form-group">
                <label for="port" class="form-label">Port</label>
                <input type="number" class="form-control" id="port" name="port" value="<?php echo $port; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Enregistrer</button>
        </form>
    </div>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script> -->
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>

<?php include 'footer.php'; ?>