<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) // si l'user est pas log ou est pas admin
  {
    header("location:index.php?logged=no_access"); // redirection
  }

  if (!isset($_GET["id"])){ // si le champ id n'est pas init
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die;
  }
  else {
  $id = $_GET["id"];
  $sql = "SELECT *,site.codeSite FROM site,adherent WHERE site.codeSite=$id AND site.responsable_id=adherent.codeAdherent LIMIT 1"; // on prend le club et son adherent
  $result = $db->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $interprete = $result->fetch();

  if ($result->rowCount() == 0){ // si zéro lignes
    echo "&nbsp;Le club sélectionné n'existe pas";
    die;
  }

  if (isset($_GET["lock"])){ // si le champ lock est init

    if ($_GET["lock"] == 1){ // si = 1

        $sql = "UPDATE site SET statut = 0 WHERE codeSite = $id"; // on met le statut 0
        $result = $db->query($sql);
        $redActionMessage[] = "Le club a été restreint de ses capacités. Actualisation...";
        header( "Refresh:2; url=edit_club.php?id=$id", true, 303);

    } else if ($_GET["lock"] == 0){ // si =0

        $sql = "UPDATE site SET statut = 1 WHERE codeSite = $id";
        $result = $db->query($sql);
        $greenActionMessage = "La restriction de ce club a été levée. Actualisation...";
        header( "Refresh:3; url=edit_club.php?id=$id", true, 303);
    }
  }

  if (isset($_GET["ban"])){ // si champ ban initialisé

    if ($_GET["ban"] == 1){ // s'il est égal à 1

        $db->query("UPDATE adherent SET codeSite = NULL WHERE codeSite=$id"); // on vire tout le monde du club
        $db->query("UPDATE site SET statut = -1 WHERE codeSite = $id"); // on change le statut à -1
        $greenActionMessage = "Le club a été banni. Actualisation...";//message de succès
        header( "Refresh:2; url=edit_club.php?id=$id", true, 303); // refresh

    } else if ($_GET["ban"] == 0){ // si = 0

        $db->query("UPDATE site SET statut = 1 WHERE codeSite = $id"); // on change le statut
        $responsable_id = $db->query("SELECT codeAdherent FROM adherent,site WHERE site.responsable_id = adherent.codeAdherent AND site.codeSite=$id")->fetch();// on prend l'id du responsable
        $getidresp = $responsable_id["codeAdherent"];
        $db->query("UPDATE adherent SET codeSite = $id WHERE adherent.codeAdherent=$getidresp"); // et on réintroduit son responsable
        $greenActionMessage = "Le banissement de ce club a été levé et le club déverrouillé.. Actualisation..."; // message de succès
        header( "Refresh:3; url=edit_club.php?id=$id", true, 303); // refresh

    }
  }

}
    ?>
<style media="screen">
  .categ {
    width: 23%;
    background-color: #e1e1e1;
    font-style: italic;
    padding: 10px;
  }
  #box{
    width: 80%;
    margin: 0 auto;
    background-color: white;
    border-radius:5px;
    border:1px solid black;
    padding:5px;
  }
</style>

<h1>Gestion du club n°<?php echo $interprete["codeSite"]; ?> - <?php echo htmlspecialchars($interprete["nomSite"]); ?></h1>
  <center><i><FONT color="grey">Administrateurs, les modifications que vous effectuez seront visibles par les utilisateurs, soyez prudents.</FONT></i></center>
<br>
<div id="box">
  <center>
<h4 style="margin:2px;">Actions administrateur</h4>
<hr>


<?php
if(isset($redActionMessage)) // si y'a un message d'erreur
{
 foreach($redActionMessage as $redLabel) // pour chaque ligne, on l'affiche
 {
 ?>
  <div class="error"><?php echo $redLabel; ?></div>
    <?php
 }
}
if(isset($greenActionMessage)) // si message de succès
{
?>
 <div class="success">
   <?php echo $greenActionMessage; // on l'affiche ?>
 </div>
<?php
}
?>

<?php
  $SiteID = $interprete["codeSite"]; // stockage de l'id du club dans une autre variable

  if ($interprete["statut"] == 1){ // si statut = 1
    echo "<a href=\"edit_club.php?id=$SiteID&lock=1\">Restreindre ce club</a>";
  } else if ($interprete["statut"] == 0) { // sinon si = 0
    echo "<a href=\"edit_club.php?id=$SiteID&lock=0\">Ré-ouvrir ce club</a>";
  }
?>
&nbsp;|&nbsp;
<?php
  if ($interprete["statut"] == 1 || $interprete["statut"] == 0){ // si statut = 1 ou = 0
    echo "<a href=\"edit_club.php?id=$SiteID&ban=1\" style=\"color:red;\">Bannir ce club</a>";
  } else if ($interprete["statut"] == -1) { // sinon si = -1
    echo "<a href=\"edit_club.php?id=$SiteID&ban=0\" style=\"color:green;\">Dé-bannir ce club</a>";
  }
?>
&nbsp;|&nbsp;<a href="../view_club.php?id=<?php echo $interprete["codeSite"]; ?>">Voir le club</a>
&nbsp;|&nbsp;<a href="edit_user.php?id=<?php echo $interprete["responsable_id"]; ?>">Voir le responsable</a>
</center>
</div>
<br>
<div class="row">
<table style="width: 100%;" border="1">
<tbody style="background-color:white;">
<tr>
<td class="categ">&nbsp;Référence du club</td>
<td>&nbsp;<?php echo $interprete["codeSite"]; ?></td>
</tr>
<tr>
<tr>
<td class="categ">&nbsp;Nom du club</td>
<td>&nbsp;<?php echo htmlspecialchars($interprete["nomSite"]); ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Responsable</td>
<td>&nbsp;<?php echo $interprete["nomUtilisateur"]; ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Localité</td>
<td>&nbsp;<?php echo $interprete["localite"]; ?>
  <?php
  $localite = $interprete["localite"];
  $code = $db->query("SELECT departement_code FROM departement WHERE departement_nom=\"$localite\"")->fetch(); // on séléctionne le code du département du club
   ?>
   (<?php echo $code["departement_code"];?>)
</td>
</tr>
<tr>
  <?php
    $members = $interprete["codeSite"];
    $sql = "SELECT nomUtilisateur FROM adherent WHERE codeSite=$members"; // on séléctionne tous les adhérents de ce club
    $q = $db->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $count = $q->rowCount();
    ?>
<td class="categ">&nbsp;Membres (<?php echo $count; ?>)</td>
<td>&nbsp;<?php while ($row = $q->fetch()): ?><?php echo htmlspecialchars($row['nomUtilisateur']); ?>&nbsp;/&nbsp;<?php endwhile; ?></td>
</tr>
<tr>
<td class="categ">&nbsp;Statut</td>
<td>&nbsp;
<?php if ($interprete["statut"] == 1){// si statut = 1

  echo "<FONT color=\"green\"><b>Ouvert</b></FONT>";
} else if ($interprete["statut"] == 0){ // si = 0

  echo "<FONT color=\"red\"><b>Verrouillé</b></FONT>";
} else if ($interprete["statut"] == -1){ // si = -1

  echo "<FONT color=\"grey\"><b>Banni</b></FONT> (par un administrateur)";
} else { // sinon
  echo "Inconnu.";
} ?>

</td>
</tr>

</tbody>
</table>

</div>

<?php include("includes/footer.php"); ?>
