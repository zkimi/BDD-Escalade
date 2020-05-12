<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) // si user n'est pas log ou n'est pas admin
  {
    header("location:../index.php?logged=no_access"); // redirection
  }

  if (!isset($_GET["id"])){ // si champ id n'est pas init
    echo "&nbsp;Vous devez séléctionner un utilisateur.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez séléctionné aucun utilisateur.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT * FROM adherent WHERE codeAdherent=$id LIMIT 1"; // on séléctionne l'adherent en question
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
<h1>Modification de l'utilisateur : <?php echo $interprete["nomUtilisateur"];?></h1>
<br>

<div class="left">
<div id="box" style="width:95%;">

  <?php
  if(isset($_REQUEST['submitnickname'])) // si clik sur bouton "submitnickname"
  {
    $nickname = strip_tags($_REQUEST['newnickname']); // on récup le nouveau pseudo

    if(empty($nickname)){ // si le pseudo est vide
     $errorNicknameMessage[]="Veuillez entrer un pseudo"; 
    }
    else
    {
     try
     {
      $select_nickname=$db->prepare("SELECT nomUtilisateur FROM adherent WHERE nomUtilisateur=:pseudo"); // on séléctionne un utilisateur avec un pseudo correspondant

      $select_nickname->execute(array(':pseudo'=>$nickname)); // on execute avec le parametre pseudo
      $result_nickname=$select_nickname->fetch(PDO::FETCH_ASSOC);

      if($select_nickname->rowCount() > 0){ // si une ligne existe

       $errorNicknameMessage[]="Un utilisateur possède déjà ce pseudo"; 

      }

      else if(!isset($errorNicknameMessage)) // sinon si aucun message d'erreur
      {
       $insert_nickname=$db->prepare("UPDATE adherent SET nomUtilisateur=:pseudo WHERE codeAdherent=:uid"); // on update le pseudo du membre

       if($insert_nickname->execute(array( ':pseudo'=>$nickname,
                                       ':uid'=>$id))){

        $goodNicknameMessage="Le pseudo a été modifié avec succès."; // avec un message de succès
       }
      }
     }
     catch(PDOException $e)
     {
      echo $e->getMessage();
     }
    }
   }
   ?>

<h1>Changer son pseudo</h1>
<?php
if(isset($errorNicknameMessage))
{
 foreach($errorNicknameMessage as $errornickname)
 {
 ?>
  <div class="error"><?php echo $errornickname; ?></div>
    <?php
 }
}
if(isset($goodNicknameMessage))
{
?>
 <div class="success">
   <?php echo $goodNicknameMessage; ?>
 </div>
<?php
}
?>

<form method="POST">
  <center><label><u>Son nouveau pseudo :</u></label></center><br>
  <input type="text" id="newnickname" name="newnickname" placeholder="Pseudo" />
   <button type="submit" name="submitnickname" id="submitnickname">Mettre à jour</button>
</form>


</div>


<br>

  <?php
  if(isset($_REQUEST['submitlevel'])) // si clic sur  "submitlevel"
  {
    try{
    $level = strip_tags($_REQUEST['niveau']); // on recup le nouveau niveau 

       $update_level=$db->prepare("UPDATE adherent SET niveau=:niveau WHERE codeAdherent=:uid");   // et on modifie

       if($update_level->execute(array( ':niveau'=>$level,
                                       ':uid'=>$id))){

        $goodLevelMessage="Le niveau a été modifié avec succès."; //msg de succès
       }
     }

     catch(PDOException $e)
     {
      echo $e->getMessage();
     }
   }

   ?>
<div id="box" style="width:95%;">
  <?php
  if(isset($goodLevelMessage))
  {
  ?>
   <div class="success">
     <?php echo $goodLevelMessage; ?>
   </div>
  <?php
  }
  ?>

<h1>Changer le niveau</h1>

<form method="POST">
  <center><label>Niveau :</label>
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

  </center>
   <button type="submit" name="submitlevel" id="submitlevel">Mettre à jour</button>
</form>


</div>

</div>

<div class="right">
<div id="box" style="width:95%;">
<h1>Informations</h1>
<p><u>Nom d'utilisateur :</u> <b><?php echo htmlspecialchars($interprete['nomUtilisateur']); ?> (ID : <?php echo htmlspecialchars($interprete['codeAdherent']); ?>)</b></p>
<p><u>Nom :</u> <?php
if(is_null($interprete['nomAdherent'])){
  echo"<i>Non spécifié</i>";
} else{
  echo $interprete['nomAdherent'];
}
?></p>
<p><u>Prénom :</u> <?php
if(is_null($interprete['prenomAdherent'])){
  echo"<i>Non spécifié</i>";
} else{
  echo $interprete['prenomAdherent'];
}
?></p>
<p><u>Niveau d'escalade :</u> <?php echo $interprete['niveau']; ?></p>
<p><u>Dernière connexion :</u> <?php edit_date_format($interprete['lastLogin']); ?></p>
<p><u>Date d'inscription :</u> <?php edit_date_format($interprete['registerDate']); ?></p>
<p><u>Adresse IP :</u> <?php echo $interprete['ipAdherent']; ?></p>
<p><u>Rang :</u> 
<?php
  if ($interprete['is_admin'] == 1){ // le champ is_admin == 1
    echo "<FONT color=\"RED\"><b>Administrateur</b></FONT>";
  } else {
    echo "Utilisateur";
  }
?>
</p>
</div>
<br>
<div id="box" style="width:95%;">
<h1>Actions sur l'utilisateur</H1>
  <p>
    <div class="actionbtn red"><a href="delete_user.php?id=<?php echo $id;?>">Supprimer l'utilisateur</a></div>
    <?php
    $responsable_du_club=$db->query("SELECT codeSite FROM site WHERE responsable_id=$id;"); // on séléctionne les club avec un ID responsable correspondant
    
    if($responsable_du_club->rowCount() > 0){ // si on a un résultat 

      $id_club = $responsable_du_club->fetch();
      $echo_id = $id_club["codeSite"];

    echo "<div class=\"actionbtn blue\"><a href=\"../view_club.php?id=$echo_id\">Voir le club dont il est responsable</a></div>";
  } 
  ?>
  </p>
</div>

<br>
</div>
