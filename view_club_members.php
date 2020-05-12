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

// partie promouvoir guide
if (isset($_GET["promote"])){ // si le champ promote est init.
  if (isset($_GET["action"])){ // si le champ action est init.

  $promote = $_GET["promote"]; // on stock l'id du membre a promouvoir

  if (empty($promote)){ // si l'id est vide
    echo "&nbsp;Vous n'avez pas choisi de membre à promouvoir.";
    die;
  }

  if (!is_numeric($promote)){ // si l'id compte autre chose que des chiffres
    echo "&nbsp;Merci de saisir un utilisateur correct!";
    die;
  }

  if ($_GET["action"] == 1){ // si action = 1
    $sql_find_guide = $db->query("SELECT * FROM guide WHERE codeSite=$id AND codeAdherent=$promote;"); // on selectionne le guide dans la BDD avec son id

    if ($sql_find_guide->rowCount() == 0){ // si zéro résultat = il n'existe pas
      $db->query("INSERT INTO guide (codeGuide, codeAdherent, codeSite) VALUES (NULL, $promote, $id)"); // alors on l'ajoute
      $successMsg = "Vous venez de promouvoir cette personne au rang de guide."; // msg succès
    } else { // s'il existe
      echo"&nbsp;Cette personne est déjà guide dans ce club !"; 
      die;
    }
  }

  if ($_GET["action"] == 0){ // si action = 0
    $sql_find_guide = $db->query("SELECT * FROM guide WHERE codeSite=$id AND codeAdherent=$promote;"); // on selectionne le guide dans la BDD avec son id

    if ($sql_find_guide->rowCount() == 0){ // si zéro résultat
      echo"&nbsp;Cette personne n'est pas guide dans le club...";
      die;
    } else { // sinon si il est guide
      $db->query("DELETE FROM guide WHERE codeAdherent=$promote AND codeSite=$id;"); // on supprime le guide de la BDD
      $CriticClubMessage[] = "Vous venez de retirer le rang de guide à cette personne."; // msg rouge
    }
  }
}
}
?>


<h1>Membres du club : <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>

<div class="row">
  <div id="box">
    <?php
    if(isset($CriticClubMessage)) // si le tableau errorMsg est initialisé
    {
     foreach($CriticClubMessage as $redLabel) // pour chaque ligne du tableau on initalise une variable
     {
     ?>
      <div class="error"><?php echo $redLabel; // on affiche cette variable ?></div>
        <?php
     }
    }
    if(isset($successMsg)) // si un message de succès est initalisé
    {
    ?>
     <div class="success">
       <?php echo $successMsg; // on l'affiche ?>
     </div>
    <?php
    }
    ?>
    <center>
      <h3>Membres (<?php echo $count; ?>)</h3>
      <hr>
      <?php
      $sql = "SELECT codeAdherent,nomAdherent,prenomAdherent,niveau FROM adherent WHERE codeSite=$id"; // on séléctionne tout les membres du club
      $q = $db->query($sql);
      $q->setFetchMode(PDO::FETCH_ASSOC);
      while ($membres = $q->fetch()): ?>
      <?php echo htmlspecialchars(strtoupper($membres['nomAdherent'])); ?>&nbsp;<?php echo htmlspecialchars(strtoupper($membres['prenomAdherent'])); ?>&nbsp;<b>(niveau: <?php echo htmlspecialchars(strtoupper($membres['niveau'])); ?>)</b>
      <br><p>
        <?php
        $id_adherent = $membres["codeAdherent"];
        $guides = $db->query("SELECT * FROM guide WHERE codeSite = $id AND codeAdherent=$id_adherent")->rowCount(); // on compte les guides avec son ID

        if ($guides == 1){ // si c'est un guide
          echo "<div class=\"actionbtn red\"><a href=\"view_club_members.php?id=$id&promote=$id_adherent&action=0\">Retirer du rang de guide</a></div>";
        } else {
          echo "<div class=\"actionbtn green\"><a href=\"view_club_members.php?id=$id&promote=$id_adherent&action=1\">Promouvoir au rang de guide</a></div>";
        }
         ?>

    </p>
      <hr>
    <?php endwhile; ?>
      <br>
      <hr>
      <a href="view_club.php?id=<?php echo $interprete["codeSite"]; ?>">Retour</a>
    </center>
  </div>
</div>

<?php include("includes/footer.php"); ?>
