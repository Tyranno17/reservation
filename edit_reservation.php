<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';


if (!isset($_GET['id'])) {
    header('Location: calendrier.php');
    exit;
}

$reservation_id = $_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['id'];
    $etablissement_id = $_POST['etablissement_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $destination = $_POST['destination'];


    $sql = "UPDATE reservation SET etablissement_id = ?, vehicule_id = ?, date_debut = ?, date_fin = ?, destination = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissis", $etablissement_id, $vehicule_id, $date_debut, $date_fin, $destination, $reservation_id);
    $stmt->execute();

    header('Location: calendrier.php');
    exit;
}

$currentUserId = $_SESSION['sAMAccountName'];

$sql = "SELECT * FROM reservation WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();


    $etablissement_actuel_id = $reservation['etablissement_id'];
    $vehicule_actuel_id = $reservation['vehicule_id'];
    $date_debut = new DateTime($reservation['date_debut']);
    $date_fin = new DateTime($reservation['date_fin']);
    $destination = $reservation['destination'];
    $date_debut_input = $date_debut->format('Y-m-d\TH:i');
    $date_fin_input = $date_fin->format('Y-m-d\TH:i');
    $date_debut_formatted = $date_debut->format('d/m/Y H:i');
    $date_fin_formatted = $date_fin->format('d/m/Y H:i');
} else {
    // header('Location: calendrier.php');
    exit;
}

$sql_vehicules = "SELECT vehicule.*, etablissement.id as etablissement_id FROM vehicule JOIN etablissement ON vehicule.etablissement_id = etablissement.id";
$result_vehicules = $conn->query($sql_vehicules);


if ($result_vehicules === false) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

$sql_etablissements = "SELECT * FROM etablissement";
$result_etablissements = $conn->query($sql_etablissements);

if ($result_etablissements === false) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}


$stmt->close();
$conn->close();
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet">
<title>Modifier une réservation</title>

<?php if (isset($message)) : ?>
    <div class="alert alert-danger">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- <body> -->
<h1>Modifier une réservation</h1>

<form id="editReservationForm" action="edit_reservation.php?id=<?php echo $reservation_id; ?>" method="post">
    <!-- Ajoutez cette ligne pour inclure l'ID de la réservation -->
    <input type="hidden" name="id" value="<?php echo $reservation_id; ?>">
    <div class="mb-3">
        <label for="etablissement" class="form-label">Etablissement</label>
        <select class="form-select" name="etablissement_id" id="etablissement" onchange="filterVehiclesByEtablissement()">
            <!-- Options du etablissement -->
            <?php while ($etablissement = $result_etablissements->fetch_assoc()) : ?>
                <option value="<?php echo $etablissement['id']; ?>" <?php echo ($etablissement['id'] == $etablissement_actuel_id) ? 'selected' : ''; ?>><?php echo $etablissement['nom']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="vehicule_id" class="form-label">Véhicule</label>
        <select class="form-select" name="vehicule_id" id="vehicule_id">
            <!-- Options du véhicule -->
            <?php while ($vehicule = $result_vehicules->fetch_assoc()) : ?>
                <option value="<?php echo $vehicule['id']; ?>" data-etablissement-id="<?php echo $vehicule['etablissement_id']; ?>" <?php if ($vehicule['id'] == $reservation['vehicule_id'])
                                                                                                                            echo 'selected'; ?>>
                    <?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' - ' . $vehicule['immatriculation']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="date_debut" class="form-label">Date de début</label>
        <input type="text" class="form-control" name="date_debut" id="date_debut" value="<?php echo $date_debut_formatted; ?>" placeholder="Cliquez pour choisir la date et l'heure" data-toggle="flatpickr" required>
    </div>
    <div class="mb-3">
        <label for="date_fin" class="form-label">Date de fin</label>
        <input type="text" class="form-control" name="date_fin" id="date_fin" value="<?php echo $date_fin_formatted; ?>" placeholder="Cliquez pour choisir la date et l'heure" data-toggle="flatpickr" required>
    </div>
    <div class="mb-3">
        <label for="destination" class="form-label">Destination</label>
        <input type="text" class="form-control" id="destination" name="destination" value="<?php echo $reservation['destination']; ?>" placeholder="Veuillez indiquer votre destination, Pensez au covoiturage" required>
    </div>
</form>
<script src="js/custom.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/fr.min.js"></script>

<script>
    flatpickr('#date_debut', {
        enableTime: true,
        dateFormat: 'd/m/Y H:i',
        locale: 'fr',
        time_24hr: true,
        minuteIncrement: 15,
    });
    flatpickr('#date_fin', {
        enableTime: true,
        dateFormat: 'd/m/Y H:i',
        locale: 'fr',
        time_24hr: true,
        minuteIncrement: 15,
    });
</script>
<script>
    function filterVehiclesByEtablissement() {
        var etablissementSelect = document.getElementById("etablissement");
        var vehicleSelect = document.getElementById("vehicule_id"); // Modifié ici
        var selectedEtablissementId = etablissementSelect.value;

        for (var i = 0; i < vehicleSelect.options.length; i++) {
            var option = vehicleSelect.options[i];
            var etablissementId = option.getAttribute("data-etablissement-id");

            if (selectedEtablissementId === etablissementId) {
                option.style.display = "block";
            } else {
                option.style.display = "none";
            }
        }
        // Sélectionner le premier véhicule visible dans la liste
        for (var i = 0; i < vehicleSelect.options.length; i++) {
            var option = vehicleSelect.options[i];

            if (option.style.display !== "none") {
                vehicleSelect.value = option.value;
                break;
            }
        }
    }

    // Appliquer le filtrage initial pour afficher uniquement les véhicules du etablissement actuel
    filterVehiclesByEtablissement();
</script>
<script>
    var currentUserId = <?php echo $_SESSION['sAMAccountName']; ?>;
</script>