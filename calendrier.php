<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calendrier</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/fullcalendar.min.css">
    <link rel="stylesheet" href="../css/flatpickr.min.css">
    <style>
        .fc-event {
            font-size: 0.8em;
        }
    </style>
</head>

<body>
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
                            <input type="text" class="form-control" id="add_service_id">
                        </div>
                        <div class="form-group">
                            <label for="add_vehicule_id">Véhicule</label>
                            <input type="text" class="form-control" id="add_vehicule_id">
                        </div>
                        <div class="form-group">
                            <label for="add_date_debut">Date de début</label>
                            <input type="text" class="form-control" id="add_date_debut">
                        </div>
                        <div class="form-group">
                            <label for="add_date_fin">Date de fin</label>
                            <input type="text" class="form-control" id="add_date_fin">
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

    <script src="../js/jquery-3.6.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/index.global.min.js"></script>
    <script src="../js/flatpickr.min.js"></script>
    <script src="../js/flatpickr.fr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'get-events.php',
                selectable: true,
                select: function (info) {
                    showAddEventModal(info.startStr, info.endStr);
                },
                eventClick: function (event) {
                    showEventModal('view', event);
                }
            });
            calendar.render();
        });

        function showEventModal(mode, event) {
            let eventId = event.id;
            let eventInfo = event.title.split(' - ');
            let userName = eventInfo[0];
            let vehicleName = eventInfo[1];
            let vehiclePlate = eventInfo[2];

            $('#service_id').val(serviceName).prop('disabled', true);

            $('#view_service_id').val(userName);
            $('#view_vehicule_id').val(vehicleName + ' - ' + vehiclePlate);
            $('#view_date_debut').val(event.start.toISOString());
            $('#view_date_fin').val(event.end.toISOString());

            $('#viewEventModal').modal('show');
        }

        function showAddEventModal(startDate, endDate) {
            $('#add_date_debut').val(startDate);
            $('#add_date_fin').val(endDate);

            $('#addEventModal').modal('show');
        }

        function saveEvent() {
            // Code pour enregistrer l'événement dans la base de données
        }
    </script>
</body>

</html>