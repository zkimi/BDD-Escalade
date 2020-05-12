<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) // si l'user est pas log ou est pas admin
  {
    header("location:../index.php?logged=no_access"); // redirection
  }

  if (!isset($_GET["id"])){ // si champ id n'est pas renseigné
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if (!is_numeric($_GET["id"])) { // si champ id est pas un nombre
    echo "&nbsp;Merci de saisir un club valide.";
    die;
  }
  else if(empty($_GET["id"])){ // si champ id est vide
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT * FROM site WHERE codeSite=$id LIMIT 1"; // on séléctionne le club en question avec l'id rentré
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si 0 lignes alors
    echo "&nbsp;Le club sélectionnée n'existe pas";
    die;
  }
  }
  ?>


<div class="row">
<h1>Supprimer le club n°<?php echo $interprete["codeSite"]; ?> - <?php echo $interprete["nomSite"]; ?> ?</h1>

<?php
if(isset($_REQUEST['submit_delete_club'])) // si click sur "submit_delete_club"
{
  $identite = strip_tags($interprete["codeSite"]); // on stock l'id du club
   try
   {
    $select_delete=$db->prepare("SELECT codeSite FROM site WHERE codeSite=:codeSite"); // on séléctionne le club

    $select_delete->execute(array(':codeSite'=>$identite)); // on execute avec les paramètres
    $result_delete=$select_delete->fetch(PDO::FETCH_ASSOC);

    if($select_delete->rowCount() > 1){ // si y'a plus d'une ligne qui apparait alors 
     $errorDeleteMessage[]="Il y a un problème car il y a plusieurs clubs avec cet ID."; 
    }

    if($select_delete->rowCount() == 0){
     $errorDeleteMessage[]="Erreur : Club introuvable."; // si 0 lignes alors
    }

    else if(!isset($errorDeleteMessage)) // si aucun message d'erreur :
    {
    $nb_notes = $db->query("SELECT * FROM voie_notes WHERE codeSite = $id;")->rowCount();
    for ($i=0; $i < $nb_notes; $i++) {
      $db->query("DELETE FROM voie_notes WHERE codeSite=$id"); // on supprime tout les avis sur les parcours de ce club
    }
    $nb_cordees = $db->query("SELECT * FROM cordee WHERE cordee.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_cordees; $i++) {
      $db->query("DELETE FROM cordee WHERE cordee.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $nb_sorties = $db->query("SELECT * FROM sortie WHERE sortie.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_cordees; $i++) {
      $db->query("DELETE FROM sortie WHERE sortie.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $db->query("ALTER TABLE voie DISABLE KEYS;");
    $nb_parcours = $db->query("SELECT * FROM voie WHERE voie.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_parcours; $i++) {
      $db->query("DELETE FROM voie WHERE voie.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $db->query("ALTER TABLE voie ENABLE KEYS;");
    $nb_guides = $db->query("SELECT * FROM guide WHERE guide.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_guides; $i++) {
      $db->query("DELETE FROM guide WHERE guide.codeSite=$id;"); // on supprime tout les guides de ce club
    }
    $db->query("UPDATE adherent SET codeSite=NULL WHERE codeSite=$id"); // on vire tout les membres du club
    $db->query("DELETE FROM site WHERE codeSite=$id"); // et enfin, on supprime le club de la BDD.


      $goodDeleteMessage="Le club a été supprimé avec succès."; // message de succès
     
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
<h4>Êtes-vous sûr de vouloir supprimer le club n°<i><?php echo $interprete["codeSite"]; ?></i> ?</h4>
<hr>
<br>
<form method="POST">
<button type="submit" name="submit_delete_club">SUPPRIMER</button>&nbsp;&nbsp;
</form>
<a href="index.php">ANNULER</a>
<br><br>
</center>
</div>

</div>
