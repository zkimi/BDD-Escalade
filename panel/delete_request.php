<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }

  if (!isset($_GET["id"])){ // si id est pas init
    echo "&nbsp;Vous devez sélectionner une demande.";
    die;
  } else if (!is_numeric($_GET["id"])) { // si id contient autre que des chiffres
    echo "&nbsp;Merci de saisir une demande valide.";
    die;
  }
  else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucun utilisateur.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT * FROM demandes WHERE codeDemande=$id LIMIT 1"; // on recup les infos de la demande de l'ID saisi
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si zéro lignes
    echo "&nbsp;La demande sélectionnée n'existe pas";
    die;
  }
  }
  ?>

  <style>
  #box{
    width: 45%;
    background-color: white;
    border-radius:5px;
    border:1px solid black;
    padding:5px;
  }
  </style>

<div class="row">
<h1>Supprimer la demande n°<?php echo $interprete["codeDemande"]; ?> ?</h1>

<?php
if(isset($_REQUEST['submit_delete_user'])) // si click sur btn "submit_delete_user"
{
  $identite = strip_tags($interprete["codeDemande"]); // on stock l'id de la demande
   try
   {
    $select_delete=$db->prepare("SELECT codeDemande FROM demandes WHERE codeDemande=:codeDemande"); // on select la demande

    $select_delete->execute(array(':codeDemande'=>$identite)); // on exec avec l'id en parametre
    $result_delete=$select_delete->fetch(PDO::FETCH_ASSOC);

    if($select_delete->rowCount() > 1){ // si  + d'un resultat
     $errorDeleteMessage[]="Il y a un problème car il y a plusieurs demandes avec cet ID."; //msg d'erreur
    }

    if($select_delete->rowCount() == 0){ // si 0 resultats
     $errorDeleteMessage[]="Erreur : Demande introuvable."; // msg d'erreur
    }

    else if(!isset($errorDeleteMessage)) // si pas de msg d'erreur
    {
     $delete_user=$db->prepare("DELETE FROM demandes_messages WHERE codeDemande=:codeDemande");   // on détruit la demande

     if($delete_user->execute(array(':codeDemande'=>$identite))){
       $db->prepare("DELETE FROM demandes WHERE codeDemande=:codeDemande")->execute(array(':codeDemande'=>$identite));

      $goodDeleteMessage="La demande a été supprimée avec succès."; // msg de succès
     }
    }
   }
   catch(PDOException $e)
   {
    echo $e->getMessage();
   }
  }
 ?>

<div id="box" style="margin: 0 auto;">
  <?php
  if(isset($errorDeleteMessage))
  {
   foreach($errorDeleteMessage as $errordelete)
   {
   ?>
    <div class="error"><?php echo $errordelete; ?></div>
      <?php
   }
  }
  if(isset($goodDeleteMessage))
  {
  ?>
   <div class="success">
     <?php echo $goodDeleteMessage; ?>
   </div>
  <?php
  }
  ?>
<center>
<h4>Êtes-vous sûr de vouloir supprimer la demande n°<i><?php echo $interprete["codeDemande"]; ?></i> ?</h4>
<hr>
<br>
<form method="POST">
<button type="submit" name="submit_delete_user">SUPPRIMER</button>&nbsp;&nbsp;
</form>
<a href="index.php">ANNULER</a>
<br><br>
</center>
</div>

</div>
