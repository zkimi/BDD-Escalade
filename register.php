<div id="container">
<?php include("includes/header.php"); ?>
<h1>Inscription au panel de gestion</h1>

<?php

if (!isset($_SESSION["user_login"])){ // si aucune session n'est lancée

if(isset($_REQUEST['submitBtnRegister'])) //si le formulaire est envoyé avec un clic bouton -> "submitBtnRegister"
{
 $nom = strip_tags($_REQUEST['nom']); //textbox "nom"
 $prenom = strip_tags($_REQUEST['prenom']); //textbox "prenom"
 $username = strip_tags($_REQUEST['username']); //textbox "username"
 $email  = strip_tags($_REQUEST['email']);  //textbox "email"
 $password = strip_tags($_REQUEST['password']); //textbox "password"
 $niveau = strip_tags($_REQUEST['niveau']); //textbox "niveau"

 if(!empty($_SERVER['HTTP_CLIENT_IP'])){ // récupérer l'ip du visiteur de différentes manières
        //ip depuis protocole internet HTTP
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        // sinon ip depuis proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
  }
  if(empty($nom)){ // si le nom est vide
   $errorMsg[]="Veuillez saisir votre nom"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
  }
  if(empty($prenom)){ // si le prenom est vide
   $errorMsg[]="Veuillez saisir votre prénom"; //on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
  }
 if(empty($username)){ // si le pseudo est vide
  $errorMsg[]="Veuillez saisir votre nom d'utilisateur"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(empty($email)){ // si l'email est vide
  $errorMsg[]="Veuillez saisir votre adresse électronique"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ // si l'email n'est pas valide
  $errorMsg[]="Veuillez entrer une adresse électronique valide"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(preg_match('/[^a-zA-Z\d]/', $username)){ // si il y a des caractères spéciaux dans le pseudo
   $errorMsg[]="Le pseudo ne doit pas comporter de caractères spéciaux"; //on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(empty($password)){ // si le mdp est vide
  $errorMsg[]="Veuillez entrer votre mot de passe"; //on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(empty($niveau)){ // si le niveau est vide
  $errorMsg[]="Merci de sélectionner votre niveau d'escalade"; //check passowrd textbox not empty
 }
 else if(strlen($password) < 6){ // si la longueur du mdp est inférieure à 6
  $errorMsg[] = "Le mot de passe doit comporter au moins 6 caractères"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else if(strlen($username) < 6){ // si la longueur du pseudo est inferieure à 6
  $errorMsg[] = "Le nom d'utilisateur doit comporter au moins 6 caractères"; // on inscrit un message d'erreur dans un tableau (si il y en a plusieurs)
 }
 else
 {
  try
  {
   $select_registered_users=$db->prepare("SELECT nomUtilisateur, adresseMailAdherent FROM adherent WHERE nomUtilisateur=:uname OR adresseMailAdherent=:uemail"); // on séléctionne les utilisateurs avec le pseudo rentré ou l'adresse mail rentrée
   $select_registered_users->execute(array(':uname'=>$username, ':uemail'=>$email)); // on éxecute la requette avec les paramètres
   $row=$select_registered_users->fetch(PDO::FETCH_ASSOC); // et la méthode de recherche

   if($row["nomUtilisateur"]==$username){ // si dans le résultat un pseudo correspond au pseudo rentré
    $errorMsg[]="Désolé, ce pseudo existe déjà"; // on inscrit un msg d'erreur
   }
   else if($row["adresseMailAdherent"]==$email){ // sinon si dans le résultat une adresse mail correspond au mail rentré
    $errorMsg[]="Désolé, cet email existe déjà"; // on inscrit un msg d'erreur
   }
   else if(!isset($errorMsg)) // sinon si le tableau des messages d'erreur $errorMsg n'est pas initialisé
   {
    $new_password = password_hash($password, PASSWORD_DEFAULT); // alors on encrypte le mdp rentré

    $insert_user=$db->prepare("INSERT INTO adherent (nomUtilisateur,adresseMailAdherent,mdpAdherent,ipAdherent,registerDate,lastLogin,nomAdherent,prenomAdherent,niveau) VALUES
                (:uname,:uemail,:upassword,:uip,NOW(),NOW(),:unom,:uprenom,:univeau)");   // puis on insert l'utilisateur dans la db

    if($insert_user->execute(array( ':unom'=>$nom,
                                    ':uprenom'=>$prenom,
                                    ':uname' =>$username,
                                    ':uemail'=>$email,
                                    ':univeau'=>$niveau,
                                    ':upassword'=>$new_password,
                                    ':uip'=>$ip))){ // avec ses paramètres pour compléter la requete.

     $registerMsg="Vous vous êtes inscrit avec succès! Redirection...<meta http-equiv=\"refresh\" content=\"2;URL=login.php\">"; // on affiche un message de succès
    }
   }
  }
  catch(PDOException $e)
  {
   echo $e->getMessage();
  }
 }
}
} else { // sinon
  header("location:index.php"); // redirection vers l'index
}
?>

<?php
if(isset($errorMsg)) // si le tableau errorMsg est initialisé
{
 foreach($errorMsg as $error) // pour chaque ligne du tableau on initalise une variable
 {
 ?>
  <div class="error"><?php echo $error; // on affiche cette variable ?></div>
    <?php
 }
}
if(isset($registerMsg)) // si un message de succès est initalisé
{
?>
 <div class="success">
   <?php echo $registerMsg; // on l'affiche ?>
 </div>
<?php
}
?>



<form method="POST">
<input type="text" id="nom" name="nom" placeholder="Nom de famille"/>
<input type="text" id="prenom" name="prenom" placeholder="Prénom"/>
<input type="text" id="username" name="username" placeholder="Pseudo"/>

<div class="select">
<select id="niveau" name="niveau" style="margin: 0 auto;">
  <option value="1" selected="selected">Niveau d'escalade</option>
  <?php
    $levels=$db->query("SELECT niveau FROM niveau;"); // on récupère tous les niveaux de la table niveau
    $levels->setFetchMode(PDO::FETCH_ASSOC); // avec son mode de recherche
  ?>
  <?php while ($levels_label = $levels->fetch()): // pour chaque ligne de la DB on crée une option avec le niveau correspondant ?>
    <option value="<?php echo htmlspecialchars($levels_label['niveau']) ?>"><?php echo htmlspecialchars($levels_label['niveau']) ?></option>
  <?php endwhile; ?>
</select>
  <div class="select_arrow"></div>
</div>

<input type="text" id="email" name="email" placeholder="Adresse e-mail"/>
<input type="password" id="password" name="password" placeholder="Mot de passe" />

<button type="submit" name="submitBtnRegister" id="submitBtnRegister">Inscription</button>
</form>





<center><small>Vous avez déjà un compte ? <a href="login.php">Connectez-vous !</a></small></center>

<?php include("includes/footer.php"); // on inclue le pied de page ?>
