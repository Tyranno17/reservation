<!DOCTYPE html>

<?
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>

<head>
<title>Reserve ta voiture !</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-icons.css">
    
  </head>
  <body>
  <div class="container-fluid p-5 bg-primary text-white text-center">
  <h1>Bienvenue sur le site de réservation de véhicule de l'Escale</h1>
  <p>Réserve ta voiture!</p> 
</div>

<div class="container mt-5">

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

  <div class="row">
    <div class="col-sm-4">
        
					<img src="images/logo-lescale.png" class="img-fluid" alt="...">
				</div>
        
		<div class="col-sm-4 text-center">
            
            <h1>Connectez-vous</h1>
          
            <form method="post" action="process_login.php">
  <div class="input-group mb-3">
    <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur" required>
  </div>
  
  <div class="input-group mb-3">
    <input type="password" class="form-control" name="password" placeholder="Mot de passe" id="password" required>
    <div class="input-group-append">
      <span class="input-group-text" onclick="togglePasswordVisibility()">
        <i class="bi bi-eye-slash" id="togglePassword"></i>
      </span>
    </div>
  </div>
  
  <input type="submit" class="w-50 btn btn-lg btn-primary" value="Se connecter">
</form>
</div>
<?php include 'footer.php'; ?>
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script>
    function togglePasswordVisibility() {
  var passwordInput = document.getElementById('password');
  var togglePasswordIcon = document.getElementById('togglePassword');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    togglePasswordIcon.classList.remove('bi-eye-slash');
    togglePasswordIcon.classList.add('bi-eye');
  } else {
    passwordInput.type = 'password';
    togglePasswordIcon.classList.remove('bi-eye');
    togglePasswordIcon.classList.add('bi-eye-slash');
  }
}
</script>

  </body>
</html>
