<?php
require "include/init_twig.php";
require "include/_connexion.php";
include("include/_traduction.php");
require_once "include/_functions.php";
session_start();
$css = "styleRecapReservation";
$script = "recapReservation";
$pdo = getPDO();
$lang = getLang();

@$email = $_SESSION['email'];
$connection = getConnectionText($lang);
$langues = getIconLang($pdo);

if(isset($_POST['submit'])){

  $fromDate = $_POST["CheckIn"];
  $toDate = $_POST["CheckOut"];
  $idCategorieChambre = $_POST["idCategorieChambre"];
  $idRoom = $_POST["idChambre"];
  $roomType = $_POST["roomType"];
  $nbPerson = $_POST["nbPerson"];
  $nbChild = $_POST["nbChild"];
  $totalToPay = $_POST["totalToPay"];
  $nbDay = $_POST["nbDay"];
  @$dateCancel = date('Y-m-d', strtotime($fromDate. ' - 1 day'));

  $nom = strip_tags($_POST["nom"]);
  $prenom = strip_tags($_POST["prenom"]);
  $email = strip_tags($_POST["email"]);
  $mdp = $_POST["mdp"];
  $tel = strip_tags($_POST["tel"]);
  @$pays = strip_tags($_POST["pays"]);
  $message = strip_tags($_POST["message"]);




    $checkMail = $pdo->query("SELECT * FROM client WHERE email = '$email'")->fetch();// Pour vérifier si l'utilisateur existe déjà
    //Le nom ne doit pas etre vide et doit contenir que des alphabet muniscule et majiscule
    if(empty(trim($nom))){
      $fieldError = 'Le Nom invalide ou laissé vide';
    }
    //Le prenom ne doit pas etre vide et doit contenir que des alphabet muniscule et majiscule
    elseif(empty(trim($prenom))){
      $fieldError = 'Le Prénom invalide ou laissé vide';
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) || empty(trim($email))) {
      $fieldError = "L'email entré est invalide";
    }
    elseif($checkMail){
      $fieldError = "L'email saisi existe déjà, connectez vous pour effectué la réservation";
    }
    elseif (empty(trim($mdp))) {
      $fieldError = "Veuillez entrez un mot de passe svp";
    }
    elseif (empty(trim($tel))) {
      $fieldError = "Veuillez entrez un numéro de téléphone valide svp";
    }
    else {
      $client = $pdo->prepare("INSERT INTO client(nom, prenom, email, mdp, tel, pays ) VALUES(?, ?, ?, ?, ?, ?)");
      $client->bindValue(1, $nom, PDO::PARAM_STR);
      $client->bindValue(2, $prenom, PDO::PARAM_STR);
      $client->bindValue(3, $email, PDO::PARAM_STR);
      $client->bindValue(4, password_hash($mdp, PASSWORD_DEFAULT), PDO::PARAM_STR);
      $client->bindValue(5, $tel, PDO::PARAM_STR);
      $client->bindValue(6, $pays, PDO::PARAM_STR);
      $client->execute();
      $idClient = $pdo->lastInsertId();

      $reservation = $pdo->prepare("INSERT INTO reservation_chambre(chambreId, categorieChambreId, clientId, dateArriver, dateDepart, nbPerson, nbChild, requeteSpeciale) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
      $reservation->bindParam(1, $idRoom, PDO::PARAM_STR);
      $reservation->bindParam(2, $idCategorieChambre, PDO::PARAM_STR);
      $reservation->bindParam(3, $idClient, PDO::PARAM_STR);
      $reservation->bindParam(4, $fromDate, PDO::PARAM_STR);
      $reservation->bindParam(5, $toDate, PDO::PARAM_STR);
      $reservation->bindParam(6, $nbPerson, PDO::PARAM_STR);
      $reservation->bindParam(7, $nbChild, PDO::PARAM_STR);
      $reservation->bindParam(8, $message, PDO::PARAM_STR);
      $reservation->execute();

      $_SESSION["email"] = $email;

    }
  }


  if (isset($fieldError)) {
    $_SESSION['erreur'] = $_POST; //on ouvre la session pour sauvegarder les données en cas d'erreur et les afficher dans la page formReservation
    header("Location: formReservation.php?lang=$lang&message=$fieldError&nom=$nom&prenom=$prenom&email=$email&tel=$tel"); //Afficher le message d'erreur adéquat si il y a un ou des erreurs lors d'envoie du formulaire et garder les champs remplis
    die();
  }
// }


echo $twig->render('recapReservation.html.twig',
  	  array('css' => $css,
            'script' => $script,
            'lang' => $lang,
            'langues' => $langues,
            'connection' => $connection,
            'prenom' => @$prenom,
            'fromDate' => @$fromDate,
            'toDate' => @$toDate,
            'roomType' => @$roomType,
            'nbPerson' => @$nbPerson,
            'nbChild' => @$nbChild,
            'nbDay' => @$nbDay,
            'totalToPay' => @$totalToPay,
            'dateCancel' => @$dateCancel,
            //Pour la traduction
            'nav1' => @$traductions[$lang]["nav1"],
            'nav2' => @$traductions[$lang]["nav2"],
            'nav3' => @$traductions[$lang]["nav3"],
            'nav4' => @$traductions[$lang]["nav4"],
            'nav5' => @$traductions[$lang]["nav5"],
            'profil' => @$traductions[$lang]["profil"],
            'connection' => $connection,
            'Mentions' => @$traductions[$lang]["Mentions"],
            'politic' => @$traductions[$lang]["politic"],
            'condition' => @$traductions[$lang]["condition"],
            'adress' => @$traductions[$lang]["adress"],
            'recap' => @$traductions[$lang]["recap"],
            'recap1' => @$traductions[$lang]["recap1"],
            'recap2' => @$traductions[$lang]["recap2"],
            'recap3' => @$traductions[$lang]["recap3"],
            'recap4' => @$traductions[$lang]["recap4"],
            'recap5' => @$traductions[$lang]["recap5"],
            'recap6' => @$traductions[$lang]["recap6"],
            'ttNight' => @$traductions[$lang]["ttNight"],
            'ttPay' => @$traductions[$lang]["ttPay"],
            'print' => @$traductions[$lang]["print"],
            'thnxFor' => @$traductions[$lang]["thnxFor"],
            'reservation' => @$traductions[$lang]["reservation"],
            'dateA' => @$traductions[$lang]["dateA"],
            'dateD' => @$traductions[$lang]["dateD"]
  				));
?>
