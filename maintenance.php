<?php
require_once "includes/mysql.php";
?>

<?php
$sql = "SELECT * FROM website LIMIT 1"; // on séléctionne les infos du site dans la table website
$result = $db->query($sql);
$result->setFetchMode(PDO::FETCH_ASSOC);
$config = $result->fetch();

	if ($config["maintenance"] == 0){ // si la maintenance est sur "0" alors on redirige l'utilisateur sur le site, il a rien a faire sur cette page
	  header( "Refresh:0; url=index.php", true, 303);
	}
?>
<center>
<h2>Oups... Notre site est en maintenance !</h1>
<br>
<?php if ($config["raison_maintenance"] != ""){ // si une raison est spécifiée on l'affiche

  $raison = $config["raison_maintenance"];

  echo "<i>Raison: $raison.</i>";
}
?>
<br><h4>Revenez plus tard!</h4>
<br>
<br>
<a href="./panel">Accès administrateur</a>
</center>
