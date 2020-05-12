<?php
require_once "mysql.php";
require_once "functions.inc.php";
?>

<?php
$sql = "SELECT * FROM website LIMIT 1"; // on séléctionne tout les paramètres du site
$result = $db->query($sql);
$result->setFetchMode(PDO::FETCH_ASSOC);
$config = $result->fetch();

  if ($config["maintenance"] == 1){ // si la maintenance est activée
    header( "Refresh:0; url=maintenance.php", true, 303); // redirection direct !
  }
?>
<div>
<!DOCTYPE html>
<html lang="fr" dir="ltr" style="height:auto;">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Escalade - Projet BDD</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sortable.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
  </head>
  <body>
<div id="header">
  <div class="right_head_img"></div>
  <div class="left_head_img"></div>
  <div id="logo"><a href="./index.php"><?php echo $config["titre"]; ?></a></div>

</div>



<nav>
<a id="resp-menu" class="responsive-menu" href="#"><i class="fas fa-bars"></i> Menu</a>
   <ul class="menu" style="padding-left: 0;">
   <li><a href="index.php"><i class="fa fa-home"></i> ACCUEIL</a>
   <ul class="sub-menu">
   <li><a href="aide.php">Aide</a></li>
   <li><a href="fonctionnalites.php">Fonctionnalités</a></li>
   </ul>
   </li>
   <?php
   session_start();
   if(!isset($_SESSION['user_login'])) // si l'utilisateur est pas LOG
     {
       echo"
      <li><a  href=\"login.php\"><i class=\"fa fa-user\"></i> CONNEXION</a></li>
      <li><a  href=\"register.php\"><i class=\"fa fa-user-plus\"></i> INSCRIPTION</a></li>";
    } else if (isset($_SESSION['user_login'])) { // sinon :

   $id = $_SESSION['user_login'];

   $select_user_info = $db->prepare("SELECT * FROM adherent WHERE codeAdherent=:uid"); // Je séléctionne les paramètres de l'utilisateur
   $select_user_info->execute(array(":uid"=>$id));

   $row=$select_user_info->fetch(PDO::FETCH_ASSOC);

   if (!is_null($row["codeSite"])){ // S'il a un club, j'affiche un onglet "Mon club"
     $codeClub = $row["codeSite"];
     echo"<li><a  href=\"view_club.php?id=$codeClub\"><i class=\"fas fa-user-check\"></i> MON CLUB</a></li>";
   }

   echo"
   <li><a  href=\"clubs.php\"><i class=\"fas fa-hiking\"></i> LES CLUBS</a></li>
   <li><a  href=\"requests.php\"><i class=\"fas fa-folder-open\"></i> MES DEMANDES</a></li>
   <li style=\"float:right;\"><a href=\"#\" style=\"text-transform:uppercase;\"><i class=\"fas fa-user-cog\"></i> "; echo $row['nomAdherent']; echo" "; echo$row['prenomAdherent']; echo"</a>
    <ul style=\"left:unset;right:0px;\">";
    if ($row["is_admin"] == 1){
      echo "<li><a href=\"./panel\"><FONT color=\"red\"><b>Panel administrateur</b></FONT></a></li>";
    }
    echo"
    <li><a href=\"settings.php\">Mes paramètres</a></li>
    <li><a href=\"#\" class=\"modal-trigger\" data-modal=\"modal-name\">Déconnexion</a></li>
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
      <button onclick=\"location.href='logout.php';\" class=\"close-modal\"  style=\"text-align:center;background-color: #256363;border: 2px solid #184242;\">Se déconnecter</button>
    </div>
  </div>
</div>


   ";
 }
   ?>
 </ul>
  </nav>
