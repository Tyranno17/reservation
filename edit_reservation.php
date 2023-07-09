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
    $service_id = $_POST['service_id'];
    $vehicule_id = $_POST['vehicule_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $destination = $_POST['destination'];


    $sql = "UPDATE reservation SET service_id = ?, vehicule_id = ?, date_debut = ?, date_fin = ?, destination = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissis", $service_id, $vehicule_id, $date_debut, $date_fin, $reservation_id, $destination);
    $stmt->execute();

    header('Location: calendrier.php');
    exit;
}

$currentUserId = $_SESSION['utilisateur_id'];

$sql = "SELECT * FROM reservation WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();

    // Afficher les valeurs de $currentUserId et $reservation['utilisateur_id']
    // echo "Current User ID: ";
    // var_dump($currentUserId);
    // echo "Reservation Creator ID: ";
    // var_dump($reservation['utilisateur_id']);

    // Vérifier si l'utilisateur actuellement connecté est le créateur de la réservation

    // if ($currentUserId !== $reservation['utilisateur_id']) {
    //     // L'utilisateur actuellement connecté n'est pas le créateur de la réservation
    //     echo "Vous ne pouvez pas modifier cette réservation car vous n'êtes pas le créateur.";
    //     exit; // Arrête l'exécution du script
    // }


    $service_actuel_id = $reservation['service_id'];
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

$sql_vehicules = "SELECT vehicule.*, service.id as service_id FROM vehicule JOIN service ON vehicule.service_id = service.id";
$result_vehicules = $conn->query($sql_vehicules);


if ($result_vehicules === false) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

$sql_services = "SELECT * FROM service";
$result_services = $conn->query($sql_services);

if ($result_services === false) {
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
        <label for="service" class="form-label">Service</label>
        <select class="form-select" name="service_id" id="service" onchange="filterVehiclesByService()">
            <!-- Options du service -->
            <?php while ($service = $result_services->fetch_assoc()) : ?>
                <option value="<?php echo $service['id']; ?>" <?php echo ($service['id'] == $service_actuel_id) ? 'selected' : ''; ?>><?php echo $service['nom']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="vehicule_id" class="form-label">Véhicule</label>
        <select class="form-select" name="vehicule_id" id="vehicule_id">
            <!-- Options du véhicule -->
            <?php while ($vehicule = $result_vehicules->fetch_assoc()) : ?>
                <option value="<?php echo $vehicule['id']; ?>" data-service-id="<?php echo $vehicule['service_id']; ?>" <?php if ($vehicule['id'] == $reservation['vehicule_id'])
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
    function filterVehiclesByService() {
        var serviceSelect = document.getElementById("service");
        var vehicleSelect = document.getElementById("vehicule_id"); // Modifié ici
        var selectedServiceId = serviceSelect.value;

        for (var i = 0; i < vehicleSelect.options.length; i++) {
            var option = vehicleSelect.options[i];
            var serviceId = option.getAttribute("data-service-id");

            if (selectedServiceId === serviceId) {
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

    // Appliquer le filtrage initial pour afficher uniquement les véhicules du service actuel
    filterVehiclesByService();
</script>
<script>
    var currentUserId = <?php echo $_SESSION['utilisateur_id']; ?>;
</script>