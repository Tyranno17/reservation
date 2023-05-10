<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: calendrier.php');
    exit;
}

$reservation_id = $_GET['id'];

$sql = "SELECT r.id, r.date_debut, r.date_fin, u.prenom, u.nom, v.marque, v.modele, v.immatriculation, s.nom AS service
        FROM reservation r
        INNER JOIN utilisateur u ON r.utilisateur_id = u.id
        INNER JOIN vehicule v ON r.vehicule_id = v.id
        INNER JOIN service s ON v.service_id = s.id
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reservation = $result->fetch_assoc();
    $vehicule_marque = $reservation['marque'];
    $vehicule_modele = $reservation['modele'];
    $vehicule_immatriculation = $reservation['immatriculation'];
    $service = $reservation['service'];
    $prenom = $reservation['prenom'];
    $nom = $reservation['nom'];

    $date_debut = new DateTime($reservation['date_debut']);
    $date_fin = new DateTime($reservation['date_fin']);
    $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
    $debut_fr = $fmt->format($date_debut);
    $fin_fr = $fmt->format($date_fin);
} else {
    header('Location: calendrier.php');
    exit;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Visualisation Modification</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- <h1>Voir une réservation ou modifier une réservation</h1>
    <p>ID de réservation:
        <?php echo $reservation['id']; ?>
    </p>
    <p>Utilisateur:
        <?php echo $prenom . ' ' . $nom; ?>
    </p>
    <p>Véhicule:
        <?php echo $vehicule_marque . ' ' . $vehicule_modele . ' - ' . $vehicule_immatriculation; ?>
    </p>
    <p>Service:
        <?php echo $service; ?>
    </p>
    <p>Date de début:
        <?php echo $debut_fr; ?>
    </p>
    <p>Date de fin:
        <?php echo $fin_fr; ?>
    </p> -->
    <div class="container my-5">
        <h1>Voir ou modifier une réservation</h1>
        <p>ID de réservation:
            <?php echo $reservation_id; ?>
        </p>
        <p>Réservataire:
            <?php echo $prenom . ' ' . $nom; ?>
        </p>
        <p>Service:
            <?php echo $service; ?>
        </p>
        <p>Véhicule:
            <?php echo $vehicule_marque . ' ' . $vehicule_modele . ' - ' . $vehicule_immatriculation; ?>
        </p>
        <p>Date de début:
            <?php echo $debut_fr; ?>
        </p>
        <p>Date de fin:
            <?php echo $fin_fr; ?>
        </p>
        <a href="#" onclick="openEditReservationModal(<?php echo $reservation['id']; ?>)">Modifier</a>

    </div>
    <script src="js/jquery-3.6.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>