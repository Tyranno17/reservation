<?php

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est un administrateur
if (!checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery-3.6.4.min.js"></script>
</head>

<body>


    <div class="container">
        <h1>Tableau de bord</h1>
        <!-- Vous pouvez ajouter ici des éléments pour afficher des informations sur les utilisateurs, les réservations, les véhicules, etc. -->

        <!-- Exemple de formulaire pour ajouter des fonctionnalités au tableau de bord -->
        <form action="process_dashboard.php" method="post">
            <!-- Ajoutez des éléments de formulaire ici, comme des inputs, des textarea, etc. -->
            <button type="submit" name="submit" class="btn btn-primary">Soumettre</button>
        </form>
    </div>
    <div class="container mt-5">
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Reste du contenu -->
    </div>

    <?php include 'footer.php'; ?>


</body>

</html>