<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/functions.php');

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Récupérer tous les vehicules
$vehicules = getAllVehicules();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Véhicules</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h1>Gestion des véhicules</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Marque</th>
                    <th scope="col">Modèle</th>
                    <th scope="col">Année</th>
                    <th scope="col">Immatriculation</th>
                    <th scope="col">Etablissement</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicules as $vehicule) : ?>
                    <tr>
                        <td><?php echo $vehicule['id']; ?></td>
                        <td><?php echo $vehicule['marque']; ?></td>
                        <td><?php echo $vehicule['modele']; ?></td>
                        <td><?php echo $vehicule['annee']; ?></td>
                        <td><?php echo $vehicule['immatriculation']; ?></td>
                        <td><?php $etablissement = getEtablissementById($vehicule['etablissement_id']); echo $etablissement['nom']; ?></td>
                    <td>
                        <a href="edit_vehicles.php?id=<?= $vehicule['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="delete_vehicles.php?id=<?= $vehicule['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?');">Supprimer</a>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
    <a href="add_vehicles.php" class="btn btn-primary">Ajouter un véhicule</a>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
