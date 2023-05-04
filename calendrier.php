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
                    <button type="button" class="btn btn-primary" id="openEditModal">Modifier</button>


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
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
                <!-- Modale pour éditer, modifier une réservation -->
                <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog"
                    aria-labelledby="editEventModalTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editEventModalTitle">Modifier une réservation</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editEventForm" action="edit-event.php" method="post">
                                    <div class="form-group">
                                        <label for="edit_service_id">Service</label>
                                        <select class="form-control" id="edit_service_id" name="service_id" required>
                                            <option value="">Sélectionnez un service</option>
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_vehicule_id">Véhicule</label>
                                        <select class="form-control" id="edit_vehicule_id" name="vehicule_id" required>
                                            <option value="">Sélectionnez un véhicule</option>
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
                                    <input type="hidden" id="edit_utilisateur_id" name="utilisateur_id"
                                        value="<?php echo $_SESSION['utilisateur_id']; ?>">
                                    <input type="hidden" id="edit_reservation_id" name="reservation_id">
                                </form>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>

                        </div>
                    </div>
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

        // calendar.on('eventClick', function (info) {
        //     // Récupérez les données de l'événement
        //     const reservationId = info.event.id;
        //     const utilisateurId = info.event.extendedProps.utilisateur_id;
        //     const vehiculeId = info.event.extendedProps.vehicule_id;
        //     const dateDebut = info.event.start;
        //     const dateFin = info.event.end;
        //     const userIdConnected = document.getElementById('utilisateur_id').value; // Obtenez l'ID de l'utilisateur connecté (adaptez cette ligne en fonction de votre implémentation)

        //     // Vérifiez si l'utilisateur peut modifier la réservation
        //     if (utilisateurId === userIdConnected && dateDebut > Date.now()) {
        //         // Remplissez les champs de la fenêtre modale de modification
        //         $('#edit_reservation_id').val(reservationId);
        //         $('#edit_utilisateur_id').val(utilisateurId);
        //         $('#edit_vehicule_id').val(vehiculeId);
        //         $('#edit_date_debut').val(dateDebut.toISOString());
        //         $('#edit_date_fin').val(dateFin.toISOString());

        //         // Affichez la fenêtre modale de modification
        //         $('#editEventModal').modal('show');
        //     } else {
        //         // Affichez un message d'erreur ou effectuez une autre action en cas d'échec des vérifications
        //     }
        // });
        document.getElementById('editEventButton').addEventListener('click', function () {
            // Récupérer les valeurs des champs de la fenêtre modale
            const serviceId = document.getElementById('edit_service_id').value;
            const vehiculeId = document.getElementById('edit_vehicule_id').value;
            const dateDebut = document.getElementById('edit_date_debut').value;
            const dateFin = document.getElementById('edit_date_fin').value;
            const reservationId = document.getElementById('edit_reservation_id').value;

            // Envoyer les données à un fichier PHP pour mettre à jour l'événement
            const formData = new FormData();
            formData.append('service_id', serviceId);
            formData.append('vehicule_id', vehiculeId);
            formData.append('date_debut', dateDebut);
            formData.append('date_fin', dateFin);
            formData.append('reservation_id', reservationId);

            fetch('update-event.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Fermer la fenêtre modale "Modifier une réservation"
                        $('#editEventModal').modal('hide');

                        // Recharger les événements du calendrier pour afficher les modifications
                        calendar.refetchEvents();
                    } else {
                        alert('Erreur lors de la mise à jour de la réservation : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la mise à jour de la réservation :', error);
                });
        });


    </script>
</body>

</html>