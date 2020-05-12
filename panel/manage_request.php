<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:index.php?logged=no_access");
  }

  if (!isset($_GET["id"])){ // si id n'est pas init.
    echo "&nbsp;Vous devez sélectionner une demande.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucune demande.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT *,nomUtilisateur FROM demandes,adherent WHERE codeDemande=$id AND utilisateur_id=adherent.codeAdherent LIMIT 1"; // on séléctionne la demande avec l'id correspondant
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si zéro résultat
    echo "&nbsp;La demande sélectionnée n'existe pas";
    die;
  }

  if (isset($_GET["delete"])){ // si le champ delete est init

    if ($_GET["delete"] == 1){ // s'il est égal a 1

      $sql = "UPDATE demandes SET statut = 3 WHERE codeDemande = $id"; // on met le statut 3
      $result = $db->query($sql);
      $redActionMessage[] = "La demande a été cloturée. Actualisation...";
      header( "Refresh:2; url=manage_request.php?id=$id", true, 303);

    } else if ($_GET["delete"] == 0){ // s'il est égal a 0

      $sql = "UPDATE demandes SET statut = 1 WHERE codeDemande = $id"; // on met le statut 1
      $result = $db->query($sql);
      $greenActionMessage = "La demande a été ré-ouverte et placée en cours de traitement. Actualisation...";
      header( "Refresh:3; url=manage_request.php?id=$id", true, 303);

    }
  }

  if (isset($_GET["check"])){ // si le champ check est initialisé

    if ($_GET["check"] == 1){ // si = 1

      $sql = "UPDATE demandes SET statut = 4 WHERE codeDemande = $id"; // on met le statut 4
      $result = $db->query($sql);
      $redActionMessage[] = "La demande a été validée. Actualisation...";
      header( "Refresh:2; url=manage_request.php?id=$id", true, 303);

    } else if ($_GET["check"] == 0){ // si = 0

      $sql = "UPDATE demandes SET statut = 1 WHERE codeDemande = $id"; // on met le statut 1
      $result = $db->query($sql);
      $greenActionMessage = "La demande a été invalidée et placée en cours de traitement. Actualisation...";
      header( "Refresh:3; url=manage_request.php?id=$id", true, 303);

    }
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
  #box{
    width: 80%;
    margin: 0 auto;
    background-color: white;
    border-radius:5px;
    border:1px solid black;
    padding:5px;
  }
</style>

<h1>Traitement de la demande n°<?php echo $interprete["codeDemande"]; ?></h1>
  <center><i><FONT color="grey">Administrateurs, les modifications que vous effectuez seront visibles par les utilisateurs, soyez prudents.</FONT></i></center>
<br>
<div id="box">
  <center>
<h4 style="margin:2px;">Actions administrateur</h4>
<hr>


<?php
if(isset($redActionMessage))
{
 foreach($redActionMessage as $redLabel)
 {
 ?>
  <div class="error"><?php echo $redLabel; ?></div>
    <?php
 }
}
if(isset($greenActionMessage))
{
?>
 <div class="success">
   <?php echo $greenActionMessage; ?>
 </div>
<?php
}
?>

<?php
  $uid = $interprete["codeDemande"];

  if ($interprete["statut"] == 1 || $interprete["statut"] == 2){ // si le statut == 1 ou == 2
    echo "<a href=\"manage_request.php?id=$uid&delete=1\">Cloturer la demande</a>";
  } else if ($interprete["statut"] == 3) { // si == 3
    echo "<a href=\"manage_request.php?id=$uid&delete=0\">Ré-ouvrir la demande</a>";
  }
  echo"&nbsp;|&nbsp;";
  if ($interprete["statut"] == 1 || $interprete["statut"] == 2){ // si le statut == 1 ou == 2
    echo "<a href=\"manage_request.php?id=$uid&check=1\">Valider la demande</a>";
  } else if ($interprete["statut"] == 4) { // si == 4
    echo "<a href=\"manage_request.php?id=$uid&check=0\">Invalider la demande</a>";
  }
?>
&nbsp;|&nbsp;<a href="edit_user.php?id=<?php echo $interprete["utilisateur_id"]; ?>">Voir le profil</a>
</center>
</div>
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
  $sql = "SELECT *,nomUtilisateur FROM demandes_messages,adherent WHERE codeDemande=$demande AND codeAdherent=auteur_id"; // séléctionne chaque message de la demande et de l'utilisateur
  $result = $db->query($sql);
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>
  <?php while ($row = $q->fetch()): ?>
    <tr>
      <td class="categ" style="background-color:#b2b2b2;"><b>MAJ : </b><?php echo htmlspecialchars($row['nomUtilisateur']) ?> (<?php edit_date_format($row['date']); ?>)</td>
      <td>&nbsp;<?php echo htmlspecialchars($row['message']); ?></td>
    </tr>
  <?php endwhile; ?>

</tbody>
</table>
<br>
<center><h4 style="margin:0px;">Votre réponse</h4></center><hr>

<?php
if(isset($_REQUEST['submitReponse'])) // si click sur btn "submitReponse"
{
  $reponse = strip_tags($_REQUEST["reponse"]); // on stock la réponse
   try
   {
    if(empty($reponse)){
     $errorReponseMessage[]="Merci de saisir du texte"; // si la réponse est vide
    }

    else if(!isset($errorReponseMessage)) // si pas de msg d'erreur
    {
     $add_reponse=$db->prepare("INSERT INTO demandes_messages VALUES (NULL, :codeDemande, :message, :auteur, NOW());"); // on insert la réponse

     if($add_reponse->execute(array(':codeDemande'=>$interprete["codeDemande"], ':message'=>$reponse, ':auteur'=>$_SESSION["user_login"]))){
      $db->prepare("UPDATE demandes SET statut=2 WHERE codeDemande = :codeDemande")->execute(array(':codeDemande'=>$interprete["codeDemande"]));

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
<button type="submit" name="submitReponse">Répondre à l'utilisateur</button>
<form>

</div>

<?php include("includes/footer.php"); ?>
