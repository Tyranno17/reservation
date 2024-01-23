<!DOCTYPE html>
<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-icons.css">

</head>

<body>
    <?php
    // Récupérer tous les véhicules et tous les etablissements
    $vehicules = getAllVehicules();
    $etablissements = getAllEtablissements();

    // Vérifier si l'utilisateur est connecté
    if (!checkUserLogged()) {
        header("Location: index.php");
        exit;
    }

    if (isset($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $type => $message) {
            echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        }
        unset($_SESSION['flash']);
    }
    if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php
    include 'header.php';
    ?>

    <div class="container mt-5">

    </div>
    <div class="container mt-5">
        <h1>Mes réservations</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Date de début</th>
                    <th scope="col">Date de fin</th>
                    <th scope="col">Etablissement</th>
                    <th scope="col">Véhicule</th>
                    <th scope="col">Immatriculation</th>
                    <th scope="col">Destination</th>
                </tr>
            </thead>
            <tbody id="reservations-list">
                <!-- Les réservations seront insérées ici via JavaScript -->
            </tbody>
        </table>

    </div>
    <?php
    include 'footer.php';
    ?>
    <!-- Charger jQuery en premier -->
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
        // Appeler fetchReservations() au chargement de la page
        $(document).ready(function() {
            fetchReservations();

        });



        function fetchReservations() {
            $.ajax({
                url: 'fetch_reservations.php',
                type: 'GET',
                dataType: 'json',
                async: true,
                success: function(reservations) {
                    var reservationsHtml = '';

                    reservations.forEach(function(reservation) {
                        reservationsHtml += `
    <tr>
        <td>${reservation.id}</td>
        <td>${formatDateToFrench(reservation.date_debut)}</td>
        <td>${formatDateToFrench(reservation.date_fin)}</td>
        <td>${reservation.etablissement}</td>
        <td>${reservation.vehicule}</td>
        <td>${reservation.immatriculation}</td>
        <td>${reservation.destination}</td>
    </tr>
`;
                    });

                    $('#reservations-list').html(reservationsHtml);
                }
            });
        }

        function formatDateToFrench(date) {
            let tempDate = new Date(date);
            let day = tempDate.getDate().toString().padStart(2, '0');
            let month = (tempDate.getMonth() + 1).toString().padStart(2, '0');
            let year = tempDate.getFullYear();
            let hours = tempDate.getHours().toString().padStart(2, '0');
            let minutes = tempDate.getMinutes().toString().padStart(2, '0');

            return `${day}-${month}-${year} ${hours}:${minutes}`;
        }


        // Rafraîchir les réservations toutes les 5 secondes (5000 millisecondes)
        setInterval(fetchReservations, 5000);
    </script>
</body>

</html>