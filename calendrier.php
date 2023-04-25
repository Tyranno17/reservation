<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['utilisateur_id'];

$services = getServices();
$vehicules = getVehicules();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier</title>
    <!-- Inclure les fichiers CSS ici -->
    <<link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../css/flatpickr.min.css">
        <style>
            #calendar {
                max-width: 900px;
                margin: 50px auto;
            }
        </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <<div class="container mt-5">
        <h2>Calendrier de réservation de véhicule de l'Association l'Escale</h2>
        <div id='calendar'></div>
        </div>

        <!-- Début du code pour la modal fusionnée -->
        <div class="modal fade modal-lg" tabindex="-1" role="dialog" id="eventModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalTitle">Détails de la réservation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="eventModalBody">
                        <form id="eventForm">
                            <!-- Contenu du formulaire de réservation -->
                            <form action="../process_reservation.php" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="service_id" class="form-label">Service</label>
                                            <select class="form-select" id="service_id" name="service_id" required>
                                                <option value="">Sélectionnez un service</option>
                                                <?php foreach ($services as $service): ?>
                                                    <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?>
                                                    </option>
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
                                    </div>
                                    <div class="col-md-6">
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
                                    </div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Réserver</button>
                            </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="eventModalClose">Fermer</button>
                        <button type="button" class="btn btn-primary" id="eventModalSubmit">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin du code pour la modal fusionnée -->

        <!-- Inclure les fichiers JavaScript ici -->
        <script src="../js/jquery-3.6.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/index.global.min.js"></script>
        <script src="../js/flatpickr.min.js"></script>
        <script src="../js/flatpickr.fr.js"></script>

        <script>
            // Votre code JavaScript pour le calendrier et les modals ici

            const currentUserId = <?php echo $userId; ?>;
            let eventModal;
            let eventForm;
            let eventModalSubmit;
            let calendar;

            function isDateTimeInThePast(selectedDate, now) {
                return selectedDate.getTime() < now.getTime();
            }

            function showEventModal(mode, event) {
                if (mode === 'view') {
                    $('#eventModalTitle').text('Détails de la réservation');
                    // Récupérer les informations de l'événement et les afficher dans les champs du formulaire
                    let eventInfo = event.title.split(' - ');
                    let userName = eventInfo[0];
                    let vehicleName = eventInfo[1];
                    let vehiclePlate = eventInfo[2];

                    $('#service_id').val(userName).prop('disabled', true);
                    $('#vehicule_id').val(vehicleName + ' - ' + vehiclePlate).prop('disabled', true);
                    $('#date_debut').val(event.start.format()).prop('disabled', true);
                    $('#date_fin').val(event.end.format()).prop('disabled', true);

                } else if (mode === 'edit') {
                    $('#eventModalTitle').text('Modifier la réservation');
                    // Mettre à jour le contenu du formulaire avec les détails de l'événement et rendre les champs modifiables
                } else if (mode === 'create') {
                    $('#eventModalTitle').text('Créer une réservation');
                    // Réinitialiser le formulaire pour la création d'un nouvel événement
                }
                eventModal.modal('show');
            }


            // Initialise le calendrier Flatpickr pour les dates de début et de fin
            document.addEventListener('DOMContentLoaded', function () {
                eventModal = $('#eventModal');
                eventForm = $('#eventForm');
                eventModalSubmit = $('#eventModalSubmit');

                // Initialise le calendrier
                let calendarEl = document.getElementById('calendar');
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
                        week: 'semaine', day: 'jour'
                    },
                    allDayText: 'journée', // Ajoutez cette ligne pour traduire "all-day" en français
                    events: 'get-events.php',
                    eventColor: 'green',
                    eventDisplay: 'block',
                    eventClick: function (info) {
                        if (info.event.extendedProps.user_id === currentUserId) {
                            showEventModal('edit', info.event);
                        } else {
                            showEventModal('view', info.event);
                        }
                    },
                    dateClick: function (info) {
                        if (!isDateTimeInThePast(info.date, new Date())) {
                            showEventModal('create', { start: info.date });
                        }
                    },
                });

                calendar.render();

                // Initialise le calendrier Flatpickr pour les dates de début et de fin
                const dateDebutPicker = flatpickr("#date_debut", {
                    locale: "fr",
                    dateFormat: "d-m-Y H:i",
                    enableTime: true,
                    time_24hr: true,
                    minTime: "07:00",
                    maxTime: "18:00",
                    minuteIncrement: 30,
                    onClose: function (selectedDates, dateStr, instance) {
                        $("#date_debut_hidden").val(dateStr);
                    },
                });

                const dateFinPicker = flatpickr("#date_fin", {
                    locale: "fr",
                    dateFormat: "d-m-Y H:i",
                    enableTime: true,
                    time_24hr: true,
                    minTime: "07:00",
                    maxTime: "18:00",
                    minuteIncrement: 30,
                    onClose: function (selectedDates, dateStr, instance) {
                        $("#date_fin_hidden").val(dateStr);
                    },
                });
            });

        </script>
</body>

</html>