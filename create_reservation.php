<?php
session_start();
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['sAMAccountName'])) {
/* if (!checkUserLogged()) {
    header('Location: index.php');
    exit; */
}

require_once 'includes/db.php';
require_once 'includes/functions.php';


// Ajoutez ceci en haut de votre fichier create_reservation.php
if (isset($_GET['date'])) {
    $selected_date = $_GET['date'];
} else {
    $selected_date = '';
}



$sql_etablissements = "SELECT * FROM etablissement";
$result_etablissements = $conn->query($sql_etablissements);

$sql_vehicules = "SELECT * FROM vehicule";
$result_vehicules = $conn->query($sql_vehicules);


$conn->close();
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
    <input type="hidden" id="sAMAccountName" name="sAMAccountName" value="<?php echo $_SESSION['sAMAccountName']; ?>">
    <div class="mb-3">
        <label for="etablissement_id" class="form-label">Etablissement</label>
        <select class="form-select" name="etablissement_id" id="etablissement_id">
            <?php while ($etablissement = $result_etablissements->fetch_assoc()) : ?>
                <option value="<?php echo $etablissement['id']; ?>">
                    <?php echo $etablissement['nom']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="vehicule_id" class="form-label">Véhicule</label>
        <select class="form-select" name="vehicule_id" id="vehicule_id">
            <?php while ($vehicule = $result_vehicules->fetch_assoc()) : ?>
                <option value="<?php echo $vehicule['id']; ?>" data-etablissement-id="<?php echo $vehicule['etablissement_id']; ?>" style="display:none;">
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
    <div class="mb-3">
        <label for="destination" class="form-label">Destination</label>
        <input type="text" class="form-control" id="destination" name="destination" placeholder="Veuillez indiquer votre destination, Pensez au covoiturage" required>
    </div>
</form>
<script src="js/custom.js"></script>
<script>
    $(document).ready(function() {
        $("#etablissement_id").on("change", function() {
            var etablissement_id = $(this).val();
            $("#vehicule_id").val("");
            $("#vehicule_id option").each(function() {
                if ($(this).data("etablissement-id") == etablissement_id) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        $("#etablissement_id").trigger("change");
    });
</script>