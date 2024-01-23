<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Récupérer l'ID de l'utilisateur à modifier
if (isset($_GET['id'])) {
    $sAMAccountName = $_GET['id'];
} else {
    header("Location: utilisateurs.php");
    exit;
}

// Récupérer les informations de l'utilisateur à modifier
$user = getUserById($sAMAccountName);

// Traiter le formulaire de modification de l'utilisateur
if (isset($_POST['submit'])) {
    $sAMAccountName = $_POST['sAMAccountName'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (updateUser($sAMAccountName, $prenom, $nom, $email, $password, $role)) {
        $_SESSION['success'] = "L'utilisateur a été modifié avec succès.";
        header("Location: utilisateurs.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de la modification de l'utilisateur. Veuillez réessayer.";
    }
}

?>
<?php include 'header.php'; ?>

<div class="container mt-5">
    <h1>Modifier un utilisateur</h1>
    <form action="edit_user.php?id=<?= $sAMAccountName ?>" method="post">
        <div class="mb-3">
            <label for="prenom" class="form-label">Prenom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $user['prenom'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= $user['nom'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="sAMAccountName" class="form-label">sAMAccountName</label>
            <input type="text" class="form-control" id="sAMAccountName" name="prenom" value="<?= $user['sAMAccountName'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select class="form-select" id="role" name="role" required>
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Modifier</button>
    </form>
</div>

<?php include 'footer.php'; ?>