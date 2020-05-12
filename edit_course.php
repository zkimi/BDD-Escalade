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
  $sql = "SELECT *,site.codeSite FROM site,adherent,voie WHERE voie.codeVoie=$id AND voie.codeSite=site.codeSite AND site.responsable_id=adherent.codeAdherent LIMIT 1"; // selectionne les infos du club ainsi que son responsable
  $interprete = $db->query($sql)->fetch(); // stockage dans $interprete du resultat

  $sqlcount = "SELECT * FROM voie WHERE codeVoie = $id"; // selectionner tous les adherents de ce club
  $count = $db->query($sqlcount)->rowCount(); // stockage dans $count de la valeur de la fonction rowCount()

  if ($count == 0){ // si il n'y a pas d'occurence
    echo "&nbsp;Le parcours sélectionné n'existe pas";
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

?>

<h1>Parcours "<?php echo htmlspecialchars($interprete["nomVoie"]); ?>"</h1>

<div class="row">
  <div id="box">
    <center>
      <?php
      if(isset($_REQUEST['submitCourseNameEdit'])) //button name "submitCourseNameEdit"
      {
        $titre = strip_tags($_REQUEST['titre']);

         try
         {
           $db->query("UPDATE voie SET nomVoie=\"$titre\" WHERE codeVoie=$id");   // on update le titre
           $goodMessage = "Titre modifié avec succès. Raffraichissement...";
           header("refresh:2; edit_course.php?id=$id");
         }
         catch(PDOException $e)
         {
          echo $e->getMessage();
         }
        }

        if(isset($_REQUEST['submitCourseDescEdit'])) //button name "submitCourseDescEdit"
        {
          $description = strip_tags($_REQUEST['description']);

           try
           {
             $db->query("UPDATE voie SET description=\"$description\" WHERE codeVoie=$id");    // on update la description
             $goodMessage = "Description modifiée avec succès. Raffraichissement...";
             header("refresh:2; edit_course.php?id=$id");
           }
           catch(PDOException $e)
           {
            echo $e->getMessage();
           }
          }

          if(isset($_REQUEST['submitCourseLevelEdit'])) //button name "submitCourseLevelEdit"
          {
            $level = strip_tags($_REQUEST['niveau']);

             try
             {
               $db->query("UPDATE voie SET difficulteVoie=\"$level\" WHERE codeVoie=$id");    // on update le niveau
               $goodMessage = "Difficultée modifiée avec succès. Raffraichissement...";
               header("refresh:2; edit_course.php?id=$id");
             }
             catch(PDOException $e)
             {
              echo $e->getMessage();
             }
            }

            if(isset($_REQUEST['submitCourseHeightEdit'])) //button name "submitCourseHeightEdit"
            {
              $longueur = strip_tags($_REQUEST['hauteur']);

               try
               {
                 $db->query("UPDATE voie SET longueurVoie=$longueur WHERE codeVoie=$id");    // on update la hauteur
                 $goodMessage = "Hauteur modifiée avec succès. Raffraichissement...";
                 header("refresh:2; edit_course.php?id=$id");
               }
               catch(PDOException $e)
               {
                echo $e->getMessage();
               }
              }

       ?>


      <?php
        if(isset($goodMessage))
        {
        ?>
         <div class="success">
           <?php echo $goodMessage; ?>
         </div>
        <?php
        }
        ?>

      <h3>Édition du parcours "<?php echo htmlspecialchars($interprete["nomVoie"]); ?>"</h3>
      <hr>
      <form method="post">
        <label>Titre du parcours :</label>
        <input type="text" name="titre" value="<?php echo htmlspecialchars($interprete["nomVoie"]);?>"><br>
        <button type="submit" name="submitCourseNameEdit">Mettre à jour</button>
      </form>
        <br>
        <form method="post">
        <label>Description du parcours :</label>
        <textarea type="textarea" id="description" name="description" style="text-align: left;height:140px;resize:none;" /><?php echo htmlspecialchars($interprete["description"]);?></textarea>
        <br>
        <button type="submit" name="submitCourseDescEdit">Mettre à jour</button>
        </form>
        <form method="post">
        <label>Hauteur (en cm) :</label>
        <input type="text" name="hauteur" value="<?php echo htmlspecialchars($interprete["longueurVoie"]);?>">
        <br>
        <button type="submit" name="submitCourseHeightEdit">Mettre à jour</button>
        </form>
        <form method="post">
        <label>Niveau requis :</label>
        <div class="select">
          <select name="niveau">
          <?php
            $result=$db->query("SELECT * FROM niveau;");
            $result->setFetchMode(PDO::FETCH_ASSOC);
          ?>

          <?php while ($select = $result->fetch()): ?>
            <option value="<?php echo htmlspecialchars($select['niveau']) ?>"><?php echo htmlspecialchars($select['niveau']) ?></option>
          <?php endwhile; ?>
          </select>
            <div class="select_arrow"></div>
          </div><br>
        <br>
        <button type="submit" name="submitCourseLevelEdit">Mettre à jour</button>
      </form>
      <hr>
      <a href="view_club.php?id=<?php echo $interprete["codeSite"]; ?>">Retour</a>
    </center>
  </div>
</div>

<?php include("includes/footer.php"); ?>
