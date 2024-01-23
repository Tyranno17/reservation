<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Vérifier si l'ID du véhicule est fourni
if (!isset($_GET['id'])) {
    header("Location: vehicules.php");
    exit;
}

$vehicule_id = $_GET['id'];
$vehicule = getVehicleById($vehicule_id);

// Traiter le formulaire de mise à jour du véhicule
if (isset($_POST['submit'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $immatriculation = $_POST['immatriculation'];
    $etablissement_id = $_POST['etablissement_id'];

    if (updateVehicle($vehicule_id, $marque, $modele, $annee, $immatriculation, $etablissement_id)) {
        $_SESSION['success'] = "Le véhicule a été mis à jour avec succès.";
        header("Location: vehicules.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du véhicule. Veuillez réessayer.";
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <h1>Modifier un véhicule</h1>
    <form action="edit_vehicles.php?id=<?php echo $vehicule_id; ?>" method="post">
        <div class="mb-3">
            <label for="marque" class="form-label">Marque</label>
            <input type="text" class="form-control" id="marque" name="marque" value="<?php echo $vehicule['marque']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="modele" class="form-label">Modèle</label>
            <input type="text" class="form-control" id="modele" name="modele" value="<?php echo $vehicule['modele']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="annee" class="form-label">Année</label>
            <input type="number" class="form-control" id="annee" name="annee" value="<?php echo $vehicule['annee']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="immatriculation" class="form-label">Immatriculation</label>
            <input type="text" class="form-control" id="immatriculation" name="immatriculation" value="<?php echo $vehicule['immatriculation']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="etablissement_id" class="form-label">Etablissement</label>
            <select class="form-control" id="etablissement_id" name="etablissement_id">
                <?php
                $etablissements = getEtablissements();
                foreach ($etablissements as $etablissement) {
                    $selected = $vehicule['etablissement_id'] == $etablissement['id'] ? 'selected' : '';
                    echo "<option value='{$etablissement['id']}' {$selected}>{$etablissement['nom']}</option>";
                }
                ?>
                </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
                
                </div>
                <?php
                include 'footer.php';
                ?>
