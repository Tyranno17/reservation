<?php

require_once 'db.php';
require_once 'config.php';


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}


function checkUserLogged() {
    return isset($_SESSION['sAMAccountName']);
}

function checkUserRole($role) {
    return isset($_SESSION['utilisateur_role']) && $_SESSION['utilisateur_role'] == $role;
}


function getAllUsers()
{
    global $conn;
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

    $query = "SELECT * FROM utilisateur";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Erreur lors de la récupération des utilisateurs: " . mysqli_error($conn));
    }

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return $users;
}

function getReservationsByVehiculeId($vehicule_id)
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM reservations WHERE vehicule_id = ?");
    $stmt->bind_param("i", $vehicule_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $reservations = [];
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }

    $stmt->close();

    return $reservations;
}

function getEtablissements()
{
    global $conn;
    $sql = "SELECT * FROM etablissement";
    $result = mysqli_query($conn, $sql);
    $etablissements = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $etablissements;
}

function getVehicules()
{
    global $conn;
    $query = "SELECT * FROM vehicule";
    $result = $conn->query($query);
    $vehicules = [];
    while ($row = $result->fetch_assoc()) {
        $vehicules[] = $row;
    }
    return $vehicules;
}

function getReservationsByUserId($sAMAccountName)
{
    global $conn;

    $sql = "SELECT reservation.id, reservation.date_debut, reservation.date_fin, reservation.destination, 
                   etablissement.nom AS etablissement, 
                   CONCAT(vehicule.marque, ' ', vehicule.modele) AS vehicule, 
                   vehicule.immatriculation
            FROM reservation
            INNER JOIN vehicule ON reservation.vehicule_id = vehicule.id
            INNER JOIN etablissement ON vehicule.etablissement_id = etablissement.id
            WHERE reservation.sAMAccountName = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $sAMAccountName);
    $stmt->execute();

    $result = $stmt->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    //var_dump($reservations);
    return $reservations;
}

function addUser($prenom, $nom, $email, $hashed_password, $role)
{
    global $conn;
    // Assurez-vous que la valeur de $role est soit 'admin', soit 'user'
    if ($role !== 'admin' && $role !== 'user') {
        $role = 'user'; // Définir 'user' comme valeur par défaut si la valeur fournie n'est pas autorisée
    }
    // Vérifier si l'utilisateur existe déjà
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
        // L'utilisateur existe déjà, définir un message d'erreur
        $_SESSION['error_message'] = "Cet e-mail est déjà utilisé. Veuillez utiliser un autre e-mail.";
        return false;
    }
    // Insérer le nouvel utilisateur
    $sql = "INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sssss", $prenom, $nom, $email, $hashed_password, $role);
    if ($stmt->execute()) {
        return true;
    } else {
        $_SESSION['error_message'] = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
        return false;
    }

    if ($stmt->errno) {
        echo "Échec de l'exécution de l'instruction: " . $stmt->error;
    } else {

        $stmt->close();
    }
}

function getAllVehicules()
{
    global $conn;
    $sql = "SELECT * FROM vehicule";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

function getUserByEmail($email)
{
    global $conn;
    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }

    if ($stmt->errno) {
        echo "Échec de l'exécution de l'instruction: " . $stmt->error;
    } else {

        $stmt->close();
    }
}

//La fonction exécute ensuite la requête pour supprimer l'utilisateur de la base de données.
//Si l'exécution réussit, la fonction renvoie true, sinon, elle affiche un message d'erreur et renvoie false.
function deleteUser($sAMAccountName)
{
    global $conn;

    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
    $stmt->bind_param('s', $sAMAccountName);

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de la suppression de l'utilisateur : " . $stmt->error;
        return false;
    }
}

// Récupère un utilisateur par ID
function getUserById($sAMAccountName)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");

    if ($stmt) {
        $stmt->bind_param('s', $sAMAccountName);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
}

//La fonction vérifie si un mot de passe est fourni, et si c'est le cas, il met également à jour le mot de passe.
// Sinon, il met à jour uniquement les autres informations de l'utilisateur.
function updateUser($sAMAccountName, $prenom, $nom, $email, $password, $role)
{
    global $conn;

    // Vérifier si le mot de passe doit être mis à jour
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->bind_param('ssssss', $prenom, $nom, $email, $password_hash, $role, $sAMAccountName);
    } else {
        $stmt = $conn->prepare("UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param('sssss', $prenom, $nom, $email, $role, $sAMAccountName);
    }

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de la mise à jour de l'utilisateur : " . $stmt->error;
        return false;
    }
}

