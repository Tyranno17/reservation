<?php
session_start();
require_once('../includes/db.php');
require_once('../includes/functions.php');

// Vérifier si l'utilisateur est connecté et est administrateur
if (!checkUserLogged() || !checkUserRole('admin')) {
    header("Location: ../index.php");
    exit;
}

// Traitez le formulaire d'ajout d'utilisateur
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (addUser($prenom, $nom, $email, $sAMAccountName, $role)) {
        $_SESSION['success'] = "L'utilisateur a été ajouté avec succès.";
        header("Location: utilisateurs.php");
        exit;
    } else {
        $_SESSION['error'] = "Une erreur s'est produite lors de l'ajout de l'utilisateur. Veuillez réessayer.";
    }
}

?>
<?php include 'header.php'; ?>

<div class="container mt-5">
    <h1>Ajouter un utilisateur</h1>
    <form action="add_user.php" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select class="form-select" id="role" name="role" required>
                <option value="user">Utilisateur</option>
                <option value="admin">Administrateur</option>
            </select>

        </div>
        <div class="mb-3">
            <label for="samaccountname" class="form-label">sAMAccountName</label>
            <input type="text" class="form-control" id="samaccountname" name="samaccountname" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<?php include 'footer.php'; ?>