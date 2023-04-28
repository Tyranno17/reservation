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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
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
                            <input type="text" class="form-control" id="add_date_debut" required>
                        </div>
                        <div class="form-group">
                            <label for="add_date_fin">Date de fin</label>
                            <input type="text" class="form-control" id="add_date_fin" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="saveEvent()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once 'footer.php';
    ?>
    <script src="../js/jquery-3.6.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/index.global.min.js"></script>
    <script src="../js/moment.min.js"></script>
    <script src="../js/fr.min.js"></script>
    <script src="../js/flatpickr.min.js"></script>
    <script src="../js/fr.js"></script>

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
            $('#addEventForm').on('submit', function (e) {
                e.preventDefault();
                saveEvent();
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
                events: 'get-events.php',
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

        function showAddEventModal(startDate, endDate) {
            $('#add_date_debut').val('');
            $('#add_date_fin').val('');

            $('#addEventModal').modal('show');
        }

        function saveEvent() {
            const utilisateur_id = $('#add_utilisateur_id').val();
            const vehicule_id = $('#add_vehicule_id').val();
            const date_debut = $('#add_date_debut').val();
            const date_fin = $('#add_date_fin').val();

            $.ajax({
                url: 'save-event.php',
                type: 'POST',
                data: {
                    utilisateur_id: utilisateur_id,
                    vehicule_id: vehicule_id,
                    date_debut: date_debut,
                    date_fin: date_fin
                },
                success: function (response) {
                    if (response === 'success') {
                        $('#addEventModal').modal('hide');
                        location.reload();
                    } else {
                        // Gérer les erreurs ici
                        alert('Une erreur est survenue lors de l\'enregistrement de l\'événement.');
                    }
                },
                error: function () {
                    // Gérer les erreurs ici
                    alert('Une erreur est survenue lors de l\'enregistrement de l\'événement.');
                }
            });
        }


    </script>
</body>

</html>