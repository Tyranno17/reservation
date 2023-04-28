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
    <link rel="stylesheet" href="css/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/flatpickr.min.css">
</head>

<body>
    <?php
    // Récupérer tous les véhicules et tous les services
    $vehicules = getAllVehicules();
    $services = getAllServices();

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
    if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php
    include 'header.php';
    ?>

    <div class="container mt-5">
        <h1>Réserver un véhicule</h1>
        <?php
        if (isset($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $type => $message) {
                echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
            }
            unset($_SESSION['flash']);
        }
        ?>
        <form action="process_reservation.php" method="post">
            <!-- ... -->
            <div class="mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select class="form-select" id="service_id" name="service_id" required>
                    <option value="">Sélectionnez un service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="vehicule_id" class="form-label">Véhicule</label>
                <select class="form-select" id="vehicule_id" name="vehicule_id" required>
                    <option value="">Sélectionnez un véhicule</option>
                    <?php foreach ($vehicules as $vehicule): ?>
                        <option value="<?php echo $vehicule['id']; ?>"
                            data-service-id="<?php echo $vehicule['service_id']; ?>"><?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' (' . $vehicule['immatriculation'] . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de début</label>
                <input type="text" id="date_debut" class="form-control" required>
                <input type="hidden" id="date_debut_hidden" name="date_debut">
            </div>
            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de fin</label>
                <input type="text" id="date_fin" class="form-control" required>
                <input type="hidden" id="date_fin_hidden" name="date_fin">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Réserver</button>
        </form>

    </div>
    <div class="container mt-5">
        <h6>Mes réservations</h6>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Date de début</th>
                    <th scope="col">Date de fin</th>
                    <th scope="col">Service</th>
                    <th scope="col">Véhicule</th>
                    <th scope="col">Immatriculation</th>
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
    <script src="js/scripts.js"></script>
    <!-- Flatpickr JS -->

    <script src="js/flatpickr.min.js"></script>
    <!-- <script src="js/flatpickr.fr.js"></script> -->
    <script>
        // Appeler fetchReservations() au chargement de la page
        $(document).ready(function () {
            fetchReservations();
            filterVehiculesByService();
        });
        $("#service_id").on("change", function () {
            let service_id = $(this).val();
            let date_debut = $("#date_debut_hidden").val();
            let date_fin = $("#date_fin_hidden").val();

            $("#vehicule_id").on("change", function () {
                let vehiculeImmatriculation = $(this).find(":selected").text().split('(')[1].split(')')[0];
                $("#immatriculation").val(vehiculeImmatriculation);
            });


            filterVehiculesByService(); // Ajoutez ceci pour mettre à jour la liste des véhicules en fonction du service sélectionné

            // Vérifiez si les dates de début et de fin sont renseignées avant de procéder au tri
            if (date_debut !== "" && date_fin !== "") {
                $.ajax({
                    //...
                    data: {
                        service_id: service_id,
                        date_debut: date_debut,
                        date_fin: date_fin
                    },
                    //...
                });
            }
        });

        $("#date_debut").flatpickr({
            locale: "fr",
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            minTime: "07:00",
            maxTime: "18:00",
            minuteIncrement: 30,
            onChange: function (selectedDates, dateStr, instance) { // Ajoutez une virgule ici
                // Met à jour le champ caché avec la date au format Y-m-d H:i
                $("#date_debut_hidden").val(instance.formatDate(selectedDates[0], "Y-m-d H:i"));
            }
        });

        $("#date_fin").flatpickr({
            locale: "fr",
            dateFormat: "d-m-Y H:i",
            enableTime: true,
            time_24hr: true,
            minTime: "07:00",
            maxTime: "18:00",
            minuteIncrement: 30,
            onChange: function (selectedDates, dateStr, instance) { // Ajoutez une virgule ici
                // Met à jour le champ caché avec la date au format Y-m-d H:i
                $("#date_fin_hidden").val(instance.formatDate(selectedDates[0], "Y-m-d H:i"));
            }
        });




        function filterVehiculesByService() {
            let service_id = $('#service_id').val();
            $('#vehicule_id option').each(function () {
                let optionServiceId = $(this).data('service-id');
                if (optionServiceId === undefined) return;
                if (service_id === optionServiceId.toString()) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#vehicule_id').val(''); // Réinitialiser la sélection du véhicule
        }

        function fetchReservations() {
            $.ajax({
                url: 'fetch_reservations.php',
                type: 'GET',
                dataType: 'json',
                success: function (reservations) {
                    var reservationsHtml = '';

                    reservations.forEach(function (reservation) {
                        reservationsHtml += `
    <tr>
        <td>${reservation.id}</td>
        <td>${formatDateToFrench(reservation.date_debut)}</td>
        <td>${formatDateToFrench(reservation.date_fin)}</td>
        <td>${reservation.service}</td>
        <td>${reservation.vehicule}</td>
        <td>${reservation.immatriculation}</td>
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