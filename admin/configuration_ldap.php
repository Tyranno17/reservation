<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
define('ENCRYPTION_KEY', 'sn5vdq811512A/');
require_once '../includes/functions.php';


// Vérifier si l'utilisateur est un administrateur
if (!checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}


// Récupérer les configurations actuelles
$stmt = $conn->prepare("SELECT * FROM ldap_config ORDER BY id DESC LIMIT 1");
$stmt->execute();
$config = $stmt->get_result()->fetch_assoc();
$stmt->close();


// Récupération de la liste des utilisateurs
$stmt = $conn->prepare("SELECT * FROM utilisateur");
$stmt->execute();
$result = $stmt->get_result();
$utilisateurs = [];
while ($row = $result->fetch_assoc()) {
    $utilisateurs[] = $row;
}
$stmt->close();


// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ldap_server = $_POST['ldap_host'] ?? '';
    $ldap_port = $_POST['ldap_port'] ?? '';
    $ldap_user_dn = $_POST['ldap_user_dn'] ?? '';
    $ldap_password = $_POST['ldap_password'] ?? '';
    $ldap_base_dn = $_POST['ldap_base_dn'] ?? '';
    $ldap_bind = $_POST['ldap_bind'] ?? '';
    $ldap_filter = $_POST['ldap_filter'] ?? '';
    $ldap_field_login = $_POST['ldap_field_login'] ?? '';
    $ldap_sync_field = $_POST['ldap_sync_field'] ?? '';

    // Chiffrez les informations sensibles avant de les sauvegarder
    $ldap_user_dn_encrypted = encrypt($ldap_user_dn, ENCRYPTION_KEY);
    $ldap_password_encrypted = encrypt($ldap_password, ENCRYPTION_KEY);


    // Vérifiez quelle action a été demandée

    $message = ""; // Ajoutez cette ligne au début de votre script PHP
    /* $msg = ""; */ // Ajoutez cette ligne au début de votre script PHP

    $action = $_POST['action'] ?? '';

    if ($action === 'test') {
        // Code pour tester la connexion LDAP
        // ...
        $ldap_conn = ldap_connect($ldap_server, (int)$ldap_port);
        if ($ldap_conn) {
            $bind = @ldap_bind($ldap_conn, $ldap_user_dn, $ldap_password);
            if ($bind) {
                $message = "Connexion LDAP réussie !";
            }
        }

        // Code pour sauvegarder les paramètres LDAP
    } elseif ($action === 'save') {

        // Sauvegardez les paramètres LDAP de manière sécurisée (par exemple, dans une base de données, chiffrés)

        $ldap_conn = ldap_connect($ldap_server, (int)$ldap_port);
        if ($ldap_conn) {
            $bind = @ldap_bind($ldap_conn, $ldap_user_dn, $ldap_password);
            if ($bind) {

                $stmt = $conn->prepare("INSERT INTO ldap_config (ldap_host, ldap_port, ldap_user_dn, ldap_password, ldap_base_dn, ldap_bind, ldap_filter, ldap_field_login, ldap_sync_field) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE ldap_host = VALUES(ldap_host), ldap_port = VALUES(ldap_port), ldap_user_dn = VALUES(ldap_user_dn), ldap_password = VALUES(ldap_password), ldap_base_dn = VALUES(ldap_base_dn), ldap_bind = VALUES(ldap_bind), ldap_filter = VALUES(ldap_filter), ldap_field_login = VALUES(ldap_field_login), ldap_sync_field = VALUES(ldap_sync_field)");
                $stmt->bind_param("sisssisss", $ldap_server, $ldap_port, $ldap_user_dn_encrypted, $ldap_password_encrypted, $ldap_base_dn, $ldap_bind, $ldap_filter, $ldap_field_login, $ldap_sync_field);
                if ($stmt->execute()) {
                    $message .= " Les paramètres LDAP ont été sauvegardés avec succès dans la base de données.";
                } else {
                    $message .= " Erreur lors de la sauvegarde des paramètres LDAP dans la base de données.";
                }

                $stmt->close();
            } else {
                $message = "Échec de la connexion LDAP. Veuillez vérifier les paramètres.";
            }
        }
    }
}

?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.6.4.min.js"></script>

</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Configuration LDAP</h2>
        <?php if (isset($message)) { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>
        <form action="configuration_ldap.php" method="post">
            <div class="form-group row">
                <label for="ldap_host" class="col-sm-2 col-form-label">Hôte LDAP:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_host" name="ldap_host" value="<?php echo htmlspecialchars($config['ldap_host'] ?? ''); ?>" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_port" class="col-sm-2 col-form-label">Port LDAP:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_port" name="ldap_port" value="<?php echo htmlspecialchars($config['ldap_port'] ?? ''); ?>" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_user_dn" class="col-sm-2 col-form-label">DN Utilisateur:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_user_dn" name="ldap_user_dn" value="<?php echo htmlspecialchars(decrypt($config['ldap_user_dn'] ?? '', ENCRYPTION_KEY)); ?>" required></p>

                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_password" class="col-sm-2 col-form-label">Mot de passe:</label>
                <div class="col-sm-6">
                    <p><input type="password" class="form-control" id="ldap_password" name="ldap_password" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_base_dn" class="col-sm-2 col-form-label">Base DN:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_base_dn" name="ldap_base_dn" value="<?php echo htmlspecialchars($config['ldap_base_dn'] ?? ''); ?>" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_bind" class="col-sm-2 col-form-label">Utiliser bind:</label>
                <div class="col-sm-6">
                    <p><select class="form-control" id="ldap_bind" name="ldap_bind"></p>
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_filter" class="col-sm-2 col-form-label">Filtre de connexion:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_filter" name="ldap_filter" value="<?php echo htmlspecialchars($config['ldap_filter'] ?? ''); ?>" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_field_login" class="col-sm-2 col-form-label">Champ de l'identifiant:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_field_login" name="ldap_field_login" value="<?php echo htmlspecialchars($config['ldap_field_login'] ?? ''); ?>" required></p>
                </div>
            </div>
            <div class="form-group row">
                <label for="ldap_sync_field" class="col-sm-2 col-form-label">Champ de synchronisation:</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="ldap_sync_field" name="ldap_sync_field" value="<?php echo htmlspecialchars($config['ldap_sync_field'] ?? ''); ?>" required></p>

                </div>
            </div>

            <!-- Boutons séparés pour tester et sauvegarder -->
            <button type="submit" name="action" value="test" class="btn btn-secondary">Tester</button>
            <button type="submit" name="action" value="save" class="btn btn-primary">Sauvegarder la connexion</button>

        </form>
</body>
<?php include 'footer.php'; ?>

</html>