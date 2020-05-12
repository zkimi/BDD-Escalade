<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) // si l'utilisateur n'est pas connecté
  {
    header("location:index.php?logged=no"); // redirection avec message d'erreur!
  }

  if (!isset($_GET["id"])){ // si id n'est pas initalisé
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die;
  }
  else if (is_numeric($_GET["id"])){
  $id = $_GET["id"]; // stockage de l'id pour plus de simplicité.
  $sql = "SELECT *,site.codeSite FROM site,adherent WHERE site.codeSite=$id AND responsable_id=adherent.codeAdherent LIMIT 1"; // selectionne les infos du club ainsi que son responsable
  $interprete = $db->query($sql)->fetch(); // stockage dans $interprete du resultat

  $sqlcount = "SELECT * FROM adherent WHERE codeSite = $id"; // selectionner tous les adherents de ce club
  $count = $db->query($sqlcount)->rowCount(); // stockage dans $count de la valeur de la fonction rowCount()

  if ($result->rowCount() == 0){ // si il n'y a pas d'occurence
    echo "&nbsp;Le club sélectionné n'existe pas";
    die; // on affiche rien d'autre !
  }

  if (($interprete["responsable_id"] != $row["codeAdherent"]) || $interprete["statut"] == -1){
    echo "&nbsp;<u>Vous ne pouvez pas effectuer cette action pour une des raisons suivantes :</u>
    <ul>
      <li>Vous n'avez pas les permissions d'effectuer cette action</li>
      <li>Vous n'êtes pas responsable de ce club</li>
      <li>Ce club est banni</li>
    </ul>";
    die;
  }
} else { // sinon
  echo "&nbsp;Il y a une erreur dans l'URL de la page (attention aux caractères spéciaux!)";
  die; // on affiche rien d'autre !
}

  $guides=$db->query("SELECT *,nomUtilisateur FROM guide,adherent WHERE guide.codeSite=$id AND guide.codeAdherent=adherent.codeAdherent;"); // on récupère tous les guides du club
  $guides->setFetchMode(PDO::FETCH_ASSOC); // avec son mode de recherche

  if ($guides->rowCount() == 0){// si le club contient zéro guides
    echo "&nbsp;Il n'y a aucun guide dans votre club pour créer une cordée";
  die; // on affiche rien d'autre !
  }

  $parcours=$db->query("SELECT * FROM voie WHERE voie.codeSite=$id;"); // on récupère tous les guides du club
  $parcours->setFetchMode(PDO::FETCH_ASSOC); // avec son mode de recherche

  if ($parcours->rowCount() == 0){// si le club contient zéro guides
    echo "&nbsp;Il n'y a aucun parcours dans votre club pour créer une cordée";
  die; // on affiche rien d'autre !
  }


?>



<h1>Création de cordée : <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>

<div class="row">


  <?php
  if(isset($_REQUEST['submitNewCordee'])) // si le formulaire est envoyé avec le bouton submitRequest
  {

    if (!isset($_REQUEST["style"])) { // si l'option séléctionné == 0
      $errorSortieMessage[]="Merci de choisir un guide";
    } else {
      $style = strip_tags($_REQUEST["style"]);
    }

    if (!isset($_REQUEST["guide"])) { // si l'option séléctionné == 0
      $errorSortieMessage[]="Merci de choisir un guide";
    } else {
      $guide = strip_tags($_REQUEST["guide"]);
    }

    if (!isset($_REQUEST["parcoursSelect"])) { // si l'option séléctionné == 0
      $errorSortieMessage[]="Merci de choisir un parcours";
    } else {
      $parcours_field = strip_tags($_REQUEST["parcoursSelect"]);
    }
    $titre = strip_tags($_REQUEST["titre"]);
    $nbMax = strip_tags($_REQUEST["nbmax"]);
    $date = strip_tags($_REQUEST["date"]);
     try
     {
      $select_sortie=$db->query("SELECT * FROM sortie WHERE descriptionSortie=\"$titre\""); // on séléctionne les parcours de ce club avec ce titre

      if($select_sortie->rowCount() > 0){ // si y'en a plus de 0
       $errorSortieMessage[]="Une sortie existe déjà avec ce titre."; 
      }

      if (empty($titre) || strlen($titre) < 3){ // si le titre est vide ou inférieur a 3 caractères
        $errorSortieMessage[]="Merci de saisir un titre valide";
      }

      if ($nbMax < 1 || $nbMax > 15){
        $errorSortieMessage[]="Il doit y avoir au moins 1 participant ou bien 15 participants maximum";
      }

      if (empty($nbMax)){
        $errorSortieMessage[]="Merci de choisir un nombre maximum de participants";
      }

      if (empty($date)){
        $errorSortieMessage[]="Merci de choisir une date";
      }

      else if(!isset($errorSortieMessage)) // si aucune erreur :
      {

       $get_parcours_details = $db->query("SELECT * FROM voie WHERE codeVoie=\"$parcours_field\";")->fetch();

       $insert_sortie=$db->prepare("INSERT INTO sortie VALUES (NULL, :titre, :codeVoie, :dateS, :nbMaxSortie, :niveau, :id, :styleAscension, :guide);");   // on créer la demande dans la BDD

       if($insert_sortie->execute(array(':titre'=>$titre, ':codeVoie'=>$get_parcours_details["codeVoie"], ':dateS'=>$date, ':nbMaxSortie'=>$nbMax, ':niveau'=>$get_parcours_details["difficulteVoie"], ':id'=>$id, ':styleAscension'=>$style, ':guide'=>$guide))){

        $goodSortieMessage="La sortie a été créée avec succès. Redirection..."; // message de succès
        header("refresh:1; view_club.php?id=$id");
       }
      }
     }
     catch(PDOException $e)
     {
      echo $e->getMessage();
     }
    }
   ?>


  <div id="box">


