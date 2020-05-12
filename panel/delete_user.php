<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }

  if (!isset($_GET["id"])){ // si id est pas init
    echo "&nbsp;Vous devez séléctionner un utilisateur.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez séléctionné aucun utilisateur.";
    die;
  } else if (!is_numeric($_GET["id"])) { // si id contient autre que des chiffres
    echo "&nbsp;Merci de saisir une utilisateur valide.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT * FROM adherent WHERE codeAdherent=$id LIMIT 1"; // on séléctionne l'utilisateur avec cet ID.
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si zéro résultat
    echo "&nbsp;L'utilisateur séléctionné n'existe pas";
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
<h1>Supprimer l'utilisateur : <?php echo $interprete["nomUtilisateur"]; ?></h1>

<?php
if(isset($_REQUEST['submit_delete_user'])) // si clic sur btn "submit_delete_user"
{
  $identite = strip_tags($interprete["codeAdherent"]); // on stock l'id.
   try
   {
    $select_delete=$db->prepare("SELECT codeAdherent FROM adherent WHERE codeAdherent=:codeAdherent"); // on select l'user

    $select_delete->execute(array(':codeAdherent'=>$identite)); // execute avec l'id en paramètre
    $result_delete=$select_delete->fetch(PDO::FETCH_ASSOC);

    if($select_delete->rowCount() > 1){ // si + d'un résultat
     $errorDeleteMessage[]="Il y a un problème car il y a plusieurs utilisateurs avec cet ID."; // error msg
    }

    if($select_delete->rowCount() == 0){ // si 0 resultats
     $errorDeleteMessage[]="Erreur : Utilisateur introuvable."; // error msg
    }

    else if(!isset($errorDeleteMessage)) // si pas d'error msg
    {
     $delete_user=$db->prepare("DELETE FROM adherent WHERE codeAdherent=:codeAdherent");   // on delete l'utilisateur correspondant

     if($delete_user->execute(array(':codeAdherent'=>$identite))){

      $goodDeleteMessage="L'utilisateur a été supprimé avec succès."; // message de succès
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
<h4>Êtes-vous sûr de vouloir supprimer l'utilisateur <i><?php echo $interprete["nomUtilisateur"]; ?></i> ?</h4>
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
