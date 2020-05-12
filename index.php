    <div>
  <?php include("includes/header.php"); // inclusion de l'entête
  if(isset($_GET["logged"])){ // si le paramètre logged est initalisé

    if ($_GET["logged"] == "no"){ // si il est égal à "no"
      echo "<div class=\"error\">Vous devez être connecté pour accéder à cette page.</div>"; // affichage d'un message d'erreur
    }

    if ($_GET["logged"] == "no_access"){ // si il est égal à "no_access"
      echo "<div class=\"error\">Vous n'avez pas accès à cette page.</div>"; // affichage d'un message d'erreur
    }
  }
  ?>

    <h1>Bienvenue <?php
    if(!isset($_SESSION['user_login'])) // si la session n'est pas initalisée
      {
        echo""; // on affiche rien a coté de bienvenue
      } else { echo strtoupper($row['nomAdherent']); echo" "; echo strtoupper($row['prenomAdherent']); } // sinon on affiche le nom et prénom a coté de bienvenue?>!</h1>
    <p>
      Bénéficiez grace à cette interface, d'un total controle sur votre club d'escalade !<br>
      <a href="register.php">Inscrivez vous</a> dès maintenant et prenez contact avec un administrateur pour mettre votre business en place ou <a href="login.php">connectez-vous</a> si vous avez déjà un compte.
    </p>


  <?php include("includes/footer.php"); // inclusion du pied de page ?>