<?php
     if(isset($errorSortieMessage))
     {
      foreach($errorSortieMessage as $errorsortie)
      {
      ?>
       <div class="error"><?php echo $errorsortie; ?></div>
         <?php
      }
     }
     if(isset($goodSortieMessage))
     {
     ?>
      <div class="success">
        <?php echo $goodSortieMessage; ?>
      </div>
     <?php
     }
     ?>

    <center>
      <h3>Créer une nouvelle sortie (cordée)</h3>
      <hr>
     

      <form method="POST">


        <label>Titre de la cordée :</label>
        <input type="text" id="titre" name="titre" placeholder="Titre" />

        <label>Parcours sélectionné :</label>
        <div class="select">
    <select id="parcoursSelect" name="parcoursSelect" style="margin: 0 auto;">
    <option value disabled selected="selected">Parcours</option>
    <?php while ($parcours_label = $parcours->fetch()): // pour chaque ligne de la DB on crée une option avec le parcours correspondant ?>
      <option value="<?php echo htmlspecialchars($parcours_label['codeVoie']) ?>"><?php echo htmlspecialchars($parcours_label['nomVoie']) ?></option>
    <?php endwhile; ?>
    </select>
      <div class="select_arrow"></div>
    </div>
        <br>

        <label>Style d'ascension :</label>
        <div class="select">
    <select id="style" name="style" style="margin: 0 auto;">
    <option value disabled selected="selected">Style</option>
    <?php 

    $style_requete=$db->query("SELECT * FROM ascension;"); // on récupère tous les guides du club
    $style_requete->setFetchMode(PDO::FETCH_ASSOC); // avec son mode de recherche
    while ($style_label = $style_requete->fetch()): // pour chaque ligne de la DB on crée une option avec le parcours correspondant ?>
      <option value="<?php echo htmlspecialchars($style_label['styleAscension']) ?>"><?php echo htmlspecialchars($style_label['styleAscension']) ?></option>
    <?php endwhile; ?>
    </select>
      <div class="select_arrow"></div>
    </div>
        <br>

        <label>Guide encadrant :</label>
        <div class="select">
    <select id="guide" name="guide" style="margin: 0 auto;">
    <option value disabled  selected="selected">Guide</option>
    <?php while ($guides_label = $guides->fetch()): // pour chaque ligne de la DB on crée une option avec le guide correspondant ?>
      <option value="<?php echo htmlspecialchars($guides_label['codeGuide']) ?>"><?php echo htmlspecialchars($guides_label['nomUtilisateur']) ?></option>
    <?php endwhile; ?>
    </select>
      <div class="select_arrow"></div>
    </div>

    <br><label>Date de la sortie :</label>
    <input type="date"  min="2020-01-01" name="date">

    <label>Nombre maximal de participants :</label>
    <input type="number"  min="1" max="15" name="nbmax">

        <button type="submit" name="submitNewCordee" id="submitNewCordee">Envoyer la demande</button>
      </form>


      <br><hr>
      <a href="view_club.php?id=<?php echo $id; ?>">Retour</a>
    </center>
  </div>
  <br>
</div>

<?php include("includes/footer.php"); ?>
