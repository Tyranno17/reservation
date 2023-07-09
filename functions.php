<?php

require_once 'db.php';
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

function checkUserLogged()
{
    return isset($_SESSION['utilisateur_id']) && !empty($_SESSION['utilisateur_id']);
}

function checkUserRole($role = null)
{
    if (!isset($_SESSION['utilisateur_id']) || !isset($_SESSION['utilisateur_role'])) {
        return false;
    }

    if ($role !== null && $_SESSION['utilisateur_role'] != $role) {
        return false;
    }

    return true;
}

function getAllUsers()
{
    global $conn;

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

function getServices()
{
    global $conn;
    $sql = "SELECT * FROM service";
    $result = mysqli_query($conn, $sql);
    $services = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $services;
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

function getReservationsByUserId($utilisateur_id)
{
    global $conn;

    $sql = "SELECT reservation.id, reservation.date_debut, reservation.date_fin, reservation.destination 
                   service.nom AS service, 
                   CONCAT(vehicule.marque, ' ', vehicule.modele) AS vehicule, 
                   vehicule.immatriculation
            FROM reservation
            INNER JOIN vehicule ON reservation.vehicule_id = vehicule.id
            INNER JOIN service ON vehicule.service_id = service.id
            WHERE reservation.utilisateur_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $utilisateur_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $reservations = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

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
function deleteUser($utilisateur_id)
{
    global $conn;

    $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id = ?");
    $stmt->bind_param('i', $utilisateur_id);

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de la suppression de l'utilisateur : " . $stmt->error;
        return false;
    }
}

// Récupère un utilisateur par ID
function getUserById($utilisateur_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");

    if ($stmt) {
        $stmt->bind_param('i', $utilisateur_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
}

//La fonction vérifie si un mot de passe est fourni, et si c'est le cas, il met également à jour le mot de passe.
// Sinon, il met à jour uniquement les autres informations de l'utilisateur.
function updateUser($utilisateur_id, $prenom, $nom, $email, $password, $role)
{
    global $conn;

    // Vérifier si le mot de passe doit être mis à jour
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, mot_de_passe = ?, role = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $prenom, $nom, $email, $password_hash, $role, $utilisateur_id);
    } else {
        $stmt = $conn->prepare("UPDATE utilisateur SET prenom = ?, nom = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $prenom, $nom, $email, $role, $utilisateur_id);
    }

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de la mise à jour de l'utilisateur : " . $stmt->error;
        return false;
    }
}

function addVehicle($marque, $modele, $annee, $immatriculation, $service_id)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO vehicule (marque, modele, annee, immatriculation, service_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $marque, $modele, $annee, $immatriculation, $service_id);
    return $stmt->execute();
}

function updateVehicle($id, $marque, $modele, $annee, $immatriculation, $service_id)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE vehicule SET marque = ?, modele = ?, annee = ?, immatriculation = ?, service_id = ? WHERE id = ?");
    $stmt->bind_param("ssisii", $marque, $modele, $annee, $immatriculation, $service_id, $id);
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
    $stmt = $conn->prepare("SELECT v.*, s.nom as service FROM vehicule v LEFT JOIN service s ON v.service_id = s.id WHERE v.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAllServices()
{
    global $conn;

    $result = $conn->query("SELECT * FROM service");

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    return $services;
}

// Ajouter un service
function addService($nom, $description)
{
    global $conn;

    $query = "INSERT INTO service (nom, description) VALUES (?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nom, $description);

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Erreur lors de l'ajout du service : " . $stmt->error;
        return false;
    }
}

// Récupérer un service par son ID
function getServiceById($service_id)
{
    global $conn;
    $sql = "SELECT * FROM service WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $service_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $service = mysqli_fetch_assoc($result);
            return $service;
        }
    }

    return false;
}

// Mettre à jour un service
function updateService($id, $nom, $description)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE service SET nom = ?, description = ? WHERE id = ?");
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

// Supprimer un service
function deleteService($id)
{
    global $conn;

    $stmt = $conn->prepare("DELETE FROM service WHERE id = ?");
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

function insertReservation($utilisateur_id, $vehicule_id, $date_debut, $date_fin, $destination)
{
    global $conn;

    $query = "INSERT INTO reservation (utilisateur_id, vehicule_id, date_debut, date_fin, destination)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $utilisateur_id, $vehicule_id, $date_debut, $date_fin, $destination);

    if ($stmt->execute()) {
        echo "Insertion réussie"; // Ajout d'un message de débogage
        return true;
    } else {
        // Affiche l'erreur
        echo "Erreur lors de l'insertion de la réservation : " . $stmt->error; // Affiche l'erreur
        return false;
    }
}


function isVehicleAvailable($vehicule_id, $date_debut, $date_fin, $utilisateur_id)
{
    global $conn;

    $date_debut = mysqli_real_escape_string($conn, $date_debut);
    $date_fin = mysqli_real_escape_string($conn, $date_fin);
    $vehicule_id = (int) $vehicule_id;
    $utilisateur_id = (int) $utilisateur_id;

    $query = "SELECT COUNT(*) as nb_reservations
              FROM reservation
              WHERE vehicule_id = $vehicule_id
              AND utilisateur_id != $utilisateur_id
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
    if (isset($_SESSION['utilisateur_id'])) {
        return $_SESSION['utilisateur_id'];
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

function checkExistingReservation($utilisateur_id, $vehicule_id, $date_debut, $date_fin, $destination)
{
    global $conn;

    $query = "SELECT COUNT(*) FROM reservation WHERE utilisateur_id = ? AND vehicule_id = ? AND date_debut = ? AND date_fin = ? AND destination = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $utilisateur_id, $vehicule_id, $date_debut, $date_fin, $destination);
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


// Fonction pour récupérer les services
function get_services($conn)
{
    $sql = "SELECT * FROM services";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $services = array();
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        return $services;
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


function check_reservation($conn, $service_id, $vehicule_id, $date_debut, $date_fin)
{
    $sql_check = "SELECT * FROM reservation WHERE service_id = ? AND vehicule_id = ? AND ((date_debut >= ? AND date_debut < ?) OR (date_fin > ? AND date_fin <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("iissss", $service_id, $vehicule_id, $date_debut, $date_fin, $date_debut, $date_fin);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    return $result_check->num_rows > 0;
}



// function sendEmail($userEmail, $subject, $message)
// {

//     $mail = new PHPMailer(true);
//     $mail->SMTPDebug = SMTP::DEBUG_SERVER;


//     try {
//         // Paramètres du serveur
//         $mail->SMTPDebug = 2;
//         $mail->isSMTP();
//         $mail->Host = ['mail.gandi.net'];
//         $mail->SMTPAuth = true;
//         $mail->Username = ['copieur@associationlescale.fr'];
//         $mail->Password = ['#Admin1234+'];
//         $mail->SMTPSecure = 'tls';
//         $mail->Port = ['587'];

//         // Destinataires
//         $mail->setFrom('from@example.com', 'Mailer');
//         $mail->addAddress($userEmail);

//         // Contenu
//         $mail->isHTML(true);
//         $mail->Subject = $subject;
//         $mail->Body = $message;
//         $mail->AltBody = strip_tags($message);

//         $mail->send();
//         echo 'Message has been sent';
//     } catch (Exception $e) {
//         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
//     }
// }
