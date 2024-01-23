<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: etablissement.php");
    exit;
}

$etablissement_id = $_GET['id'];
$etablissement = getEtablissementById($etablissement_id);

if (!$etablissement) {
    header("Location: etablissement.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    if (updateEtablissement($etablissement_id, $nom, $description)) {
        $_SESSION['success'] = "Le etablissement a été modifié avec succès.";
        header("Location: etablissement.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de la modification du etablissement. Veuillez réessayer.";
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <h1>Modifier un etablissement</h1>
    <form action="edit_etablissement.php?id=<?php echo $etablissement_id; ?>" method="post">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du etablissement</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $etablissement['nom']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $etablissement['description']; ?></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>

<?php
include 'footer.php';
?>
