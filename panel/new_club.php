<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
   header("location:../index.php?logged=no_access"); // redirect
  }
?>

<h1>Créer un nouveau parcours</h1>

<div class="row">


  <?php
  if(isset($_REQUEST['submitClub'])) // si click sur btn submitClub
  {
    $nom = strip_tags($_REQUEST["nom"]); // on stock tout
    $localite = strip_tags($_REQUEST["localite"]);
    $responsable = strip_tags($_REQUEST["responsable"]);
    $statut = strip_tags($_REQUEST["statut"]);
     try
     {

      if (empty($nom) || strlen($nom) < 3){ // si le nom est vide ou inférieur a 3 caractères
        $errorClubMessage[]="Merci de saisir un nom valide";
      }

      else if(!isset($errorRequestMessage)) // si aucun msg d'erreur
      {

       $insert_club=$db->prepare("INSERT INTO site VALUES (NULL, :nom, :localite, :responsable, :statut);");   // on insert le club

       if($insert_club->execute(array(':nom'=>$nom, ':localite'=>$localite,':responsable'=>$responsable, ':statut'=>$statut))){

        $id_sql = $db->query("SELECT codeSite FROM site WHERE responsable_id=$responsable;")->fetch(); // du coup on récupère l'ID du club créé
        $id_new = $id_sql["codeSite"]; // on stocke l'ID.
        $db->query("UPDATE adherent SET codeSite=$id_new WHERE codeAdherent=$responsable;"); // et on y ajoute le responsable par la même occasion
        $goodClubMessage="Le club a été créé avec succès. Redirection..."; // msg de succès
        header("refresh:3; clubs.php");
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
     if(isset($errorClubMessage))
     {
      foreach($errorClubMessage as $errorclub)
      {
      ?>
       <div class="error"><?php echo $errorclub; ?></div>
         <?php
      }
     }
     if(isset($goodClubMessage))
     {
     ?>
      <div class="success">
        <?php echo $goodClubMessage; ?>
      </div>
     <?php
     }
     ?>
    <center> <h3>Formulaire de création d'un club</h3>
      <hr>

      <form method="POST">
        <label>Nom du club :</label>
        <input type="text" id="nom" name="nom" placeholder="Nom du club" /><br>


        <label>Statut :</label>
        <div class="select">
        <select id="statut" name="statut">
          <option value="1">Déverrouillé</option>
          <option value="0">Verrouillé</option>
          <option value="-1">Banni</option>
        </select>
        <div class="select_arrow">
    </div>
  </div><br>

        <br><label>Responsable :</label>
        <div class="select">
          <select name="responsable">
          <?php
            $membres=$db->query("SELECT * FROM adherent WHERE codeSite IS NULL;"); // selectionne les membres qui n'ont pas de club
            $membres->setFetchMode(PDO::FETCH_ASSOC);
          ?>

          <?php while ($select = $membres->fetch()): ?>
            <option value="<?php echo htmlspecialchars($select['codeAdherent']) ?>"><?php echo htmlspecialchars($select['nomUtilisateur']) ?></option>
          <?php endwhile; ?>
          </select>
            <div class="select_arrow"></div>
          </div><br>

          <br><label>Localité :</label>
          <div class="select">
            <select name="localite">
            <?php
              $departements=$db->query("SELECT departement_nom FROM departement;"); // selectionne tous les départements
              $departements->setFetchMode(PDO::FETCH_ASSOC);
            ?>

            <?php while ($select = $departements->fetch()): ?>
              <option value="<?php echo htmlspecialchars($select['departement_nom']) ?>"><?php echo htmlspecialchars($select['departement_nom']) ?></option>
            <?php endwhile; ?>
            </select>
            <div class="select_arrow"></div>
            </div>
            <br>

        <button type="submit" name="submitClub" id="submitClub">Ajouter le club</button>
      </form>

    </center>
   </div>


</div>
<br>
<?php include("includes/footer.php"); ?>