function addVehicle($marque, $modele, $annee, $immatriculation, $etablissement_id)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO vehicule (marque, modele, annee, immatriculation, etablissement_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $marque, $modele, $annee, $immatriculation, $etablissement_id);
    return $stmt->execute();
}

function updateVehicle($id, $marque, $modele, $annee, $immatriculation, $etablissement_id)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE vehicule SET marque = ?, modele = ?, annee = ?, immatriculation = ?, etablissement_id = ? WHERE id = ?");
    $stmt->bind_param("ssisii", $marque, $modele, $annee, $immatriculation, $etablissement_id, $id);
    return $stmt->execute();
}

function deleteVehicle($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM vehicule WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function getVehicleById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT v.*, e.nom as etablissement FROM vehicule v LEFT JOIN etablissement e ON v.etablissement_id = e.id WHERE v.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAllEtablissements()
{
    global $conn;

    $result = $conn->query("SELECT * FROM etablissement");

    $etablissements = [];
    while ($row = $result->fetch_assoc()) {
        $etablissements[] = $row;
    }

    return $etablissements;
}

// Ajouter un etablissement
function addEtablissement($nom, $description)
{
    global $conn;

    $query = "INSERT INTO etablissement (nom, description) VALUES (?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nom, $description);

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de l'ajout du etablissement : " . $stmt->error;
        return false;
    }
}

// Récupérer un etablissement par son ID
function getEtablissementById($etablissement_id)
{
    global $conn;
    $sql = "SELECT * FROM etablissement WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $etablissement_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $etablissement = mysqli_fetch_assoc($result);
            return $etablissement;
        }
    }

    return false;
}

// Mettre à jour un etablissement
function updateEtablissement($id, $nom, $description)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE etablissement SET nom = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nom, $description, $id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

    if ($stmt->errno) {
        echo "Échec de l'exécution de l'instruction: " . $stmt->error;
    } else {

        $stmt->close();
    }
}

// Supprimer un etablissement
function deleteEtablissement($id)
{
    global $conn;

    $stmt = $conn->prepare("DELETE FROM etablissement WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }

    if ($stmt->errno) {
        echo "Échec de l'exécution de l'instruction: " . $stmt->error;
    } else {

        $stmt->close();
    }
}

function insertReservation($sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination)
{
    global $conn;

    $query = "INSERT INTO reservation (sAMAccountName, vehicule_id, date_debut, date_fin, destination)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisss", $sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination);

    if ($stmt->execute()) {
        echo "Insertion réussie"; // Ajout d'un message de débogage
        return true;
    } else {
        // Affiche l'erreur
        echo "Erreur lors de l'insertion de la réservation : " . $stmt->error; // Affiche l'erreur
        return false;
    }
}


function isVehicleAvailable($vehicule_id, $date_debut, $date_fin, $sAMAccountName)
{
    global $conn;

    $date_debut = mysqli_real_escape_string($conn, $date_debut);
    $date_fin = mysqli_real_escape_string($conn, $date_fin);
    $vehicule_id = (int) $vehicule_id;
    $sAMAccountName = (int) $sAMAccountName;

    $query = "SELECT COUNT(*) as nb_reservations
              FROM reservation
              WHERE vehicule_id = $vehicule_id
              AND sAMAccountName != $sAMAccountName
              AND (
                  ('$date_debut' >= date_debut AND '$date_debut' < date_fin)
                  OR ('$date_fin' > date_debut AND '$date_fin' <= date_fin)
                  OR (date_debut >= '$date_debut' AND date_debut < '$date_fin')
                  OR (date_fin > '$date_debut' AND date_fin <= '$date_fin')
              )";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Erreur lors de la requête : " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $nb_reservations = $row['nb_reservations'];
    return $nb_reservations == 0;
}

function getUserIdFromSession()
{
    if (isset($_SESSION['sAMAccountName'])) {
        return $_SESSION['sAMAccountName'];
    } else {
        return false;
    }
}

