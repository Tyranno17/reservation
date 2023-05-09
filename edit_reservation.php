<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: calendrier.php');
    exit;
}

$reservation_id = $_GET['id'];

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();

    $service_actuel_id = $reservation['service_id'];
    $vehicule_actuel_id = $reservation['vehicule_id'];
    $date_debut = new DateTime($reservation['date_debut']);
    $date_fin = new DateTime($reservation['date_fin']);
    $date_debut_input = $date_debut->format('Y-m-d\TH:i');
    $date_fin_input = $date_fin->format('Y-m-d\TH:i');

} else {
    header('Location: calendrier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['id'];
    $service_id = $_POST['service_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Vérifier si la date et l'horaire sont déjà pris
    $sql_check = "SELECT * FROM reservation WHERE id != ? AND service_id = ? AND vehicule_id = ? AND ((date_debut >= ? AND date_debut < ?) OR (date_fin > ? AND date_fin <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iiissss", $reservation_id, $service_id, $vehicule_id, $date_debut, $date_fin, $date_debut, $date_fin);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $reservation = $result->fetch_assoc();

        $service_actuel_id = $reservation['service_id'];
        $vehicule_actuel_id = $reservation['vehicule_id'];
        $date_debut = new DateTime($reservation['date_debut']);
        $date_fin = new DateTime($reservation['date_fin']);
        $date_debut_input = $date_debut->format('Y-m-d\TH:i');
        $date_fin_input = $date_fin->format('Y-m-d\TH:i');

    } else {
        header('Location: calendrier.php');
        exit;
    }
    // Mettre à jour la réservation

    $sql = "UPDATE reservation SET service_id = ?, vehicule_id = ?, date_debut = ?, date_fin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $service_id, $vehicule_id, $date_debut, $date_fin, $reservation_id);
    $stmt->execute();


}

$sql = "SELECT * FROM reservation WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();
} else {
    header('Location: calendrier.php');
    exit;
}

$sql_vehicules = "SELECT * FROM vehicule";
$result_vehicules = $conn->query($sql_vehicules);

$sql_services = "SELECT * FROM service";
$result_services = $conn->query($sql_services);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Modifier une réservation</title>
</head>
<?php if (isset($message)): ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<body>
    <h1>Modifier une réservation</h1>

    <form action="edit_reservation.php?id=<?php echo $reservation_id; ?>" method="post">
        <div class="mb-3">
            <label for="service" class="form-label">Service</label>
            <select class="form-select" name="service" id="service">
                <!-- Options du service -->
                <?php while ($service = $result_services->fetch_assoc()): ?>
                    <option value="<?php echo $service['id']; ?>" <?php echo ($service['id'] == $service_actuel_id) ? 'selected' : ''; ?>><?php echo $service['nom']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="vehicule_id" class="form-label">Véhicule</label>
            <select class="form-select" name="vehicule_id" id="vehicule_id">
                <!-- Options du véhicule -->
                <?php while ($vehicule = $result_vehicules->fetch_assoc()): ?>
                    <option value="<?php echo $vehicule['id']; ?>" <?php if ($vehicule['id'] == $reservation['vehicule_id'])
                           echo 'selected'; ?>>
                        <?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['immatriculation']; ?>
                    <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="date_debut" class="form-label">Date de début</label>
            <input type="datetime-local" class="form-control" name="date_debut" id="date_debut"
                value="<?php echo $date_debut_input; ?>" required>
        </div>
        <div class="mb-3">
            <label for="date_fin" class="form-label">Date de fin</label>
            <input type="datetime-local" class="form-control" name="date_fin" id="date_fin"
                value="<?php echo $date_fin_input; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Modifier la réservation</button>
    </form>

    <!-- <a href="calendrier.php">Retour au calendrier</a> -->
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>