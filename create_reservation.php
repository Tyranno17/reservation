<?php
session_start();
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ajoutez ceci en haut de votre fichier create_reservation.php
if (isset($_GET['date'])) {
    $selected_date = $_GET['date'];
} else {
    $selected_date = '';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $utilisateur_id = $_SESSION['utilisateur_id']; // Ajoutez cette ligne

    // Vérifier si la date et l'horaire sont déjà pris
    $sql_check = "SELECT * FROM reservation WHERE vehicule_id = ? AND ((date_debut >= ? AND date_debut < ?) OR (date_fin > ? AND date_fin <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("issss", $vehicule_id, $date_debut, $date_fin, $date_debut, $date_fin);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Date et horaire déjà pris, afficher un message d'erreur
        $message = "La date et l'horaire sont déjà pris pour ce véhicule.";
    } else {
        // Insérer la réservation
        $sql = "INSERT INTO reservation (service_id, vehicule_id, date_debut, date_fin, utilisateur_id) VALUES (?, ?, ?, ?, ?)"; // Modifiez cette ligne
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $service_id, $vehicule_id, $date_debut, $date_fin, $utilisateur_id); // Modifiez cette ligne
        $stmt->execute();

        header('Location: calendrier.php');
        exit;
    }
}

$sql_services = "SELECT * FROM service";
$result_services = $conn->query($sql_services);

$sql_vehicules = "SELECT * FROM vehicule";
$result_vehicules = $conn->query($sql_vehicules);

$conn->close();
?>

<!-- <!DOCTYPE html>
<html lang="fr"> -->

<!-- <head> -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<title>Ajouter une réservation</title>
<!-- </head> -->
<?php if (isset($message)): ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- <body> -->
<h1>Ajouter une réservation</h1>

<form id="createReservationForm" action="create_reservation.php" method="post">
    <div class="mb-3">
        <label for="service_id" class="form-label">Service</label>
        <select class="form-select" name="service_id" id="service_id">
            <?php while ($service = $result_services->fetch_assoc()): ?>
                <option value="<?php echo $service['id']; ?>">
                    <?php echo $service['nom']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="vehicule_id" class="form-label">Véhicule</label>
        <select class="form-select" name="vehicule_id" id="vehicule_id">
            <?php while ($vehicule = $result_vehicules->fetch_assoc()): ?>
                <option value="<?php echo $vehicule['id']; ?>" data-service-id="<?php echo $vehicule['service_id']; ?>"
                    style="display:none;">
                    <?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['immatriculation']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="date_debut" class="form-label">Date de début</label>
        <input type="datetime-local" class="form-control" name="date_debut" id="date_debut" required>
    </div>
    <div class="mb-3">
        <label for="date_fin" class="form-label">Date de fin</label>
        <input type="datetime-local" class="form-control" name="date_fin" id="date_fin" required>
    </div>
    <button type="submit" class="btn btn-primary">Créer une réservation</button>
</form>

<script src="../js/jquery-3.6.4.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $("#service_id").on("change", function () {
            var service_id = $(this).val();
            $("#vehicule_id").val("");
            $("#vehicule_id option").each(function () {
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
<!-- </body> -->

<!-- </html> -->