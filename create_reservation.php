<?php
session_start();
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'includes/db.php';
require_once 'includes/functions.php';


// Ajoutez ceci en haut de votre fichier create_reservation.php
if (isset($_GET['date'])) {
    $selected_date = $_GET['date'];
} else {
    $selected_date = '';
}


if (!isPostRequest()) {
    echo json_encode(['success' => false, 'message' => 'La méthode de requête n\'est pas POST.']);
    exit;
}

$service_id = $_POST['service_id'];
$vehicule_id = $_POST['vehicule_id'];
$date_debut = convertDateFormat($_POST['date_debut']);
$date_fin = convertDateFormat($_POST['date_fin']);
$utilisateur_id = $_SESSION['utilisateur_id'];

if (!checkReservationDuration($date_debut, $date_fin)) {
    echo json_encode(['success' => false, 'message' => 'La durée de la réservation ne peut pas être inférieure à 1 heure.']);
    exit;
}

if (checkReservation($conn, $service_id, $vehicule_id, $date_debut, $date_fin)) {
    echo json_encode(['success' => false, 'message' => 'La date et l\'horaire sont déjà pris pour ce véhicule.']);
    exit;
}

insertReservation($vehicule_id, $utilisateur_id, $date_debut, $date_fin);
sendEmail($userEmail, $subject, $message);

echo json_encode(['success' => true]);
?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
<title>Ajouter une réservation</title>

<?php if (isset($message)) : ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


<h1>Ajouter une réservation</h1>

<form id="createReservationForm" action="create_reservation.php" method="post">
    <!-- Ajoutez cette ligne pour inclure l'ID de la réservation -->
    <input type="hidden" id="utilisateur_id" name="utilisateur_id" value="<?php echo $_SESSION['utilisateur_id']; ?>">
    <div class="mb-3">
        <label for="service_id" class="form-label">Service</label>
        <select class="form-select" name="service_id" id="service_id">
            <?php while ($service = $result_services->fetch_assoc()) : ?>
                <option value="<?php echo $service['id']; ?>">
                    <?php echo $service['nom']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="vehicule_id" class="form-label">Véhicule</label>
        <select class="form-select" name="vehicule_id" id="vehicule_id">
            <?php while ($vehicule = $result_vehicules->fetch_assoc()) : ?>
                <option value="<?php echo $vehicule['id']; ?>" data-service-id="<?php echo $vehicule['service_id']; ?>" style="display:none;">
                    <?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['immatriculation']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="date_debut" class="form-label">Date de début</label>
        <input type="text" class="form-control flatpickr-datetime" id="date_debut" name="date_debut" placeholder="Cliquez pour choisir la date et l'heure" required>
    </div>
    <div class="mb-3">
        <label for="date_fin" class="form-label">Date de fin</label>
        <input type="text" class="form-control flatpickr-datetime" id="date_fin" name="date_fin" placeholder="Cliquez pour choisir la date et l'heure" required>
    </div>
</form>
<script src="js/custom.js"></script>
<script>
    $(document).ready(function() {
        $("#service_id").on("change", function() {
            var service_id = $(this).val();
            $("#vehicule_id").val("");
            $("#vehicule_id option").each(function() {
                if ($(this).data("service-id") == service_id) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        $("#service_id").trigger("change");
    });
</script>