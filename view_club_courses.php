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

  $sqlcount = "SELECT * FROM site WHERE codeSite = $id"; // selectionner tous les adherents de ce club
  $count = $db->query($sqlcount)->rowCount(); // stockage dans $count de la valeur de la fonction rowCount()

  if ($count == 0){ // si il n'y a pas d'occurence
    echo "&nbsp;Le club sélectionné n'existe pas";
    die; // on affiche rien d'autre !
  }

  if (($interprete["responsable_id"] != $row["codeAdherent"]) || $interprete["statut"] == -1){ // si l'id du responsable n'est pas égal à l'id de l'utilisateur consultant cette page ou que ce club est banni
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

if (isset($_GET["delete"])){ // si delete est initalisé
   if(empty($_GET["delete"])){ // si delete est vide
      echo "&nbsp;Vous n'avez sélectionné aucun parcours.";
      die;
    }

  else if (is_numeric($_GET["delete"])){ // si delete est numérique

      $id_parcours = $_GET["delete"]; // stockage en vue de la requete

      $select_parcours = $db->query("SELECT * FROM voie WHERE codeSite=$id AND codeVoie=$id_parcours");

      if ($select_parcours->rowCount() > 0){
        $db->query("DELETE FROM voie WHERE codeVoie = $id_parcours");
      } else {
        echo "&nbsp;Le parcours est introuvable ou alors vous essayez de supprimer le parcours d'un autre club.";
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
}

?>

<h1>Parcours proposés par le club : <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>

<div class="row">
  <div id="box">
    <center>
      <?php
      $find_courses_sql = $db->query("SELECT * FROM voie WHERE codeSite=$id;");
      echo "<h3>Parcours (";echo $find_courses_sql->rowCount(); echo")</h3><hr>";

      if ($find_courses_sql->rowCount() == 0){
        echo "Aucun parcours proposé. <a href=\"add_course.php?id=$id\">En créer un nouveau ?</a>";
      } else { ?>
        <div class="card material-table">
          <div class="table-header">
            <span class="table-title">Parcours proposés</span>
            <div class="actions">
              <a href="add_course.php?id=<?php echo $id;?>" class="waves-effect btn-flat nopadding" title="Créer un parcours"><i class="fas fa-folder-plus"></i></a>
              <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
            </div>
          </div>
          <table id="datatable" style="width: 100%;">
            <thead>
              <tr>
              <th>N° parcours
              <th>Nom du parcours
              <th>Longueur
              <th>Type
              <th>Niveau requis
              <th>Actions
          </thead>
          <tbody>
            <?php while ($row = $find_courses_sql->fetch()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['codeVoie']) ?></td>
              <td><?php echo htmlspecialchars($row['nomVoie']) ?></td>
              <td><?php echo htmlspecialchars($row['longueurVoie']); ?></td>
              <td><?php echo htmlspecialchars($row["typeVoie"]); ?></td>
              <td><?php echo htmlspecialchars($row['difficulteVoie']); ?></td>
              <td>  <a href="edit_course.php?id=<?php echo $row['codeVoie']?>" class="extrasmall_btn"><i class="fas fa-pencil-alt"></i></a>
              <a href="view_club_courses.php?id=<?php echo $id; ?>&delete=<?php echo $row['codeVoie'];?>" class="extrasmall_btn" style="background-color:#b80000;border:2px solid #7c0000;"><i class="fas fa-trash-alt"></i></a></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
</div>
      <?php } ?>
      <hr>
      <a href="view_club.php?id=<?php echo $interprete["codeSite"]; ?>">Retour</a>
    </center>
  </div>
</div>

<?php include("includes/footer.php"); ?>
