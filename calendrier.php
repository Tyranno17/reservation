<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$userId = $_SESSION['utilisateur_id'];
$services = getServices();
$vehicules = getVehicules();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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
    <div class="container mt-5">
        <h2>Calendrier de réservation de véhicule de l'Association l'Escale</h2>
        <div id='calendar'></div>
    </div>

    <!-- Première fenêtre modale (eventModal) -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="eventDetails"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div> <!-- Fermez la première fenêtre modale ici -->

    <!-- Deuxième fenêtre modale (reservationModal) -->

    <div class="modal fade modal-lg" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Nouvelle réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    <button id="editEventButton" class="btn btn-primary">Modifier</button>
                    <button id="deleteEventButton" class="btn btn-danger">Supprimer</button>
                </div>
                <div class="modal-body">
                    <!-- Insérer le formulaire de réservation ici -->
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
                        <!-- ... -->
                        <button id="editEventButton" class="btn btn-primary">Modifier</button>
                        <button id="deleteEventButton" class="btn btn-danger">Supprimer</button>
                        <!-- ... -->

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <!-- <button type="submit" name="submit" class="btn btn-primary">Réserver</button> -->
                </div>
            </div>
        </div>
    </div> <!-- Fermez la deuxieme fenêtre modale ici -->

    <?php
    require_once 'footer.php';
    ?>
    <script src="../js/jquery-3.6.4.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/index.global.min.js"></script>
    <script src="../js/flatpickr.min.js"></script>
    <script src="../js/flatpickr.fr.js"></script>
    <!-- <script src="../js/moment.min.js"></script>
    <script src="../js/fr.min.js"></script> -->

    <script>
        const currentUserId = <?php echo $_SESSION['utilisateur_id']; ?>;
        let eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        let reservationModal;

        let editingEvent;

        function isDateTimeInThePast(selectedDate, now) {
            return selectedDate.getTime() < now.getTime();
        }

        $('#reservationModal').on('submit', function (e) {
            // Récupérez les valeurs de date_debut et date_fin
            const date_debut = new Date($('#date_debut_hidden').val());
            const date_fin = new Date($('#date_fin_hidden').val());

            // Vérifiez si la date de début ou la date de fin est dans le passé
            const now = new Date();
            if (isDateTimeInThePast(date_debut, now) || isDateTimeInThePast(date_fin, now)) {
                // Empêchez la soumission du formulaire
                e.preventDefault();

                // Affichez un message d'erreur à l'utilisateur
                alert('La date et l\'heure de début ou de fin sont antérieures à l\'heure actuelle. Veuillez sélectionner des dates et heures valides.');
            }
        });

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
                buttonText: {
                    today: "aujourd'hui",
                    month: 'mois',
                    week: 'semaine', day: 'jour'
                },
                allDayText: 'journée', // Ajoutez cette ligne pour traduire "all-day" en français

                editable: true,
                events: 'get-events.php',
                validRange: function (nowDate) {
                    return {
                        start: nowDate
                    };
                },
                selectable: true, // Ajoutez cette ligne pour activer la sélection des plages de temps
            });

            calendar.render();

            function formatDateToFrench(date) {
                const frenchDate = new Date(date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                return frenchDate.toLocaleDateString('fr-FR', options);
            }

            calendar.on('eventClick', function (info) {
                // Fermer la modale de réservation si elle est ouverte
                if (reservationModal && typeof reservationModal.hide === 'function') {
                    reservationModal.hide();
                }
                var event = info.event;
                var date_debut_french = formatDateToFrench(event.startStr);
                var date_fin_french = formatDateToFrench(event.endStr);
                var eventDetails = `
            Titre: ${event.title}<br>
                Date de début: ${date_debut_french}<br>
                    Date de fin: ${date_fin_french}
            `;
                // Vérifier si l'utilisateur actuellement connecté est le créateur de la réservation
                // Supposons que l'ID de l'utilisateur actuellement connecté soit stocké dans une variable "currentUserId"
                var currentUserId = <?php echo $userId; ?>; // Supposons que $userId contient l'ID de l'utilisateur connecté
                if (event.extendedProps.creatorId == currentUserId) {
                    // Afficher les boutons Modifier et Supprimer si l'utilisateur est le créateur de la réservation
                    document.getElementById('editEventButton').style.display = 'block';
                    document.getElementById('deleteEventButton').style.display = 'block';

                } else {
                    // Masquer les boutons Modifier et Supprimer si l'utilisateur n'est pas le créateur de la réservation
                    document.getElementById('editEventButton').style.display = 'none';
                    document.getElementById('deleteEventButton').style.display = 'none';

                }

                document.getElementById('eventDetails').innerHTML = eventDetails;
                eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                eventModal.show();
            });


            calendar.on('select', function (info) {
                var selectedDate = info.start;
                var now = new Date();

                if (isDateTimeInThePast(selectedDate, now)) {
                    // Si la date sélectionnée est dans le passé, ne faites rien
                    return;
                }
                // Fermer la modale de visualisation des événements si elle est ouverte
                if (eventModal && typeof eventModal.hide === 'function') {
                    eventModal.hide();
                }
                var dateDebut = info.startStr; // Format : 'YYYY-MM-DDTHH:mm:ss'
                document.getElementById('date_debut').value = formatDateToFrench(dateDebut);
                document.getElementById('date_debut_hidden').value = dateDebut;

                // Afficher le formulaire de réservation dans la fenêtre modale
                reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
                reservationModal.show();
            });

            // Gestionnaire d'événements pour le bouton Modifier
            document.getElementById('reservationForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const eventId = editingEvent.id;
                const title = document.getElementById('reservationTitle').value;
                const dateDebut = document.getElementById('date_debut_hidden').value;
                const dateFin = document.getElementById('date_fin_hidden').value;

                // Envoyer les modifications au serveur
                fetch('edit-event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `event_id=${eventId}&title=${title}&date_debut=${dateDebut}&date_fin=${dateFin}`
                }).then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Erreur lors de la modification de l\'événement');
                    }
                }).then(result => {
                    if (result.success) {
                        // Mettre à jour l'événement sur le calendrier
                        editingEvent.setProp('title', title);
                        editingEvent.setDates(dateDebut, dateFin);

                        // Fermez la fenêtre modale de réservation et affichez un message de réussite
                        reservationModal.hide();
                        alert('La réservation a été modifiée avec succès.');
                    } else {
                        alert('Erreur lors de la modification de la réservation. Veuillez réessayer.');
                    }
                }).catch(error => {
                    alert(error.message);
                });
            });



            // Gestionnaire d'événements pour le bouton Supprimer
            document.getElementById('deleteEventButton').addEventListener('click', function () {
                // Code pour supprimer la réservation
                // ...
            });


            //filterVehiculesByService(); // Ajoutez ceci pour mettre à jour la liste des véhicules en fonction du service sélectionné

            function filterVehiculesByService() {
                let service_id = $('#service_id').val();
                $('#vehicule_id option').each(function () {
                    let optionServiceId = $(this).data('service-id');
                    if (optionServiceId === undefined) return;
                    if (service_id === optionServiceId.toString()) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $('#vehicule_id').val(''); // Réinitialiser la sélection du véhicule
            }

            $('#service_id').on('change', filterVehiculesByService);

            $("#date_debut").flatpickr({
                locale: "fr",
                dateFormat: "d-m-Y H:i",
                enableTime: true,
                time_24hr: true,
                minTime: "07:00",
                maxTime: "18:00",
                minuteIncrement: 30,
                onChange: function (selectedDates, dateStr, instance) { // Ajoutez une virgule ici
                    // Met à jour le champ caché avec la date au format Y-m-d H:i
                    $("#date_debut_hidden").val(instance.formatDate(selectedDates[0], "Y-m-d H:i"));
                }
            });

            $("#date_fin").flatpickr({
                locale: "fr",
                dateFormat: "d-m-Y H:i",
                enableTime: true,
                time_24hr: true,
                minTime: "07:00",
                maxTime: "18:00",
                minuteIncrement: 30,
                onChange: function (selectedDates, dateStr, instance) { // Ajoutez une virgule ici
                    // Met à jour le champ caché avec la date au format Y-m-d H:i
                    $("#date_fin_hidden").val(instance.formatDate(selectedDates[0], "Y-m-d H:i"));
                }
            });
        });

    </script>
</body>

</html>