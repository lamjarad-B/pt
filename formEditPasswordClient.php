<?php
require_once ("include/_connexion.php");
require_once "include/_functions.php";
session_start();
$email = $_SESSION["email"];
$pdo = getPDO();
$lang = getLang();

  if (isset($_POST['updatePass'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $repeatNewPassword = $_POST['repeatNewPassword'];

    $editPassword = editPassword($pdo, $email, 'client', $oldPassword, $newPassword, $repeatNewPassword);
  }
  if (isset($editPassword)) {
    header("Location: editPasswordClient.php?message=$editPassword&lang=$lang"); //Afficher le message d'erreur adéquat si il y a un ou des erreurs lors d'envoie du formulaire
    die();
  }
?>
