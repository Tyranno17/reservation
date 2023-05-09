<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';


$services = getServices();
$vehicules = getVehicules();

// Requête SQL pour sélectionner les réservations
$sql_reservations = "SELECT * FROM reservation";

// Exécutez la requête
$result_reservations = $mysqli->query($sql_reservations);


$reservations = [];

// Vérifiez si des résultats ont été trouvés
if ($result_reservations->num_rows > 0) {
    // Parcourez les résultats et stockez-les dans un tableau
    while ($row = $result_reservations->fetch_assoc()) {
        $reservations[] = $row;
    }
}

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
                            <div class="form-group">
                                <label for="edit_reservation_id">ID de réservation</label>
                                <select class="form-control" id="edit_reservation_id" name="reservation_id" required>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <option value="<?php echo $reservation['id']; ?>"><?php echo $reservation['id']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                                <input type="text" class="form-control" id="edit_date_debut" name="date_debut" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_date_fin">Date de fin</label>
                                <input type="text" class="form-control" id="edit_date_fin" name="date_fin" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
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
                        // Code pour filtrer les véhicules en fonction du service sélectionné
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

                // Ajout des écouteurs d'événements pour les modals
                $('#viewEventModal').on('shown.bs.modal', function () {
                    var editButton = document.getElementById('editEventButton');
                    if (editButton) {
                        editButton.addEventListener('click', function () {
                            // Récupération de l'ID de réservation à partir de l'attribut data-reservation-id
                            var reservationId = $(this).data('reservation-id');

                            // Récupération des données de l'événement à partir de la modal de visualisation
                            var serviceId = $('#view_service_id').val();
                            var vehiculeId = $('#view_vehicule_id').val();
                            var dateDebut = $('#view_date_debut').val();
                            var dateFin = $('#view_date_fin').val();

                            // Appeler la fonction showEventModal avec le mode 'edit' et l'ID de réservation
                            showEventModal('edit', reservationId);
                        });
                    }
                });

                // Transfert des données vers la modal d'édition
                $('#edit_reservation_id').val(reservationId);
                $('#edit_service_id').val(serviceId);
                $('#edit_vehicule_id').val(vehiculeId);
                $('#edit_date_debut').val(dateDebut);
                $('#edit_date_fin').val(dateFin);

                // Fermeture de la modal de visualisation
                // $('#viewEventModal').modal('hide');
                $('#viewEventModal').modal('toggle');

                // Ouverture de la modal d'édition
                $('#editEventModal').modal('show');
                        });
                    }
                });

                $('#editEventModal').on('shown.bs.modal', function () {
                    var saveButton = document.getElementById('saveEditEventButton');
                    if (saveButton) {
                        saveButton.addEventListener('click', function () {
                            // Récupération des données mises à jour à partir de la modal d'édition
                            var reservationId = $('#edit_reservation_id').val();
                            var serviceId = $('#edit_service_id').val();
                            var vehiculeId = $('#edit_vehicule_id').val();
                            var dateDebut = $('#edit_date_debut').val();
                            var dateFin = $('#edit_date_fin').val();

                            // Mise à jour de l'événement sur le serveur
                            $.ajax({
                                url: 'save-event.php',
                                method: 'POST',
                                data: {
                                    reservation_id: reservationId,
                                    service_id: serviceId,
                                    vehicule_id: vehiculeId,
                                    date_debut: dateDebut,
                                    date_fin: dateFin
                                },
                                success: function (response) {
                                    // Fermeture de la modal d'édition
                                    $('#editEventModal').modal('hide');

                                    // Rechargement du calendrier pour afficher les modifications
                                    calendar.refetchEvents();
                                },
                                error: function (xhr, status, error) {
                                    // Gestion des erreurs éventuelles lors de la mise à jour
                                    console.error('Erreur lors de la mise à jour de la réservation:', error);
                                }
                            });
                        });
                    }
                });

                // Autres initialisations et écouteurs d'événements
                // ...

                function initCalendar() {
                    var calendarEl = document.getElementById('calendar');
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'fr',
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        buttonText: {
                            today: 'Aujourd\'hui',
                            month: 'Mois',
                            week: 'Semaine',
                            day: 'Jour'
                        },
                        slotMinTime: '07:00:00',
                        slotMaxTime: '18:00:00',
                        slotDuration: '00:30:00',
                        expandRows: true,
                        stickyHeaderDates: true,
                        allDaySlot: false,
                        nowIndicator: true,
                        events: 'get-events.php',
                        eventClick: function (info) {
                            var event = info.event;
                            showEventModal('view', event);
                        },

                        // Autres options et callbacks
                        // ...
                    });

                    calendar.render();
                }


                function showEventModal(mode, event) {
                    let eventId = event.id;

                    if (mode === 'view') {
                        $('#view_reservation_id').text(event.id);

                        $('#service_id').val(event.extendedProps.service).prop('disabled', true);
                        $('#vehicule_id').val(event.extendedProps.vehicule).prop('disabled', true);

                        // Formatez les dates en utilisant moment.js avec le support des locales
                        moment.locale('fr'); // Définissez la locale sur français
                        let formattedStartDate = moment(event.start.toISOString()).format('DD/MM/YYYY HH:mm');
                        let formattedEndDate = moment(event.end.toISOString()).format('DD/MM/YYYY HH:mm');

                        // Ajoutez cette ligne pour définir la valeur de 'view_reservation_id'
                        $('#view_reservation_id').text(event.extendedProps.reservation_id);

                        $('#view_service_id').val(event.extendedProps.service);
                        $('#view_vehicule_id').val(event.extendedProps.vehicule);
                        $('#view_date_debut').val(formattedStartDate);
                        $('#view_date_fin').val(formattedEndDate);

                        // Ajoutez cette ligne pour définir l'attribut data-reservation-id sur le bouton d'édition
                        $('#editEventButton').data('reservation-id', event.id);

                        $('#viewEventModal').modal('show');
                    } else if (mode === 'edit') {
                        // Les autres modifications suggérées iront ici
                    }
                }



            </script>
</body>

</html>