<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Calendrier</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/flatpickr.min.css">
        <script src="js/jquery-3.6.4.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/index.global.min.js"></script>
        <script src="js/moment.min.js"></script>
        <script src="js/fr.min.js"></script>
        <script src="js/flatpickr.min.js"></script>
        <script src="js/fr.js"></script>
    </head>

<body>
    <div class="container">
        <h1>Calendrier des réservations</h1>
        <a href="create_reservation.php" class="btn btn-primary">Créer une nouvelle réservation</a>

        <!-- Calendrier -->
        <div id="calendar"></div>
        <?php
        // Récupérer les réservations de la base de données
        $sql = "SELECT * FROM reservations";
        $result = $conn->query($sql);
        $reservations = $result->fetch_all(MYSQLI_ASSOC);

        // Convertir les réservations en événements pour FullCalendar
        $events = [];
        foreach ($reservations as $reservation) {
            $events[] = [
                'id' => $reservation['id'],
                'title' => 'Réservation ' . $reservation['id'],
                'start' => $reservation['date_debut'],
                'end' => $reservation['date_fin'],
            ];
        }

        $calendar_events = json_encode($events);
        ?>
    </div>

    <script>
        $(document).ready(function () {
            // Initialisez FullCalendar
            $('#calendar').fullCalendar({
                events: <?php echo $calendar_events; ?>,
                eventClick: function (event) {
                    // Ouvrez la page view_reservation.php avec l'ID de la réservation en tant que paramètre GET
                    window.location.href = 'view_reservation.php?id=' + event.id;
                },
                selectable: true,
                select: function (start, end) {
                    // Ouvrez la page create_reservation.php avec les dates de début et de fin en tant que paramètres GET
                    window.location.href = 'create_reservation.php?start=' + start.format() + '&end=' + end.format();
                },
            });
        });
    </script>
</body>

</html>