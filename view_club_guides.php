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

  $sqlcount = "SELECT * FROM guide WHERE codeSite = $id"; // selectionner tous les adherents de ce club
  $count = $db->query($sqlcount)->rowCount(); // stockage dans $count de la valeur de la fonction rowCount()

  if ($result->rowCount() == 0){ // si il n'y a pas d'occurence
    echo "&nbsp;Ce club ne comporte pas de guide(s)...";
    die; // on affiche rien d'autre !
  }

  if ($interprete["statut"] == -1){
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

?>


<h1>Guides du club : <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>

<div class="row">
  <div id="box">
    <center>
      <h3>Guides (<?php echo $count; ?>)</h3>
      <hr>
      <?php
      $sql = "SELECT *,guide.niveau,prenomAdherent,nomAdherent FROM guide,adherent WHERE guide.codeSite=$id AND guide.codeAdherent=adherent.codeAdherent";
      $q = $db->query($sql);
      $q->setFetchMode(PDO::FETCH_ASSOC);

      if ($count == 0){
        echo "Ce club ne comporte pas de guide(s)";
      }

      while ($membres = $q->fetch()): ?>
      <?php echo htmlspecialchars(strtoupper($membres['nomAdherent'])); ?>
      <?php echo htmlspecialchars(strtoupper($membres['prenomAdherent'])); ?>&nbsp;
      <b>(niveau d'encadrement: <?php
      if (!is_null($membres['niveau'])){
        echo htmlspecialchars(strtoupper($membres['niveau']));
      } else {
        echo "<FONT color=\"red\">Non renseigné</FONT>";
      }
      ?>)</b>
      <hr>
    <?php endwhile; ?>
      <br>
      <a href="view_club.php?id=<?php echo $interprete["codeSite"]; ?>">Retour</a>
    </center>
  </div>
  <br>
</div>

<?php include("includes/footer.php"); ?>
