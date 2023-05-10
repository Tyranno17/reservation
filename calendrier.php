<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Récupérer les événements depuis la base de données
$query = "SELECT r.id, r.date_debut, r.date_fin, u.prenom, u.nom, v.marque, v.modele, v.immatriculation, s.nom
                        FROM reservation r
                        INNER JOIN utilisateur u ON r.utilisateur_id = u.id
                        INNER JOIN vehicule v ON r.vehicule_id = v.id
                        INNER JOIN service s ON v.service_id = s.id";
$result = $conn->query($query);

// Préparer le tableau des événements pour le calendrier
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = transformRowToEvent($row);
}

function transformRowToEvent($row)
{
    $start = strtotime($row['date_debut']);
    $end = strtotime($row['date_fin']);
    $title = $row['prenom'] . ' ' . $row['nom'] . ' - ' . $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation'];
    return [
        'id' => $row['id'],
        'title' => $title,
        'start' => date('Y-m-d\TH:i:s', $start),
        'end' => date('Y-m-d\TH:i:s', $end),
        'extendedProps' => [
            'reservation_id' => $row['id'],
            'service' => $row['nom'],
            'vehicule' => $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation']
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des réservations</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/flatpickr.min.css">
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col">
                <?php include 'header.php'; ?>
                <h1>Calendrier des réservations</h1>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/index.global.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/fr.min.js"></script>
    <script src="js/flatpickr.min.js"></script>
    <script src="js/fr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var eventsData = <?php echo json_encode($events); ?>;
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: "aujourd'hui",
                    month: 'mois',
                    week: 'semaine',
                    day: 'jour'
                },
                allDayText: 'journée',
                editable: true,
                selectable: true,
                // },
                events: eventsData,

                validRange: function (nowDate) {
                    return {
                        start: nowDate
                    };
                },
                eventClick: function (info) {
                    var eventId = info.event.id;
                    window.open('view_reservation.php?id=' + eventId, '_blank', 'width=800, height=600');
                },
                dateClick: function (info) {
                    var clickedDate = info.dateStr;
                    window.open('create_reservation.php?date=' + clickedDate, '_blank', 'width=800, height=600');
                }
            });
            calendar.render();
        });

    </script>
<div class="modal fade" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editReservationModalLabel">Modifier une réservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Le contenu de la fenêtre modale d'édition de réservation sera chargé ici -->
      </div>
    </div>
  </div>
</div>
<script>
  // Fonction pour ouvrir la fenêtre modale d'édition de réservation
  function openEditReservationModal(reservationId) {
    $("#editReservationModal .modal-body").load("edit_reservation.php?id=" + reservationId);
    $("#editReservationModal").modal("show");
  }

  // Code pour gérer la soumission du formulaire d'édition de réservation
  $(document).ready(function() {
    $("#editReservationForm").on("submit", function(event) {
      event.preventDefault();
      $.ajax({
        url: "edit_reservation.php",
        type: "post",
        data: $(this).serialize(),
        success: function() {
          $("#editReservationModal").modal("hide");
          location.reload();
        }
      });
    });
  });
</script>

</body>

</html>