<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
  }

  if (!isset($_GET["id"])){ // si le champ id n'est pas init.
    echo "&nbsp;Vous devez sélectionner une demande.";
    die;
  } else if(empty($_GET["id"])){ // si le champ id est vide.
    echo "&nbsp;Vous n'avez sélectionné aucune demande.";
    die;
  } else if (!is_numeric($_GET["id"])){ // si le champ id comprend autre que des chiffres.
    echo "&nbsp;Saisissez une référence correcte.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT *,nomUtilisateur FROM demandes,adherent WHERE codeDemande=$id AND utilisateur_id=adherent.codeAdherent LIMIT 1";
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){
    echo "&nbsp;La demande sélectionnée n'existe pas";
    die;
  }

  if ($result->rowCount() == 1 && $interprete["utilisateur_id"] != $_SESSION['user_login']){
    echo "&nbsp;Vous essayez d'annuler une autre demande que la votre... Ce qui n'est absolument pas permis !";
    die;
  }
  if ($interprete["statut"] == 3 || $interprete["statut"] == 0 || $interprete["statut"] == 4){

    if ($interprete['statut'] == 0){
      $statut="Demande annulée";
    } else if ($interprete['statut'] == 1){
      $statut="En cours de traitement";
    } else if ($interprete['statut'] == 2){
      $statut="Mis à jour";
    } else if ($interprete['statut'] == 3){
      $statut="Demande cloturée";
    } else if ($interprete['statut'] == 4){
      $statut="Demande validée";
    } else {
      $statut="Inconnu";
    }

    echo "&nbsp;Vous ne pouvez pas annuler cette demande car son statut est : <b>$statut</b>.";
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
<h1>Supprimer la demande n°<?php echo $interprete["codeDemande"]; ?></h1>
<div class="row">

<?php
if(isset($_REQUEST['submit_delete_request'])) //button name "submit_delete_request"
{
  $demande = strip_tags($interprete["codeDemande"]); // stock l'id de la demande
   try
   {
    $select_delete=$db->prepare("SELECT codeDemande FROM demandes WHERE codeDemande=:codeDemande"); // on séléctionne la demande avec l'id correspondant

    $select_delete->execute(array(':codeDemande'=>$demande)); // paramètres requete
    $result_delete=$select_delete->fetch(PDO::FETCH_ASSOC);

    if($select_delete->rowCount() > 1){ // si + de 1 resultat
     $errorDeleteMessage[]="Il y a un problème car il y a plusieurs demandes avec cet ID."; // erreur msg
    }

    if($select_delete->rowCount() == 0){ // si 0 résultat
     $errorDeleteMessage[]="Erreur : Demande introuvable."; // erreur msg
    }

    else if(!isset($errorDeleteMessage)) // si pas d'erreur msg alors
    {
     $delete_user=$db->prepare("UPDATE demandes SET statut=0 WHERE codeDemande=:codeDemande");   // on delete la demande avec l'id correspondant

     if($delete_user->execute(array(':codeDemande'=>$demande))){

      $goodDeleteMessage="La demande a été annulée avec succès. Redirection..."; // msg de succès
      header("refresh:1; requests.php");
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
<h4>Êtes-vous sûr de vouloir supprimer la demande à propos de : <i>"<?php echo $interprete["motif"]; ?>"</i> portant le numéro <u><?php echo $interprete["codeDemande"]; ?></u> ?</h4>
<hr>
<br>
<form method="POST">
<button type="submit" name="submit_delete_request">SUPPRIMER</button>&nbsp;&nbsp;
</form>
<a href="view_request.php?id=<?php echo $demande; ?>">ANNULER</a>
<br><br>
</center>
</div>

</div>
