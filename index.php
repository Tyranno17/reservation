<!DOCTYPE html>
<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
    <style>
        .input-group-text {
            cursor: pointer;
        }
    </style>

</head>

<body>
    <div class="container mt-5">
        <h2> Bienvenue sur le site de réservation de véhicule de l'Association l'Escale</h2>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
            unset($_SESSION['error_message']);
        }

        if (isset($_SESSION['success_message'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
            unset($_SESSION['success_message']);
        }
        ?>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Connexion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Inscription</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <!-- Contenu de login.php -->
                <?php
                require_once 'includes/functions.php';
                $error = isset($_GET['error']) ? $_GET['error'] : null;
                ?>
                <h2 class="mt-5">Connexion</h2>
                <?php
                if ($error == 'missing') {
                    echo "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
                } elseif ($error == 'invalid') {
                    echo "<div class='alert alert-danger'>Identifiants incorrects. Veuillez réessayer.</div>";
                }
                ?>
                <form method="post" action="process_login.php">
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" id="password" class="form-control" name="password" required>
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="toggle-password"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Se connecter</button>
                </form>
                <!-- Fin du contenu de login.php -->
            </div>
            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                <!-- Contenu de register.php -->
                <?php
                require_once 'includes/functions.php';

                $error = isset($_GET['error']) ? $_GET['error'] : null;
                ?>
                <h2 class="mt-5">Inscription</h2>
                <?php
                if ($error == 'missing') {
                    echo "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
                } elseif ($error == 'password_mismatch') {
                    echo "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
                }
                ?>
                <form method="post" action="process_registration.php">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" id="password_inscription" class="form-control" name="password" required>
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="toggle-password-inscription"></i>
                            </span>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" class="form-control" name="confirm_password" required>
                                <span class="input-group-text">
                                    <i class="bi bi-eye" id="toggle-confirm-password"></i>
                                </span>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">S'inscrire</button>
                </form>
                <!-- Fin du contenu de register.php -->
            </div>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- <script src="js/scripts.js"></script> -->

    <script>
        $(document).ready(function() {
            $("#toggle-password").on("click", function() {
                let input = $("#password");
                let icon = $(this);
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    icon.removeClass("bi-eye").addClass("bi-eye-slash");
                } else {
                    input.attr("type", "password");
                    icon.removeClass("bi-eye-slash").addClass("bi-eye");
                }
            });

            $("#toggle-confirm-password").on("click", function() {
                let input = $("#confirm_password");
                let icon = $(this);
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    icon.removeClass("bi-eye").addClass("bi-eye-slash");
                } else {
                    input.attr("type", "password");
                    icon.removeClass("bi-eye-slash").addClass("bi-eye");
                }
            });

            $("#toggle-password-inscription").on("click", function() {
                let input = $("#password_inscription");
                let icon = $(this);
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                    icon.removeClass("bi-eye").addClass("bi-eye-slash");
                } else {
                    input.attr("type", "password");
                    icon.removeClass("bi-eye-slash").addClass("bi-eye");
                }
            });

            $('#myTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
</body>

</html>