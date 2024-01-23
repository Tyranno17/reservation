<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
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
        <h2 class="text-center">Configuration SMTP</h2>
        <form method="post" action="save_email_config.php">

            <div class="form-group row">
                <label for="host" class="col-sm-2 col-form-label">Hôte</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="host" name="host" value="<?php echo htmlspecialchars($host['host'] ?? ''); ?>" required></p>

                </div>
            </div>

            <div class="form-group row">
                <label for="username" class="col-sm-2 col-form-label">Nom d'utilisateur</label>
                <div class="col-sm-6">
                    <p><input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username['username'] ?? ''); ?>" required></p>

                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-sm-2 col-form-labell">Mot de passe</label>
                <div class="col-sm-6">
                    <p><input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password['password'] ?? ''); ?>" required></p>

                </div>
            </div>

            <div class="form-group row">
                <label for="port" class="col-sm-2 col-form-label">Port</label>
                <div class="col-sm-6">
                    <p><input type="number" class="form-control" id="port" name="port" value="<?php echo htmlspecialchars($port['port'] ?? ''); ?>" required></p>

                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Enregistrer</button>

        </form>
</body>
<?php include 'footer.php'; ?>

</html>