<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
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

<h1>Créer un nouveau parcours</h1>

<div class="row">


  <?php
  if(isset($_REQUEST['submitCourse'])) // si le btn submitCourse est cliqué
  {
    $nom = strip_tags($_REQUEST["nom"]); // on stock toutes les valeurs
    $type = strip_tags($_REQUEST["type"]);
    $desc = strip_tags($_REQUEST["desc"]);
    $niveau = strip_tags($_REQUEST["niveau"]);
    $hauteur = strip_tags($_REQUEST["hauteur"]);
     try
     {

      if (empty($nom) || strlen($nom) < 3){ // si le nom est vide ou inférieur a 3 caractères
        $errorCourseMessage[]="Merci de saisir un titre valide";
      }

      if (empty($desc)){ // si la description est vide
        $errorCourseMessage[]="Merci de saisir une description valide";
      }

      if (!is_numeric($hauteur)){ // si la hauteur est vide
        $errorCourseMessage[]="Merci de saisir une hauteur valide (juste les chiffres)";
      }

      else if(!isset($errorCourseMessage)) // si aucun msg d'erreur
      {

       $insert_request=$db->prepare("INSERT INTO voie VALUES (NULL, :nom, :description, :hauteur, :type, :niveau, :id);");   // on insert le parcours

       if($insert_request->execute(array(':nom'=>$nom, ':description'=>$desc,':hauteur'=>$hauteur, ':type'=>$type, ':niveau'=>$niveau, ':id'=>$id))){

        $goodCourseMessage="Le parcours a été créé avec succès. Redirection..."; // msg de succès
        header("refresh:3; view_club_courses.php?id=$id");
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
     if(isset($errorCourseMessage))
     {
      foreach($errorCourseMessage as $errorcourse)
      {
      ?>
       <div class="error"><?php echo $errorcourse; ?></div>
         <?php
      }
     }
     if(isset($goodCourseMessage))
     {
     ?>
      <div class="success">
        <?php echo $goodCourseMessage; ?>
      </div>
     <?php
     }
     ?>
    <center> <h3>Formulaire de création d'un parcours</h3>
      <hr>

      <form method="POST">
        <label>Type de parcours :</label>
        <div class="select">
        <select id="type" name="type">
          <option value="Bloc">Bloc</option>
          <option value="Falaise">Falaise</option>
        </select>
        <div class="select_arrow">
    </div>
  </div><br>
        <br><label>Nom du parcours :</label>
        <input type="text" id="nom" name="nom" placeholder="Nom" />

        <br><label>Description du parcours (durée, cadre, ...) :</label>
        <textarea type="textarea" id="desc" name="desc" placeholder="La description du parcours..." style="text-align: left;height:140px;resize:none;" /></textarea>

        <br><label>Niveau requis :</label>
        <div class="select">
          <select name="niveau">
          <?php
            $result=$db->query("SELECT * FROM niveau;"); // on séléctionne tout les niveaux
            $result->setFetchMode(PDO::FETCH_ASSOC);
          ?>

          <?php while ($select = $result->fetch()): ?>
            <option value="<?php echo htmlspecialchars($select['niveau']) ?>"><?php echo htmlspecialchars($select['niveau']) ?></option>
          <?php endwhile; ?>
          </select>
            <div class="select_arrow"></div>
          </div><br>

          <br><label>Hauteur du parcours :</label>
          <input type="text" id="hauteur" name="hauteur" placeholder="Hauteur en cm" />

        <button type="submit" name="submitCourse" id="submitCourse">Ajouter le parcours</button>
      </form>

    </center>
   </div>


</div>
<br>
<?php include("includes/footer.php"); ?>
