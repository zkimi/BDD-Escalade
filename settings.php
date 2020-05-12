<div id="container" style="height:auto;">
<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user not access in "welcome.php" page
  {
    header("location:index.php?logged=no");
  }
?>

<h1>Mes paramètres personnels</h1>

<style>
  #box {
    background-color: white;
    border-radius: 5px;
    padding:5px;
    width: 47%;
    height: auto;
    margin: 0 auto;
  }
</style>
<div class="row">
<div class="left">
<div id="box" style="width:95%;"><h1>Mes informations</h1>
<p><u>Nom d'utilisateur :</u> <b><?php echo htmlspecialchars($row['nomUtilisateur']); ?></b></p>
<p><u>Nom :</u> <?php
if(is_null($row['nomAdherent'])){
  echo"<i>Non spécifié</i>";
} else{
  echo $row['nomAdherent'];
}
?></p>
<p><u>Prénom :</u> <?php
if(is_null($row['prenomAdherent'])){
  echo"<i>Non spécifié</i>";
} else{
  echo $row['prenomAdherent'];
}
?></p>
<p><u>Niveau :</u> <?php echo strtoupper($row['niveau']); ?></p>
<p><u>Dernière connexion :</u> <?php edit_date_format($row['lastLogin']); ?></p>
<p><u>Date d'inscription :</u> <?php edit_date_format($row['registerDate']); ?></p>

</div>
</div>
<div class="right">
<div id="box" style="width:95%;"><h1>Modifier mon email</h1>
  <?php
  if(isset($_REQUEST['submitBtnUpdateEmail'])) // si click sur btn "submitBtnUpdateEmail"
  {
    $email  = strip_tags($_REQUEST['email']);  // stocker "email"

    if(empty($email)){ // si l'email est vide
     $errorEmailMsg[]="Veuillez saisir une adresse électronique"; //msg d'erreur
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ // si l'email est incorrect 
     $errorEmailMsg[]="Veuillez entrer une adresse électronique valide"; //msg d'erreur
    }
    else
    {
     try
     {
      $select_stmt=$db->prepare("SELECT adresseMailAdherent FROM adherent WHERE adresseMailAdherent=:uemail"); // requete qui recherche si cette email existe dans la BDD

      $select_stmt->execute(array(':uemail'=>$email)); // on execute avec l'email saisi
      $row=$select_stmt->fetch(PDO::FETCH_ASSOC);

      if($row["adresseMailAdherent"]==$email){ // si une adresse correspond
       $errorEmailMsg[]="Désolé, cet email existe déjà"; // msg d'erreur
      }
      else if(!isset($errorEmailMsg)) // sinon
      {
       $insert_stmt=$db->prepare("UPDATE adherent SET adresseMailAdherent=:uemail WHERE codeAdherent=:uid");   // on update l'email de l'user

       if($insert_stmt->execute(array( ':uemail'=>$email,
                                       ':uid'=>$id))){

        $EmailGoodMsg="Votre email a été mis à jour avec succès. Rechargement de la page..."; // msg succès
        header("refresh:2; settings.php");
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
<?php
function obfuscate_email($email)
{
    $em   = explode("@",$email);
    $name = implode(array_slice($em, 0, count($em)-1), '@');
    $len  = floor(strlen($name)/2);
    echo substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
}
 ?>
 <?php
 if(isset($errorEmailMsg))
 {
  foreach($errorEmailMsg as $error)
  {
  ?>
   <div class="error"><?php echo $error; ?></div>
     <?php
  }
 }
 if(isset($EmailGoodMsg))
 {
 ?>
  <div class="success">
    <?php echo $EmailGoodMsg; ?>
  </div>
 <?php
 }
 ?>
<center><p><u>Adresse mail actuelle :</u>  <?php obfuscate_email($row['adresseMailAdherent']); ?></p></center>
 <form method="POST">
 <input type="text" id="email" name="email" placeholder="Nouvelle adresse e-mail"/>
 <button type="submit" name="submitBtnUpdateEmail" id="submitBtnUpdateEmail">Mettre à jour</button>
 </form>
</div>
<div style="clear:both;"></div>

<?php
if(isset($_REQUEST['submitBtnUpdatePass'])) // si click sur  "submitBtnUpdatePass"
{
  $actualpassword = strip_tags($_REQUEST['actualpassword']); // on stock le mdp actuel
  $newpassword = strip_tags($_REQUEST['newpassword']); // on stock le nouveau mdp 

  if(empty($actualpassword)){ // si le mdp actuel est vide
   $errorPassMsg[]="Veuillez entrer votre mot de passe actuel"; // msg d'erreur
  }
  else if(empty($newpassword)){ // si le nouveau mdp est vide
   $errorPassMsg[]="Veuillez entrer votre nouveau mot de passe"; // msg d'erreur
  }
  else if(strlen($newpassword) < 6){ // si la longueur du nouveau mdp est inférieur à 6
   $errorPassMsg[] = "Le nouveau mot de passe doit comporter au moins 6 caractères"; // msg d'erreur
  }
  else
  {
   try
   {
    $select_pass=$db->prepare("SELECT codeAdherent, mdpAdherent FROM adherent WHERE codeAdherent=:uid OR mdpAdherent=:upass"); // on séléctionne le mdp de l'user en question

    $select_pass->execute(array(':upass'=>$actualpassword,':uid'=>$id)); // on execute la requete avec le paramètre id et mdp actuel
    $result_mdp=$select_pass->fetch(PDO::FETCH_ASSOC);

    if(!password_verify($actualpassword, $result_mdp['mdpAdherent'])){ // si le mdp actuel ne correspond pas a celui saisi
     $errorPassMsg[]="Le mot de passe actuel ne correspond pas";  // msg d'erreur 
    }

    else if(!isset($errorPassMsg)) // si aucune erreur
    {
     $new_password = password_hash($newpassword, PASSWORD_DEFAULT); // on encrypte le nouveau mdp

     $insert_pass=$db->prepare("UPDATE adherent SET mdpAdherent=:upassword WHERE codeAdherent=:uid");   // et on le met a jour dans la BDD

     if($insert_pass->execute(array( ':upassword'=>$new_password,
                                     ':uid'=>$id))){

      $PassGoodMsg="Votre mot de passe a été mis à jour avec succès."; // message de succès
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
<div id="box" style="margin-top:20px;width:95%;"><h1>Modifier mon mot de passe</h1>
  <?php
  if(isset($errorPassMsg))
  {
   foreach($errorPassMsg as $errorpass)
   {
   ?>
    <div class="error"><?php echo $errorpass; ?></div>
      <?php
   }
  }
  if(isset($PassGoodMsg))
  {
  ?>
   <div class="success">
     <?php echo $PassGoodMsg; ?>
   </div>
  <?php
  }
  ?>
  <form method="POST">
    <center><label><u>Votre mot de passe actuel :</u></label></center><br>
    <input type="password" id="actualpassword" name="actualpassword" placeholder="Mot de passe actuel" />
    <center><label><u>Votre nouveau mot de passe :</u></label></center><br>
    <input type="password" id="newpassword" name="newpassword" placeholder="Nouveau mot de passe" />
     <button type="submit" name="submitBtnUpdatePass" id="submitBtnUpdatePass">Mettre à jour</button>
  </form>

</div>
</div>
<br>
<div style="clear:both;"></div>

</div>

<?php include("includes/footer.php"); ?>
