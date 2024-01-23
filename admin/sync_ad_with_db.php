<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files and establish a connection to the database
include_once '../includes/db.php';
define('ENCRYPTION_KEY', 'sn5vdq811512A/');
include_once '../includes/functions.php';




// Récupération des configurations LDAP depuis la base de données
$ldapConfig = fetchLdapConfig($conn);

if (!$ldapConfig) {
    die("Erreur: Impossible de récupérer la configuration LDAP.");
}

$ldap_server = $ldapConfig['ldap_host'];
$ldap_port = $ldapConfig['ldap_port'];
$ldap_user_dn = decrypt($ldapConfig['ldap_user_dn'], ENCRYPTION_KEY);
$ldap_password = decrypt($ldapConfig['ldap_password'], ENCRYPTION_KEY);
$ldap_base_dn = $ldapConfig['ldap_base_dn'];

// Connexion à LDAP
$ldapconn = ldap_connect($ldap_server, $ldap_port);

if (!$ldapconn) {
    /* die("Erreur: Impossible de se connecter au serveur LDAP."); */
    $response['success'] = false;
    $response['messages'][] = "Erreur: Impossible de se connecter au serveur LDAP.";
    echo json_encode($response);
    exit;
}

$bind = ldap_bind($ldapconn, $ldap_user_dn, $ldap_password);
if (!$bind) {
    /* die("Erreur: Impossible de se lier au serveur LDAP avec les identifiants fournis."); */
    $response['success'] = false;
    $response['messages'][] = "Erreur: Impossible de se lier au serveur LDAP avec les identifiants fournis.";
    echo json_encode($response);
    ldap_unbind($ldapconn);
    exit;
}

// Récupération des utilisateurs depuis l'AD et la base de données
$usersFromAD = fetchUsersFromAD($ldapconn, $ldap_base_dn);
if ($usersFromAD['error']) {
    // Gérer l'erreur ici
    $response['success'] = false;
/* $response['messages'][] = "Nombre d'utilisateurs récupérés de l'AD : " . count($usersFromAD); */
$response['messages'][] = "Erreur lors de la récupération des utilisateurs de l'AD.";
    echo json_encode($response);
    ldap_unbind($ldapconn);
    exit;
}


$usersFromDB = fetchUsersFromDB($conn);

// Initialisation d'un tableau pour les messages et la progression
$response = [
    'messages' => [],
    'success' => true,
    'progress' => 100
];
$response['messages'][] = "Nombre d'utilisateurs récupérés de l'AD : " . count($usersFromAD['data']);

// Parcourir les utilisateurs de l'AD et mettre à jour la base de données en conséquence
foreach ($usersFromAD as $userAD) {
    if (isset($userAD['samaccountname'])) {
        $sAMAccountName = $userAD['samaccountname'][0];
    
        if (existsInDB($conn, $sAMAccountName)) {
            // Ajouter le message au tableau de réponse
            
            // Mettre à jour l'utilisateur
            updateUserInDB($conn, $userAD);
            $response['messages'][] = "Mise à jour de l'utilisateur : $sAMAccountName";
            
        } else {
            // Ajouter le message au tableau de réponse
            // Insérer l'utilisateur
            insertUserInDB($conn, $userAD);
            $response['messages'][] = "Ajout de l'utilisateur : $sAMAccountName";
        }
    }
        // Mise à jour de la progression 
        $response['progress'] += (1 / count($usersFromAD)) * 100;
    }




// Enregistrement des informations de synchronisation
$date = new DateTime();
$lastSyncDate = $date->format('Y-m-d H:i:s');
$lastSyncStatus = $response['success'] ? "Succès" : "Échec";
$usersSynced = count($usersFromAD);



// Insérer ou mettre à jour les informations dans la table sync_info
$stmt = $conn->prepare("INSERT INTO sync_info (last_sync, sync_status, users_synced) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE last_sync = VALUES(last_sync), sync_status = VALUES(sync_status), users_synced = VALUES(users_synced)");
$formattedDate = $date->format('Y-m-d H:i:s');
$syncStatus = $response['success'] ? 'Succès' : 'Échec';
/* $usersSyncedCount = count($usersFromAD); */
$usersSyncedCount = count($usersFromAD['data']);

$stmt->bind_param("ssi", $formattedDate, $syncStatus, $usersSyncedCount);

$stmt->execute();
$stmt->close();


// Terminer le script en renvoyant le tableau en JSON
header('Content-Type: application/json');

echo json_encode($response);

exit;

ldap_unbind($ldapconn);
?>


