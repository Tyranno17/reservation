<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$query = "SELECT r.id, r.date_debut, r.date_fin, r.sAMAccountName, r.destination, u.prenom, u.nom, v.marque, v.modele, v.immatriculation, e.nom
FROM reservation r
INNER JOIN utilisateur u ON r.sAMAccountName COLLATE utf8mb4_unicode_ci = u.sAMAccountName COLLATE utf8mb4_unicode_ci
INNER JOIN vehicule v ON r.vehicule_id = v.id
INNER JOIN etablissement e ON v.etablissement_id = e.id";

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
      'reservation' => $row['destination'],
      'user_id' => $row['sAMAccountName'], // Ajoutez cette ligne
      'etablissement' => $row['nom'],
      'vehicule' => $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation']
    ]
  ];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link href="/css/bootstrap.min.css" rel="stylesheet"> -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet">
</head>

<body data-current-user-id="<?php echo $_SESSION['sAMAccountName']; ?>">

  <div class="container">
    <div class="row">
      <div class="col">
        <?php include 'header.php'; ?>
        <h1>Calendrier des réservations</h1>
        <div id="calendar"></div>
      </div>
    </div>
  </div>

  <!-- <script src="js/jquery-3.6.4.min.js"></script> -->
  <!-- <script src="js/bootstrap.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.7/index.global.min.js"></script>
  <!-- <script src="js/index.global.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/fr.min.js"></script>
  <script src="js/custom.js"></script>


  <script>
    // Déclarer la variable calendar au niveau global
    var calendar;

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var eventsData = <?php echo json_encode($events); ?>;

      // Initialiser le calendrier
      calendar = new FullCalendar.Calendar(calendarEl, {
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
        events: 'get-events.php',

        validRange: function(nowDate) {
          return {
            start: nowDate
          };
        },
        eventClick: function(info) {
          var eventId = info.event.extendedProps.reservation_id;
          openViewReservationModal(eventId);
        },

        dateClick: function(info) {
          var clickedDate = info.dateStr;
          openCreateReservationModal(clickedDate);
        },
      });

      // Rendre le calendrier
      calendar.render();
    });
  </script>

  <div class="modal fade modal-lg" id="editReservationModal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editReservationModalLabel">Modifier une réservation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="editReservationModalContent">
          <!-- Le contenu de la modale d'édition sera chargé ici -->
        </div>
        <div class="modal-footer">
          <button id="editButtonInEditModal" type="button" class="btn btn-primary">Modifier la réservation</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade modal-lg" id="viewReservationModal" tabindex="-1" role="dialog" aria-labelledby="viewReservationModalLabel" aria-hidden="true" data-reservation-id="">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewReservationModalLabel">Voir la réservation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Le contenu de la fenêtre modale de visualisation de réservation sera chargé ici -->
        </div>
        <div class="modal-footer">
          <button id="deleteButtonInViewModal" class="btn btn-danger" data-reservation-id="">Supprimer</button>
          <button id="editButtonInViewModal" class="btn btn-primary" data-reservation-id="">Modifier</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade modal-lg" id="createReservationModal" tabindex="-1" aria-labelledby="createReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createReservationModalLabel">Créer une réservation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Le contenu de la fenêtre modale de création de réservation sera chargé ici -->
        </div>
        <div class="modal-footer">
          <button id="createButtonInCreateModal" type="button" class="btn btn-primary">Ajouter une réservation</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>


</body>

</html>