<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: calendrier.php');
    exit;
}

$reservation_id = $_GET['id'];

$sql = "SELECT r.id, r.date_debut, r.date_fin, r.destination, u.prenom, u.nom, v.marque, v.modele, v.immatriculation, e.nom AS etablissement, r.sAMAccountName
        FROM reservation r
        INNER JOIN utilisateur u ON r.sAMAccountName COLLATE utf8mb4_unicode_ci = u.sAMAccountName COLLATE utf8mb4_unicode_ci
        INNER JOIN vehicule v ON r.vehicule_id = v.id
        INNER JOIN etablissement e ON v.etablissement_id = e.id
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
    $etablissement = $reservation['etablissement'];
    $prenom = $reservation['prenom'];
    $nom = $reservation['nom'];
    $destination = $reservation['destination'];

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

<meta charset="UTF-8">
<title>Visualisation Modification</title>

<div data-creator-id="<?php echo $reservation['sAMAccountName']; ?>">
    <form id="viewReservationForm" action="view_reservation.php" method="post">
        <h1>Voir ou modifier une réservation</h1>
        <p id="errorMessage" class="alert alert-danger" style="display: none;"></p>
        <p>ID de réservation:
            <?php echo $reservation_id; ?>
        </p>
        <p>Réservataire:
            <?php echo $prenom . ' ' . $nom; ?>
        </p>
        <p>Etablissement:
            <?php echo $etablissement; ?>
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
        <p>Destination:
            <?php echo $destination; ?>
        </p>

</div>

</form>
</div>