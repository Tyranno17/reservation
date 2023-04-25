<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Récupérer les événements depuis la base de données
$stmt = $conn->prepare('SELECT r.date_debut, r.date_fin, u.prenom, u.nom, v.marque, v.modele, v.immatriculation
                        FROM reservation r
                        INNER JOIN utilisateur u ON r.utilisateur_id = u.id
                        INNER JOIN vehicule v ON r.vehicule_id = v.id');
$stmt->execute();
$result = $stmt->get_result();

// Préparer le tableau des événements pour le calendrier
$events = [];
while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['date_debut']);
    $end = strtotime($row['date_fin']);
    // $title = $row['prenom'] . ' ' . $row['nom'] . ' - ' . $row['marque'] . ' ' . $row['modele'];
    $title = $row['prenom'] . ' ' . $row['nom'] . ' - ' . $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation'];
    $events[] = [
        'title' => $title,
        'start' => date('Y-m-d\TH:i:s', $start),
        'end' => date('Y-m-d\TH:i:s', $end),
        'allDaySlot' => false
    ];
}

// Renvoyer les événements en JSON
header('Content-Type: application/json');
echo json_encode($events);
?>