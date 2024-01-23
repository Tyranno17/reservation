<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
define('ENCRYPTION_KEY', 'sn5vdq811512A/');
require_once '../includes/functions.php';


// Récupération des informations de synchronisation
$stmt = $conn->prepare("SELECT last_sync, sync_status, users_synced FROM sync_info ORDER BY id DESC LIMIT 1");
$stmt->execute();
$syncInfo = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Récupération de la liste des utilisateurs
$stmt = $conn->prepare("SELECT * FROM utilisateur");
$stmt->execute();
$result = $stmt->get_result();
$utilisateurs = [];
while ($row = $result->fetch_assoc()) {
    $utilisateurs[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">


</head>
<?php include 'header.php'; ?>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Synchronisation LDAP</h2>
        <?php if (isset($message)) { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <!-- Informations de synchronisation -->
        <p id="derniere_synchronisation">Dernière synchronisation : Chargement...</p>
        <p id="statut_synchronisation">Statut de la dernière synchronisation : Chargement...</p>
        <p id="utilisateurs_synchronises">Utilisateurs synchronisés : Chargement...</p>

        <!-- Bouton de synchronisation -->
        <button type="button" class="btn btn-primary" id="start-sync" onclick="startSync();">Démarrer la Synchronisation</button>


        <!-- Liste des utilisateurs -->
        <div class="table-responsive">
            <!-- ... Tableau des utilisateurs ... -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date de création</th>
                        <th>sAMAccountName</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['role']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['date_creation']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['sAMAccountName']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['status']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <script>
            // Fonction de synchronisation
            function startSync() {
                $.ajax({
                    url: 'sync_ad_with_db.php',
                    type: 'GET',
                    success: function(response) {
                        alert('Synchronisation effectuée.');
                        updateSyncData(); // Mise à jour des données après la synchronisation
                    },
                    error: function() {
                        alert('Une erreur est survenue lors de la synchronisation.');
                    }
                });
            }

            // Fonction pour actualiser les données de synchronisation
            function updateSyncData() {
                $.ajax({
                    url: 'get_sync_info.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#derniere_synchronisation').text('Dernière synchronisation : ' + data.last_sync);
                        $('#statut_synchronisation').text('Statut de la dernière synchronisation : ' + data.sync_status);
                        $('#utilisateurs_synchronises').text('Utilisateurs synchronisés : ' + data.users_synced);
                    },
                    error: function() {
                        console.error('Erreur lors de la récupération des données de synchronisation.');
                    }
                });
            }
        </script>

        <?php include 'footer.php'; ?>
        <!-- Bootstrap et autres scripts JS -->
        <script src="../js/jquery-3.6.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>

    </div>
</body>

</html>