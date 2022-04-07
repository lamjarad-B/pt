<?php
require "include/init_twig.php";
require_once ("include/_connexion.php");
require_once "../include/_functions.php";
require_once "include/_functionsBO.php";
$css = "styleManageReservation";
$script = "manageReservation";
$pdo = getPDO();
session_start();
$email = $_SESSION['email'];
if (!isset($_SESSION['email'])) { // rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté
  header('Location: connexionBO.php');
}
else {
  @$idReservation = $_POST['idReservation'];
  @$idCategorieChambre = $_POST['idCategorieChambre'];
  @$modification = $_POST['modification'];

  $utilisateur = $pdo->query("SELECT * FROM user WHERE email =  '$email'")->fetch();

//si on clique sur le bouton annuler
if(isset($_POST['delete'])){

  //On annule la réservation
  cancelResaBO($pdo, $idReservation);

  // Afficher un message de success d'annulation réservation
  $messageSucces = "Votre réservation a bien été annuler";
}

// Vérifier la disponibilité quand on veut modifier une réservation
if (isset($_POST['verify'])) {
  $fromDate = $_POST["CheckIn"];
  $toDate = $_POST["CheckOut"];
  $idCategorieChambre = $_POST["idCategorieChambre"];

  // Pour caculer le nombre de jours réservés
  $fromDate = new DateTime($fromDate);
  $toDate = new DateTime($toDate);
  $nbDay =  date_diff($fromDate, $toDate);
  $nbDay = $nbDay->format('%a');

  $fromDate = $fromDate->format('Y-m-d');
  $toDate = $toDate->format('Y-m-d');

  if (!empty($fromDate) && !empty($toDate)) {
      $query = "SELECT * FROM chambre
      LEFT JOIN categorie_chambre ON chambre.categorieChambreId = categorie_chambre.idCategorieChambre
      WHERE idChambre NOT IN ( SELECT chambreId FROM reservation_chambre WHERE dateArriver BETWEEN '$fromDate' AND '$toDate' OR dateDepart BETWEEN '$fromDate' AND  '$toDate') AND chambre.categorieChambreId = '$idCategorieChambre'";  //requete pour récupérer les chambres dispo
      $roomsCheck = $pdo->query($query)->fetch();
    }
  else {
    $errorMessage = "Veuillez entrez la date d'arriver et la date de départ svp";
  }
  echo json_encode([ 'dateArriver' => $fromDate, 'dateDepart' => $toDate, 'price' => $roomsCheck['tarifCategorieChambre']*$nbDay,'idReservation' => $idReservation,'idChambre' => $roomsCheck['idChambre']]);
  exit(); // Arrêter l'execution de la scripte

}
  // Fonction pour modifier reservation
  $updateResa = updateResa($pdo, $idReservation);
  // if (updateResa($pdo, $idReservation)) {
  //   $updateResaSuccess = "La réservation à été midifier avec succès";
  // }
}
// if (isset($_POST['verify']) && $_POST['verify'] == 1) {
//   echo json_encode([ 'dateArriver' => $fromDate, 'dateDepart' => $toDate, 'price' => $roomsCheck['tarifCategorieChambre']*$nbDay,'idReservation' => $idReservation,'idChambre' => $roomsCheck['idChambre']]);
//   exit(); // Arrêter l'execution de la scripte
//
// }
$reservations = getClientResaBO($pdo);
$connection = getConnectionText();
echo $twig->render('manageReservation.html.twig',
  	  array('css' => $css,
            'script' => $script,
            'connection' => $connection,
            'reservations' => $reservations,
            'modification' => $modification,
            'messageSucces' => @$messageSucces,
            'connection' => $connection,
            'roomsCheck' => @$roomsCheck,
            'idCategorieChambre' => $idCategorieChambre,
            'idReservation' => $idReservation,
            'modification' => $modification,
            'verify' => @$_POST['verify'],
            'nbDay' => @$nbDay,
            'dateArriver' => @$fromDate,
            'dateDepart' => @$toDate,
            'updateResa' => @$updateResa,
            'errorMessage' => @$errorMessage,

  				));
?>
