<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
  }

  if (!isset($_GET["id"])){ // si id n'est init.
    echo "&nbsp;Vous devez sélectionner une demande.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucune demande.";
    die;
  } else if(!is_numeric($_GET["id"])){ // si id comporte autre que des chiffres
    echo "&nbsp;La référence n'est pas valide ! Attention aux caractères spéciaux.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT *,nomUtilisateur FROM demandes,adherent WHERE codeDemande=$id AND utilisateur_id=adherent.codeAdherent LIMIT 1"; // on séléctionne la demande avec l'ID
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si zéro lignes
    echo "&nbsp;La demande sélectionnée n'existe pas";
    die;
  }

  if ($result->rowCount() == 1 && $interprete["utilisateur_id"] != $_SESSION['user_login']){ // si l'ID du créateur de la demande n'est pas le même que l'id de l'user ACTUEL
    echo "&nbsp;Vous essayez de consulter une autre demande que la votre... Ce qui n'est pas permis !";
    die;
  }
}
    ?>
<style media="screen">
  .categ {
    width: 23%;
    background-color: #e1e1e1;
    font-style: italic;
    padding: 10px;
  }
</style>

<h1>Consultation de la demande n°<?php echo $interprete["codeDemande"]; ?></h1>
  <center><i><FONT color="grey">Les demandes sont traitées par nos administrateurs dans les 24h à 72h, merci de bien vouloir patienter avant de nous re-contacter.</FONT></i></center>
<br>
<div class="row">
<table style="width: 100%;" border="1">
<tbody style="background-color:white;">
<tr>
<td class="categ">&nbsp;Référence de la demande</td>
<td>&nbsp;<?php echo $interprete["codeDemande"]; ?></td>
</tr>
<tr>
<tr>
<td class="categ">&nbsp;Pseudo</td>
<td>&nbsp;<?php echo $interprete["nomUtilisateur"]; ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Motif</td>
<td>&nbsp;<?php echo $interprete["motif"]; ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Date de l'envoi de la demande</td>
<td>&nbsp;<?php echo edit_date_format($interprete["date"]); ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Statut</td>
<td>&nbsp;<?php check_status_request($interprete["statut"]); ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Message</td>
<td>&nbsp;<?php echo $interprete["texte"]; ?></td>
</tr>

<?php
  $demande = $interprete["codeDemande"];
  $sql = "SELECT *,nomUtilisateur FROM demandes_messages,adherent WHERE codeDemande=$demande AND codeAdherent=auteur_id"; // on séléctionne les eventuels messages de reponses a la demande avec son ID.
  $result = $db->query($sql);
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>
  <?php while ($row = $q->fetch()): // pour chaque réponse on l'affiche a la suite ?>
    <tr>
      <td class="categ" style="background-color:#b2b2b2;"><b>MAJ : </b><?php echo htmlspecialchars($row['nomUtilisateur']) ?> (<?php edit_date_format($row['date']); ?>)</td>
      <td>&nbsp;<?php echo htmlspecialchars($row['message']); ?></td>
    </tr>
  <?php endwhile; ?>




</tbody>
</table>

<br>
<center><h4 style="margin:0px;">Ajouter un commentaire</h4></center><hr>

<?php
if(isset($_REQUEST['submitReponse'])) // si clic bouton ajouter une reponse
{
  $reponse = strip_tags($_REQUEST["reponse"]); // on stocke la reponse
   try
   {
    if(empty($reponse)){ // si la réponse est vide
     $errorReponseMessage[]="Merci de saisir du texte";
    }

    if($interprete['statut'] == 0 || $interprete['statut'] == 3 || $interprete['statut'] == 4){ // si la demande est fermée, annulée ou validée
      $errorReponseMessage[]="Vous ne pouvez ajouter de commentaire à cette demande vis-à-vis de son statut.";
    }

    else if(!isset($errorReponseMessage)) // si pas de msg d'erreurs
    {
     $add_reponse=$db->prepare("INSERT INTO demandes_messages VALUES (NULL, :codeDemande, :message, :auteur, NOW());");   // on insert le message

     if($add_reponse->execute(array(':codeDemande'=>$interprete["codeDemande"], ':message'=>$reponse, ':auteur'=>$_SESSION["user_login"]))){

      $db->prepare("UPDATE demandes SET statut=1 WHERE codeDemande = :codeDemande")->execute(array(':codeDemande'=>$interprete["codeDemande"])); // et on repasse la demande en traitement !

      $goodReponseMessage="La réponse a été ajoutée."; // msg de succès
         }
    }
   }
   catch(PDOException $e)
   {
    echo $e->getMessage();
   }
  }
 ?>

 <?php
 if(isset($errorReponseMessage))
 {
  foreach($errorReponseMessage as $errorreponse)
  {
  ?>
   <div class="error"><?php echo $errorreponse; ?></div>
     <?php
  }
 }
 if(isset($goodReponseMessage))
 {
 ?>
  <div class="success">
    <?php echo $goodReponseMessage; ?>
  </div>
 <?php
 }
 ?>
<form method="POST">
<textarea name="reponse" style="resize:none;"></textarea>
<button type="submit" name="submitReponse">Ajouter un commentaire</button>
<form>

<br>
<center><i>Vous voulez annuler votre demande ? <a href="delete_request.php?id=<?php echo $interprete["codeDemande"];?>">Cliquez-ici</a></i></center><br>
</div>

<?php include("includes/footer.php"); ?>
