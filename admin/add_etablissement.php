<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Traiter le formulaire d'ajout de etablissement
if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    if (addEtablissement($nom, $description)) {
        $_SESSION['success'] = "Le etablissement a été ajouté avec succès.";
        header("Location: etablissement.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de l'ajout du etablissement. Veuillez réessayer.";
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <h1>Ajouter un etablissement</h1>
    <form action="add_etablissement.php" method="post">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du etablissement</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" ></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<?php
include 'footer.php';
?>
