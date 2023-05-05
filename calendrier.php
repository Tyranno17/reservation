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
                        <p class="card-text">ID : <span id="view_reservation_id"></span></p>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
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
                    <!-- <button type="button" class="btn btn-primary" id="openEditModal">Modifier</button> -->


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

                <script>
                    $(document).ready(function () {
                        initCalendar();
                        // Initialisation de flatpickr sur les champs de date
                        flatpickr("#add_date_debut", {
                            locale: "fr",
                            dateFormat: "d-m-Y H:i",
                            enableTime: true,
                            time_24hr: true,
                            minTime: "07:00",
                            maxTime: "18:00",
                            minuteIncrement: 30,

                        });

                        flatpickr("#add_date_fin", {
                            locale: "fr",
                            dateFormat: "d-m-Y H:i",
                            enableTime: true,
                            time_24hr: true,
                            minTime: "07:00",
                            maxTime: "18:00",
                            minuteIncrement: 30,

                        });

                        // Filtre les véhicules en fonction du service sélectionné
                        $("#add_service_id").on("change", function () {
                            var selectedServiceId = $(this).val();
                            $("#add_vehicule_id option").each(function () {
                                var vehiculeServiceId = $(this).data("service-id");
                                if (selectedServiceId == vehiculeServiceId || $(this).val() == "") {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });
                        });

                    });

                    function initCalendar() {
                        var calendarEl = document.getElementById('calendar');
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
                            events: {
                                url: 'get-events.php',
                                method: 'GET',
                                failure: function () {
                                    alert('Erreur lors du chargement des événements.');
                                }
                            },
                            validRange: function (nowDate) {
                                return {
                                    start: nowDate
                                };
                            },
                            eventClick: function (info) {
                                showEventModal("view", info.event);
                            },
                            select: function (info) {
                                showAddEventModal(info.start, info.end);
                            }
                        });

                        calendar.render();
                    }


                    // Ajoutez le gestionnaire d'événements pour l'événement shown.bs.modal
                    $('#viewEventModal').on('shown.bs.modal', function () {
                        // Ajoutez le gestionnaire d'événements pour le bouton "Modifier"
                        document.getElementById('openEditModal').addEventListener('click', function () {
                            // Fermez la fenêtre modale de visualisation
                            $('#viewEventModal').modal('hide');

                            // Ouvrez la fenêtre modale de modification
                            $('#editEventModal').modal('show');
                        });
                    });



                    function showEventModal(mode, event) {
                        let eventId = event.id;

                        $('#service_id').val(event.extendedProps.service).prop('disabled', true);
                        $('#vehicule_id').val(event.extendedProps.vehicule).prop('disabled', true);

                        // Formatez les dates en utilisant moment.js avec le support des locales
                        moment.locale('fr'); // Définissez la locale sur français
                        let formattedStartDate = moment(event.start.toISOString()).format('DD/MM/YYYY HH:mm');
                        let formattedEndDate = moment(event.end.toISOString()).format('DD/MM/YYYY HH:mm');

                        $('#view_service_id').val(event.extendedProps.service);
                        $('#view_vehicule_id').val(event.extendedProps.vehicule);
                        $('#view_date_debut').val(formattedStartDate);
                        $('#view_date_fin').val(formattedEndDate);

                        $('#viewEventModal').modal('show');
                    }

                    $('#addEventModal').on('show.bs.modal', function () {
                        $('#add_service_id').val(''); // Remplacez par l'ID de l'élément de votre champ utilisateur
                        $('#add_vehicule_id').val(''); // Remplacez par l'ID de l'élément de votre champ véhicule
                        $('#add_date_debut').val(''); // Remplacez par l'ID de l'élément de votre champ date de début
                        $('#add_date_fin').val(''); // Remplacez par l'ID de l'élément de votre champ date de fin
                    });


                    function showAddEventModal(startDate, endDate) {
                        $('#add_date_debut').val('');
                        $('#add_date_fin').val('');

                        $('#addEventModal').modal('show');
                    }
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {

                        document.getElementById('editEventButton').addEventListener('click', function () {
                            // Transférer les données de la fenêtre modale de visualisation vers la fenêtre modale d'édition/modification
                            document.getElementById('edit_reservation_id').value = document.getElementById('view_reservation_id').innerText;
                            document.getElementById('edit_service_id').value = document.getElementById('view_service_id').innerText;
                            document.getElementById('edit_vehicule_id').value = document.getElementById('view_vehicule_id').innerText;
                            document.getElementById('edit_date_debut').value = document.getElementById('view_date_debut').innerText;
                            document.getElementById('edit_date_fin').value = document.getElementById('view_date_fin').innerText;

                            // Ouvrir la fenêtre modale d'édition/modification
                            $('#viewEventModal').modal('hide');
                            $('#editEventModal').modal('show');
                        });
                        document.getElementById('editEventForm').addEventListener('submit', function (event) {
                            event.preventDefault();

                            // Récupérer les données du formulaire
                            const reservationId = document.getElementById('edit_reservation_id').value;
                            const serviceId = document.getElementById('edit_service_id').value;
                            const vehiculeId = document.getElementById('edit_vehicule_id').value;
                            const dateDebut = document.getElementById('edit_date_debut').value;
                            const dateFin = document.getElementById('edit_date_fin').value;

                            // Effectuer une requête AJAX pour envoyer les données au fichier update-event.php
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'update-event.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function () {
                                if (xhr.status === 200) {
                                    // Fermer la fenêtre modale d'édition/modification
                                    $('#editEventModal').modal('hide');

                                    // Actualiser le calendrier pour afficher les modifications
                                    calendar.refetchEvents();
                                } else {
                                    console.error('Erreur lors de la mise à jour de la réservation : ' + xhr.statusText);
                                }
                            };
                            xhr.send('reservation_id=' + encodeURIComponent(reservationId) + '&service_id=' + encodeURIComponent(serviceId) + '&vehicule_id=' + encodeURIComponent(vehiculeId) + '&date_debut=' + encodeURIComponent(dateDebut) + '&date_fin=' + encodeURIComponent(dateFin));
                        });
                    });


                </script>
</body>

</html>