function checkVehicleAvailability($vehicule_id, $date_debut, $date_fin)
{
    global $conn;
    $sql = "SELECT COUNT(*) as total_reservations
            FROM reservation
            WHERE vehicule_id = ? AND (
                (date_debut <= ? AND date_fin >= ?) OR
                (date_debut <= ? AND date_fin >= ?) OR
                (date_debut >= ? AND date_fin <= ?)
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $vehicule_id, $date_debut, $date_debut, $date_fin, $date_fin, $date_debut, $date_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total_reservations'] == 0;
}

function checkExistingReservation($sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination)
{
    global $conn;

    $query = "SELECT COUNT(*) FROM reservation WHERE sAMAccountName = ? AND vehicule_id = ? AND date_debut = ? AND date_fin = ? AND destination = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisss", $sAMAccountName, $vehicule_id, $date_debut, $date_fin, $destination);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();

    return $count > 0;
}

function formatDateTime($date)
{
    return date('d/m/Y H:i', strtotime($date));
}

function formatDateToFrench($date)
{
    $tempDate = new DateTime($date);
    return $tempDate->format('d-m-Y H:i');
}


// Fonction pour récupérer les etablissements
function get_etablissements($conn)
{
    $sql = "SELECT * FROM etablissements";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $etablissements = array();
        while ($row = $result->fetch_assoc()) {
            $etablissements[] = $row;
        }
        return $etablissements;
    } else {
        return false;
    }
}

// Fonction pour récupérer les véhicules
function get_vehicules($conn)
{
    $sql = "SELECT * FROM vehicules";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $vehicules = array();
        while ($row = $result->fetch_assoc()) {
            $vehicules[] = $row;
        }
        return $vehicules;
    } else {
        return false;
    }
}

// Fonction pour récupérer une réservation
function get_reservation($conn, $reservation_id)
{
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}


function check_reservation($conn, $etablissement_id, $vehicule_id, $date_debut, $date_fin)
{
    $sql_check = "SELECT * FROM reservation WHERE etablissement_id = ? AND vehicule_id = ? AND ((date_debut >= ? AND date_debut < ?) OR (date_fin > ? AND date_fin <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iissss", $etablissement_id, $vehicule_id, $date_debut, $date_fin, $date_debut, $date_fin);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    return $result_check->num_rows > 0;
}

function getUserBySAMAccountName($sAMAccountName) {
    global $conn;  // Supposant que $conn est votre connexion à la base de données avec mysqli

    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE sAMAccountName = ? LIMIT 1");
    $stmt->bind_param("s", $sAMAccountName);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}



function createUserWithSAMAccountName($prenom, $nom, $email, $sAMAccountName, $role = 'user') {
    global $conn;

    $query = "INSERT INTO utilisateur (prenom, nom, email, sAMAccountName, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Liaison des paramètres à la requête préparée
    $stmt->bind_param('sssss', $prenom, $nom, $email, $sAMAccountName, $role);

    return $stmt->execute();
}




function getLDAPConfig()
 {
    global $conn;
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

    
    $query = "SELECT * FROM ldap_config LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        return mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}


function encrypt($data, $key) {
    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    return base64_encode($iv.$hmac.$ciphertext_raw);
}

function decrypt($data, $key) {
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac)) { // Compare les deux hash (les deux HMAC) pour s'assurer de l'intégrité des données
        return $decrypted;
    }
    return false; // Retourne false en cas d'échec du déchiffrement
}


function fetchUsersFromAD($ldapconn, $ldap_base_dn) {
    $filter = "(objectClass=user)";
    $attributes = array("samaccountname", "mail", "givenname", "sn");
    $result = ldap_search($ldapconn, $ldap_base_dn, $filter, $attributes);

    if (!$result) {
        return ['error' => true, 'message' => "Erreur lors de la recherche LDAP : " . ldap_error($ldapconn)];
    }

    $entries = ldap_get_entries($ldapconn, $result);
    return ['error' => false, 'data' => $entries];
}


function fetchUsersFromDB($conn) {
    $sql = "SELECT * FROM utilisateur";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return ['error' => true, 'message' => "Erreur de requête sur la base de données"];
    }

    $users = [];
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    return ['error' => false, 'data' => $users];
}


    function existsInDB($conn, $sAMAccountName) {
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE samaccountname = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $sAMAccountName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $count > 0;
}

