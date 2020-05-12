    <div id="container">
<?php include("includes/header.php"); ?>
<h1>Connexion au panel de gestion</h1>

<?php

if(isset($_SESSION["user_login"])) // si une session est déjà lancée
{
 header("location: index.php"); // redirection vers l'index
}

if(isset($_REQUEST['submitBtnLogin'])) //si le formulaire est envoyé avec un clic bouton -> "submitBtnLogin"
{
 $username =strip_tags($_REQUEST["username_or_email"]); //textbox "username_or_email"
 $email  =strip_tags($_REQUEST["username_or_email"]); //textbox "username_or_email"
 $password =strip_tags($_REQUEST["password"]);   //textbox "password"

 if(!empty($_SERVER['HTTP_CLIENT_IP'])){ // récupérer l'ip du visiteur de différentes manières
        //ip depuis protocole internet HTTP
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        // sinon ip depuis proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
  }

 if(empty($username)){ // si le nom est vide
  $errorMsg[]="Veuillez saisir votre nom d'utilisateur ou votre adresse électronique"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(empty($email)){ // si l'email est vide
  $errorMsg[]="Veuillez saisir votre nom d'utilisateur ou votre adresse électronique"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(empty($password)){ // si le mdp est vide
  $errorMsg[]="Veuillez saisir votre mot de passe"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else
 {
  try
  {
   $select_registered_users=$db->prepare("SELECT * FROM adherent WHERE nomUtilisateur=:uname OR adresseMailAdherent=:uemail"); // on selectionne les utilisateurs avec ce pseudo ou cet email
   $select_registered_users->execute(array(':uname'=>$username, ':uemail'=>$email)); // et on execute la requete avec les champs rentrés par l'utilisateur
   $row=$select_registered_users->fetch(PDO::FETCH_ASSOC); // avec la methode de recherche

   if($select_registered_users->rowCount() > 0) // si la requête compte plus de zéro lignes alors
   {
    if($username==$row["nomUtilisateur"] OR $email==$row["adresseMailAdherent"]) // on vérifie si la ligne est bien égale avec le pseudo et l'email rentré par l'utilisateur
    {
     if(password_verify($password, $row["mdpAdherent"])) // on compare le mdp encrypté stocké en base de donné et le mdp rentré par l'utilisateur
     {
      $_SESSION["user_login"] = $row["codeAdherent"]; // on démarre une session avec l'id user_login qui correspondra a l'id de l'adherent
      $update_user=$db->prepare("UPDATE adherent SET ipAdherent=:uip, lastLogin=NOW() WHERE codeAdherent=:uid"); // on ecrit une requete pour mettre à jour l'ip de l'utilisateur et sa dernière connexion
      $update_user->execute(array(':uip'=>$ip,':uid'=>$row["codeAdherent"])); // avec les paramètres récupérés
      $loginMsg = "Connecté avec succès ! Redirection...";  // on initialise un message de succès
      header("refresh:2; index.php");   // après 2 secondes on redirige l'utilisateur sur la page d'index
     }
     else // si la vérification du mot de passe échoue
     {
      $errorMsg[]="Mauvais mot de passe"; // on inscrit un msg d'erreur
     }
    }
    else // si la comparaison avec l'entrée de l'utilisateur et la db echoue
    {
     $errorMsg[]="Mauvais nom d'utilisateur ou adresse électronique"; // on inscrit un msg d'erreur
    }
   }
   else // si la comparaison avec l'entrée de l'utilisateur et la db echoue
   {
    $errorMsg[]="Mauvais nom d'utilisateur ou adresse électronique";// on inscrit un msg d'erreur
   }
  }
  catch(PDOException $e)
  {
   $e->getMessage();
  }
 }
}
?>

<?php
if(isset($errorMsg)) // si le tableau errorMsg est initialisé
{
 foreach($errorMsg as $error) // pour chaque ligne du tableau on initalise une variable
 {
 ?>
  <div class="error">
   <?php echo $error // on affiche la variable ; ?>
  </div>
    <?php
 }
}
if(isset($loginMsg)) // si un message de succès est initialisé
{
?>
 <div class="success">
  <?php echo $loginMsg; // on affiche ce message ?>
 </div>
<?php
}
?>


  <form method="POST">
    <input type="text" id="username_or_email" name="username_or_email" placeholder="Utilisateur ou adresse e-mail"/>
    <input type="password" id="password" name="password" placeholder="Mot de passe" />

    <button type="submit" name="submitBtnLogin" id="submitBtnLogin">Connexion</button>
  </form>





  <center><small>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous !</a></small></center>

  <?php include("includes/footer.php"); // inclusion du pied de page ?>
