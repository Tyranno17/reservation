<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';


$services = getServices();
$vehicules = getVehicules();

?>

<!doctype html>

<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendrier</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/flatpickr.min.css">
    <script src="../js/jquery-3.6.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/index.global.min.js"></script>
    <script src="../js/moment.min.js"></script>
    <script src="../js/fr.min.js"></script>
    <script src="../js/flatpickr.min.js"></script>
    <script src="../js/fr.js"></script>

    <style>
        .fc-event {
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <div id="calendar"></div>
    </div>

    <!-- Modale pour visualiser un événement -->
    <div class="modal fade" id="viewEventModal" tabindex="-1" role="dialog" aria-labelledby="viewEventModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewEventModalTitle">Détails de la réservation</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="view_reservation_id">ID de réservation</label>
                            <span class="form-control" id="view_reservation_id" readonly></span>
                        </div>
                        <div class="form-group">
                            <label for="view_service_id">Service</label>
                            <input type="text" class="form-control" id="view_service_id" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_vehicule_id">Véhicule</label>
                            <input type="text" class="form-control" id="view_vehicule_id" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_date_debut">Date de début</label>
                            <input type="text" class="form-control" id="view_date_debut" readonly>
                        </div>
                        <div class="form-group">
                            <label for="view_date_fin">Date de fin</label>
                            <input type="text" class="form-control" id="view_date_fin" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="editEventButton">Modifier</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale pour ajouter un événement -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalTitle">Ajouter une réservation</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addEventForm" action="save-event.php" method="post">
                        <div class="form-group">
                            <label for="add_service_id">Service</label>
                            <select class="form-control" id="add_service_id" name="service_id" required>
                                <option value="">Sélectionnez un service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_vehicule_id">Véhicule</label>
                            <select class="form-control" id="add_vehicule_id" name="vehicule_id" required>
                                <option value="">Sélectionnez un véhicule</option>
                                <?php foreach ($vehicules as $vehicule): ?>
                                    <option value="<?php echo $vehicule['id']; ?>"
                                        data-service-id="<?php echo $vehicule['service_id']; ?>"><?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' (' . $vehicule['immatriculation'] . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_date_debut">Date de début</label>
                            <input type="text" class="form-control" id="add_date_debut" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label for="add_date_fin">Date de fin</label>
                            <input type="text" class="form-control" id="add_date_fin" name="date_fin" required>
                        </div>
                        <input type="hidden" name="utilisateur_id" value="<?php echo $_SESSION['utilisateur_id']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
                <!-- Fenêtre modale d'édition/modification -->
                <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog"
                    aria-labelledby="editEventModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editEventModalLabel">Modifier une réservation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editEventForm">
                                    <div class="form-group">
                                        <label for="edit_reservation_id">ID de réservation</label>
                                        <input type="text" class="form-control" id="edit_reservation_id"
                                            name="reservation_id" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_service_id">Service</label>
                                        <select class="form-control" id="edit_service_id" name="service_id" required>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_vehicule_id">Véhicule</label>
                                        <select class="form-control" id="edit_vehicule_id" name="vehicule_id" required>
                                            <?php foreach ($vehicules as $vehicule): ?>
                                                <option value="<?php echo $vehicule['id']; ?>"
                                                    data-service-id="<?php echo $vehicule['service_id']; ?>"><?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' (' . $vehicule['immatriculation'] . ')'; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_date_debut">Date de début</label>
                                        <input type="text" class="form-control" id="edit_date_debut" name="date_debut"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_date_fin">Date de fin</label>
                                        <input type="text" class="form-control" id="edit_date_fin" name="date_fin"
                                            required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer les
                                            modifications</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <?php
                require_once 'footer.php';
                ?>

                <body>

                    <div class="container">
                        <div id="calendar" class="mt-5"></div>
                    </div>
                    <script>
                        $(document).ready(function () {
                            var calendarEl = document.getElementById('calendar');

                            var calendar = new FullCalendar.Calendar(calendarEl, {
                                locale: 'fr',
                                headerToolbar: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                                },
                                initialDate: '2022-05-07',
                                navLinks: true,
                                selectable: true,
                                selectMirror: true,
                                dayMaxEvents: true,
                                events: 'load-events.php',
                                select: function (info) {
                                    $('#createEventModal').modal('show');
                                },
                                eventClick: function (info) {
                                    showEventModal('view', info.event);
                                }
                            });

                            calendar.render();

                            function showEventModal(mode, event) {
                                let eventId = event.id;

                                $('#service_id').val(event.extendedProps.service).prop('disabled', true);
                                $('#vehicule_id').val(event.extendedProps.vehicule).prop('disabled', true);

                                moment.locale('fr');
                                let formattedStartDate = moment(event.start.toISOString()).format('DD/MM/YYYY HH:mm');
                                let formattedEndDate = moment(event.end.toISOString()).format('DD/MM/YYYY HH:mm');

                                $('#view_reservation_id').val(eventId);
                                $('#view_service_id').val(event.extendedProps.service);
                                $('#view_vehicule_id').val(event.extendedProps.vehicule);
                                $('#view_date_debut').val(formattedStartDate);
                                $('#view_date_fin').val(formattedEndDate);

                                $('#viewEventModal').modal('show');

                                $('#editEventButton').click(function () {
                                    document.getElementById('edit_reservation_id').value = document.getElementById('view_reservation_id').value;
                                    document.getElementById('edit_service_id').value = document.getElementById('view_service_id').value;
                                    document.getElementById('edit_vehicule_id').value = document.getElementById('view_vehicule_id').value;
                                    document.getElementById('edit_date_debut').value = document.getElementById('view_date_debut').value;
                                    document.getElementById('edit_date_fin').value = document.getElementById('view_date_fin').value;

                                    $('#viewEventModal').modal('hide');
                                    $('#editEventModal').modal('show');
                                });

                                document.getElementById('editEventForm').addEventListener('submit', function (event) {
                                    event.preventDefault();

                                    let reservationId = document.getElementById('edit_reservation_id').value;
                                    let serviceId = document.getElementById('edit_service_id').value;
                                    let vehiculeId = document.getElementById('edit_vehicule_id').value;
                                    let dateDebut = document.getElementById('edit_date_debut').value;
                                    let dateFin = document.getElementById('edit_date_fin').value;

                                    const xhr = new XMLHttpRequest();
                                    xhr.open('POST', 'update-event.php', true);
                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    xhr.onload = function () {
                                        if (xhr.status === 200) {
                                            $('#editEventModal').modal('hide');
                                            calendar.refetchEvents();
                                        } else {
                                            console.error('Erreur lors de la mise à jour de la réservation : ' + xhr.statusText);
                                        }
                                    };
                                    xhr.send('reservation_id=' + encodeURIComponent(reservationId) + '&service_id=' + encodeURIComponent(serviceId) + '&vehicule_id=' + encodeURIComponent(vehiculeId) + '&date_debut=' + encodeURIComponent(dateDebut) + '&date_fin=' + encodeURIComponent(dateFin));
                                });
                            }
                        });

                    </script>
                </body>

</html>