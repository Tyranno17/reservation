<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Traiter le formulaire d'ajout de véhicule
if (isset($_POST['submit'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $immatriculation = $_POST['immatriculation'];
    $etablissement_id = $_POST['etablissement_id'];

    if (addVehicle($marque, $modele, $annee, $immatriculation, $etablissement_id)) {
        $_SESSION['success'] = "Le véhicule a été ajouté avec succès.";
        header("Location: vehicules.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de l'ajout du véhicule. Veuillez réessayer.";
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <h1>Ajouter un véhicule</h1>
    <form action="add_vehicles.php" method="post">
        <div class="mb-3">
            <label for="marque" class="form-label">Marque</label>
            <input type="text" class="form-control" id="marque" name="marque" required>
        </div>
        <div class="mb-3">
            <label for="modele" class="form-label">Modèle</label>
            <input type="text" class="form-control" id="modele" name="modele" required>
        </div>
        <div class="mb-3">
            <label for="annee" class="form-label">Année</label>
            <input type="number" class="form-control" id="annee" name="annee" required>
        </div>
        <div class="mb-3">
            <label for="immatriculation" class="form-label">Immatriculation</label>
            <input type="text" class="form-control" id="immatriculation" name="immatriculation" required>
        </div>
        <div class="mb-3">
            <label for="etablissement_id" class="form-label">Etablissement</label>
            <select class="form-control" id="etablissement_id" name="etablissement_id">
                <?php
                $etablissements = getEtablissements();
                foreach ($etablissements as $etablissement) {
                    echo "<option value='{$etablissement['id']}'>{$etablissement['nom']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<?php
include 'footer.php';
?>
