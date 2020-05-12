
<?php
  define('HOST','localhost'); // hote
  define('DB_NAME', 'escalade'); // nom de la bdd
  define('USER', 'root'); // nom d'utilisateur
  define('PASS',''); // mdp.

  try{
    $db = new PDO("mysql:host=". HOST .";dbname=".DB_NAME, USER, PASS, array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')); // connexion a la BDD avec paramètre pour avoir les accents sous forme UTF8.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  } catch(PDOException $e){ // si erreur on l'affiche
    echo $e;
  }
?>
