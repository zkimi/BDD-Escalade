<?php
require_once "../includes/mysql.php";
require_once "../includes/functions.inc.php";
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr" style="height:auto;">
  <head>
    <meta charset="utf-8">
    <title>Escalade - Panel Admin</title>
    <link rel="stylesheet" href="../css/body.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/sortable.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
  </head>
  <body>
<div id="header">
  <div class="right_head_img"></div>
  <div class="left_head_img"></div>
  <div id="logo"><a href="./index.php"><FONT COLOR="red">Panel administrateur</FONT></a></div>

</div>



<nav>
<a id="resp-menu" class="responsive-menu" href="#"><i class="fas fa-bars"></i> Menu</a>
   <ul class="menu" style="padding-left: 0;">
   <li><a href=".././index.php"><i class="fa fa-home"></i> RETOUR SUR LE SITE</a>

   </li>
   <?php
   session_start();

   $id = $_SESSION['user_login'];

   $select_stmt = $db->prepare("SELECT * FROM adherent WHERE codeAdherent=:uid");
   $select_stmt->execute(array(":uid"=>$id));

   $row=$select_stmt->fetch(PDO::FETCH_ASSOC);
   if(isset($_SESSION['user_login']))
   {
   echo"
   <li><a href=\"#\"><i class=\"fa fa-bars\"></i> ACTIONS ADMINISTRATEUR</a>
   <ul class=\"sub-menu\" style=\"width:250px;\">
   <li><a  href=\"edit_website.php\"><i class=\"fa fa-edit\"></i> GÉRER LE SITE</a></li>
   <li><a  href=\"users.php\"><i class=\"fa fa-users\"></i> GÉRER LES UTILISATEURS</a></li>
   <li><a  href=\"requests.php\"><i class=\"fa fa-folder-open\"></i> GÉRER LES DEMANDES</a></li>
   <li><a  href=\"clubs.php\"><i class=\"fa fa-hiking\"></i> GÉRER LES CLUBS</a></li>
   </ul>
   </li>
   <li style=\"float:right;\"><a href=\"#\"><i class=\"fas fa-user-cog\"></i> "; echo $row['nomUtilisateur']; echo"</a>
    <ul style=\"left:unset;right:0px;\">
    <li><a href=\".././logout.php\" class=\"modal-trigger\" data-modal=\"modal-name\">Déconnexion</a></li>
    </ul>
   </li>

   <div class=\"modal\" id=\"modal-name\">
  <div class=\"modal-sandbox\"></div>
  <div class=\"modal-box\">
    <div class=\"modal-header\">
      <div class=\"close-modal\">&#10006;</div>
      <h1>Êtes-vous sûr de vouloir vous déconnecter ?</h1>
    </div>
    <div class=\"modal-body\">
      <p>Si vous vous déconnectez maintenant, vous perdrez tout le travail non-enregistré sur votre compte.</p>
      <p>Cependant, si vous avez bien pris soin d'avoir enregistré votre travail, vous pouvez procéder à la déconnexion.</p>
        <br />
      <button onclick=\"location.href='.././logout.php';\" class=\"close-modal\"  style=\"text-align:center;background-color: #256363;border: 2px solid #184242;\">Se déconnecter</button>
    </div>
  </div>
</div>


   ";
 }
   ?>
 </ul>
  </nav>
