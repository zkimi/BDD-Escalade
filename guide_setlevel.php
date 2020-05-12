<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
  }

  if (!isset($_GET["id"])){ // si le champ id n'est pas init.
    echo "&nbsp;Vous devez sélectionner un guide.";
    die;
  } else if(empty($_GET["id"])){  // si le champ id est vide.
    echo "&nbsp;Vous n'avez sélectionné aucun guide.";
    die;
  } else if (!is_numeric($_GET["id"])){ // si le champ id comprend autre que des chiffres.
    echo "&nbsp;Saisissez une référence guide correcte.";
    die;
  } else if (!isset($_GET["club"])){ // si le champ club n'est pas init.
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if(empty($_GET["club"])){ // si le champ club est vide.
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die;
  } else if (!is_numeric($_GET["club"])){ // si le champ club comprend autre que des chiffres.
    echo "&nbsp;Saisissez une référence club correcte.";
    die;
  }
  else {
  $id = $_GET["id"];
  $club = $_GET["club"];
  $sql = "SELECT *,prenomAdherent,nomAdherent FROM guide,adherent WHERE guide.codeAdherent=$id AND guide.codeSite=$club AND guide.codeAdherent=adherent.codeAdherent LIMIT 1"; // on séléctionne le guide de tel club
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si y'a zéro résultat
    echo "&nbsp;Le guide sélectionné n'existe pas";
    die;
  }

  if ($interprete["codeAdherent"] != $_SESSION['user_login']){ // si le codeAdherent est different du codeAdherent de la personne actuelle
    echo "&nbsp;Vous essayez de modifier un guide qui n'est pas...VOUS. Ce qui n'est absolument pas permis !";
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
<h1>Choisir le niveau d'encadrement maximal</h1>
<div class="row">

<?php
if(isset($_REQUEST['submit_set_guide_level'])) //button name "submit_set_guide_level"
{
  $niveau = strip_tags($_REQUEST["niveau"]); // on stocke le niveau
   try
   {
    $select_guide=$db->prepare("SELECT * FROM guide WHERE codeAdherent=:codeAdherent AND codeSite=:codeSite AND niveau IS NOT NULL LIMIT 1"); // on séléctionne le guide en question avec comme condition que son niveau ne SOIT PAS NULL

    $select_guide->execute(array(':codeAdherent'=>$id, ':codeSite'=>$club)); // avec les paramètres corrrespondants

    if($select_guide->rowCount() == 1){ // si le guide avec niveau renseigné existe
     $errorGuideMessage[]="Vous avez déjà choisi un niveau, voyez avec un administrateur pour le modifier."; // msg d'erreur
    }

    else if(!isset($errorGuideMessage)) // sinon si pas d'erreur
    {
     $set_level=$db->prepare("UPDATE guide SET niveau=\"$niveau\" WHERE codeAdherent=:codeAdherent AND codeSite=:codeSite");   // on modifie le niveau d'encadrement du guide

     if($set_level->execute(array(':codeAdherent'=>$id, ':codeSite'=>$club))){

      $goodGuideMessage="Votre niveau a été modifié avec succès. Redirection..."; // msg succès
      header("refresh:1; view_club.php?id=$club"); // refresh
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
  if(isset($errorGuideMessage))
  {
   foreach($errorGuideMessage as $errorguide)
   {
   ?>
    <div class="error"><?php echo $errorguide; ?></div>
      <?php
   }
  }
  if(isset($goodGuideMessage))
  {
  ?>
   <div class="success">
     <?php echo $goodGuideMessage; ?>
   </div>
  <?php
  }
  ?>
<center>
<h4>Bonjour <u><?php echo strtoupper($interprete["nomAdherent"]); ?>&nbsp;<?php echo strtoupper($interprete["prenomAdherent"]); ?></u>, choisissez votre niveau d'encadrement :</h4>
<hr>
<small><i>Le niveau d'enseignement doit obligatoirement être inférieur à votre niveau personnel, sachant que votre niveau est : <b><?php echo strtoupper($interprete["niveau"]); ?></b>.</i></small>
<br><br>
<form method="POST">

  <div class="select">
  <select id="niveau" name="niveau" style="margin: 0 auto;">
    <option value="1" selected="selected">Niveau d'escalade</option>
    <?php
      $levels=$db->query("SELECT niveau FROM niveau;"); // on récupère tous les niveaux de la table niveau
      $levels->setFetchMode(PDO::FETCH_ASSOC); // avec son mode de recherche
    ?>
    <?php while ($levels_label = $levels->fetch()): // pour chaque ligne de la DB on crée une option avec le niveau correspondant ?>
      <option value="<?php echo htmlspecialchars($levels_label['niveau']) ?>"><?php echo htmlspecialchars($levels_label['niveau']) ?></option>
    <?php endwhile; ?>
  </select>
    <div class="select_arrow"></div>
  </div>


<button type="submit" name="submit_set_guide_level">Choisir le niveau d'encadrement</button>&nbsp;&nbsp;
</form>
<br><br>
</center>
</div>

</div>
