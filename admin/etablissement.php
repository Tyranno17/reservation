<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

$etablissements = getAllEtablissements();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etablissements</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
     <h1>Gestion des etablissements</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Description</th>
                </tr>
            </thead>
            <tbody>
                <!-- La variable "$etablissement" correspond au nom de la table dans la base de données -->
                <?php foreach ($etablissements as $etablissement): ?>  
                    <tr>
                        <td><?php echo $etablissement['id']; ?></td>
                        <td><?php echo $etablissement['nom']; ?></td>
                        <td><?php echo $etablissement['description']; ?></td>
                    <td>
                        <a href="edit_etablissement.php?id=<?= $etablissement['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete_etablissement.php?id=<?= $etablissement['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce etablissement?');">Supprimer</a>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <a href="add_etablissement.php" class="btn btn-primary">Ajouter un etablissement</a>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
