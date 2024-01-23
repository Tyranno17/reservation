<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">

</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Administration</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="header.php">Accueil</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="calendrier.php">Calendrier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="utilisateurs.php">Utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservations.php">Réservations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vehicules.php">Véhicules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="etablissement.php">Etablissements</a>
                    </li>
                    <!--                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Configuration
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="configuration_ldap.php">Configuration LDAP</a></li>
                            <li><a class="dropdown-item" href="synchronisation_ldap.php">Synchronisation LDAP</a></li>
                            <li><a class="dropdown-item" href="configuration_smtp.php">Configuration SMTP</a></li>
                        </ul>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="configuration_ldap.php">Configuration LDAP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="synchronisation_ldap.php">Synchronisation LDAP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="configuration_smtp.php">Configuration SMTP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Deconnexion</a>
                    </li>
                </ul>

            </div>
        </div>
        </div>
    </nav>
    <script src="../js/bootstrap.min.js"></script>
    <!-- <script src="../js/custom.js"></script> -->
</body>

</html>