function updateUserInDB($conn, $userData) {
    // Exemple de mise à jour de certains champs de l'utilisateur
    $stmt = $conn->prepare("UPDATE utilisateur SET prenom=?, nom=?, email=? WHERE sAMAccountName=?");

    // Remplacer par les champs appropriés de $userData
    $prenom = $userData['prenom']; // Assurez-vous que ces champs existent dans votre $userData
    $nom = $userData['nom'];
    $email = $userData['email'];
    $sAMAccountName = $userData['samaccountname'][0];

    $stmt->bind_param("ssss", $prenom, $nom, $email, $sAMAccountName);

    $stmt->execute();
    $stmt->close();
}
function insertUserInDB($conn, $userData) {
    // Exemple d'insertion dans la table utilisateur
    $stmt = $conn->prepare("INSERT INTO utilisateur (prenom, nom, email, sAMAccountName) VALUES (?, ?, ?, ?)");

    // Remplacer par les champs appropriés de $userData
    $prenom = $userData['prenom']; // Assurez-vous que ces champs existent dans votre $userData
    $nom = $userData['nom'];
    $email = $userData['email'];
    $sAMAccountName = $userData['samaccountname'][0];

    $stmt->bind_param("ssss", $prenom, $nom, $email, $sAMAccountName);

    $stmt->execute();
    $stmt->close();
}



function existsInAD($userADs, $dbUserSAM)
 {
    global $conn;
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}
    foreach ($userADs as $userAD) {
        if ($userAD['samaccountname'][0] == $dbUserSAM) {
            return true;
        }
    }
    return false;
}

function setUserAsInactive($conn, $sAMAccountName)
 {
    global $conn;
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}
    // Marquez l'utilisateur comme inactif
}


function fetchLdapConfig($conn)
 {
    global $conn;
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}
    $sql = "SELECT * FROM ldap_config LIMIT 1";  // Nous supposons qu'il n'y a qu'une seule configuration LDAP
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function authenticateWithAD($username, $password, $ldapConfig) {
    // Connectez-vous au serveur LDAP
    $ldapConn = ldap_connect($ldapConfig['ldap_host'], (int)$ldapConfig['ldap_port']);
    if (!$ldapConn) {
        return false;
    }

    // Tentez de vous lier avec les informations d'identification de l'utilisateur
    // $user_dn = $username . "@" . $ldapConfig['ldap_domain']; // Supposons que vous stockez le domaine complet (ex. "domaine.local") dans la configuration LDAP
    $user_dn = $username . "@escale.local";
    $ldapBind = @ldap_bind($ldapConn, $user_dn, $password);

    // Fermez la connexion
    ldap_close($ldapConn);

    return $ldapBind;
}


function getUserRoleFromDB($conn, $sAMAccountName) {
    // Vérifier la connexion à la base de données
    if (!$conn) {
        die("Erreur de connexion à la base de données.");
    }

    // Préparer la requête SQL
    $sql = "SELECT role, sAMAccountName FROM utilisateur WHERE sAMAccountName = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Lier les paramètres et exécuter la requête
    mysqli_stmt_bind_param($stmt, "s", $sAMAccountName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Récupérer le rôle de l'utilisateur
    $user = mysqli_fetch_assoc($result);
    
    // Fermer le statement
    mysqli_stmt_close($stmt);

    // Vérifier si un utilisateur a été trouvé
    if ($user) {
        return $user;
    } else {
        return false;
    }
}

function getEmailFromDB($conn, $sAMAccountName) {
    // Vérifier la connexion à la base de données
    if (!$conn) {
        die("Erreur de connexion à la base de données.");
    }

    // Préparer la requête SQL
    $sql = "SELECT email FROM utilisateur WHERE sAMAccountName = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Si la préparation échoue, retourner une erreur
    if (!$stmt) {
        die("Erreur de préparation de la requête: " . mysqli_error($conn));
    }

    // Lier les paramètres et exécuter la requête
    mysqli_stmt_bind_param($stmt, "s", $sAMAccountName);
    mysqli_stmt_execute($stmt);

    // Stocker le résultat et récupérer l'email
    mysqli_stmt_bind_result($stmt, $email);
    mysqli_stmt_fetch($stmt);

    // Fermer le statement et retourner l'email
    mysqli_stmt_close($stmt);
    return $email;
}

