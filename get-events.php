<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Récupérer les événements depuis la base de données
global $conn;
$stmt = $conn->prepare('
SELECT r.id, r.date_debut, r.date_fin, r.destination, u.prenom, u.nom, v.marque, v.modele, v.immatriculation, e.nom
                        FROM reservation r
                        INNER JOIN utilisateur u ON r.sAMAccountName COLLATE utf8mb4_unicode_ci = u.sAMAccountName COLLATE utf8mb4_unicode_ci
                        INNER JOIN vehicule v ON r.vehicule_id = v.id
                        INNER JOIN etablissement e ON v.etablissement_id = e.id
                        ');
$stmt->execute();
$result = $stmt->get_result();

// Préparer le tableau des événements pour le calendrier
$events = [];
while ($row = $result->fetch_assoc()) {
    $start = strtotime($row['date_debut']);
    $end = strtotime($row['date_fin']);
    $title = $row['prenom'] . ' ' . $row['nom'] . ' - ' . $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation'];
    $events[] = [
        'id' => $row['id'],
        'title' => $title,
        'start' => date('Y-m-d\TH:i:s', $start),
        'end' => date('Y-m-d\TH:i:s', $end),
        'extendedProps' => [
            // Ajoutez la ligne ci-dessous
            'reservation_id' => $row['id'],
            'reservation' => $row['destination'],
            //'user_id' => $row['sAMAccountName'], // Ajoutez cette ligne
            'etablissement' => $row['nom'],
            'vehicule' => $row['marque'] . ' ' . $row['modele'] . ' - ' . $row['immatriculation']
        ]
    ];
}
// Envoyer les événements au format JSON
header('Content-Type: application/json');
echo json_encode($events);
