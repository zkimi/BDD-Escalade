<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no_access");
  }

  if (!isset($_GET["id"])){ // si le champ id n'est pas init.
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if(empty($_GET["id"])){ // si le champ id est vide.
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die; 
  } else if(!is_numeric($_GET["id"])){ // si le champ id comprend autre que des chiffres.
    echo "&nbsp;Le numéro du club sélectionné n'est pas correct.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT site.codeSite,site.nomSite,site.localite,site.responsable_id,site.statut,adherent.nomUtilisateur FROM site,adherent WHERE site.codeSite=$id AND site.responsable_id=adherent.codeAdherent LIMIT 1";
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){
    echo "&nbsp;Le club sélectionné n'existe pas";
    die;
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
}
    ?>
<h1>Édition de votre club : <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>
<br>
<div class="row">
  <div id="box">

    <?php
    if(isset($_REQUEST['changeClubName'])) //button name "submitBtnUpdate"
    {
      $clubName = strip_tags($_REQUEST['clubName']);  // stocker le nouveau nom

       try
       {
         $db->query("UPDATE site SET nomSite=\"$clubName\" WHERE codeSite=$id");   // on modifie le nom du club avec un UPDATE
         header("location:edit_club.php?id=$id");
       }
       catch(PDOException $e)
       {
        echo $e->getMessage();
       }
      }
     ?>

     <?php
     if(isset($_REQUEST['changeClubArea'])) //button name "submitBtnUpdate"
     {
       $clubArea = strip_tags($_REQUEST['clubArea']);  // stocker la localite

        try
        {
          $db->query("UPDATE site SET localite=\"$clubArea\" WHERE codeSite=$id");   // on modifie la localité du club avec un UPDATE
          header("location:edit_club.php?id=$id");
        }
        catch(PDOException $e)
        {
         echo $e->getMessage();
        }
       }
      ?>

     <?php
     if(isset($_REQUEST['changeClubManager'])) //button name "submitBtnUpdate"
     {
       $managerName = strip_tags($_REQUEST['managerName']); // stocker l'ID du manager

        try
        {
          $db->query("UPDATE site SET responsable_id=$managerName WHERE codeSite=$id");   // on modifie le responsable du club avec un UPDATE
          header("location:view_club.php?id=$id"); // vu que l'utilisateur en question n'est plus reponsable on le redirige vers la page du club
        }
        catch(PDOException $e)
        {
         echo $e->getMessage();
        }
       }
      ?>

    <center>
      <h3>Informations générales</h3>
    <hr>
      <form method="POST">
        <label>Nom du club :</label>
        <input type="text" name="clubName" value="<?php echo htmlspecialchars($interprete["nomSite"]); ?>">
        <button type="submit" name="changeClubName">Changer le nom</button>
      </form>

      <form method="POST">
        <label>Localisation :</label>

        <div class="select">
          <select name="clubArea">
          <?php
            $result=$db->query("SELECT departement_nom FROM departement;");
            $result->setFetchMode(PDO::FETCH_ASSOC);
          ?>

          <?php while ($select = $result->fetch()): ?>
            <option value="<?php echo htmlspecialchars($select['departement_nom']) ?>"><?php echo htmlspecialchars($select['departement_nom']) ?></option>
          <?php endwhile; ?>
          </select>
            <div class="select_arrow"></div>
          </div>

        <button type="submit" name="changeClubArea">Changer de localisation</button>

      <br>

      <h3>Gestion du club</h3>
      <hr>
      <form method="POST">
        <i style="opacity:.8;"><B><FONT color="red">Attention!</FONT></B> Si vous changez de responsable, vos permissions concernant ce club seront retirées et vous serez rétrogradés au rang de membre.</i><br>
        <br>
        <label>Responsable :</label>

        <div class="select">
          <select name="managerName">
          <?php
            $result=$db->query("SELECT adherent.codeAdherent,adherent.nomUtilisateur FROM adherent,site WHERE adherent.codeSite=$id GROUP BY adherent.nomUtilisateur;"); // on séléctionne tous les adhérents de ce club.
            $result->setFetchMode(PDO::FETCH_ASSOC); // méthode de recherche
          ?>
          <?php while ($select = $result->fetch()): // pour chaque ligne = une option ?>
            <option value="<?php echo htmlspecialchars($select['codeAdherent']) ?>" selected="selected"><?php echo htmlspecialchars($select['nomUtilisateur']) ?></option>
          <?php endwhile; ?>
          </select>
            <div class="select_arrow"></div>
          </div>

        <button type="submit" name="changeClubManager">Changer de responsable</button>
      </form>
    </center>

  </div>
</div>
<br>
<?php include("includes/footer.php"); ?>
