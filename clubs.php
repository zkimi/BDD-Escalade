<div id="container" style="height:auto;">
<?php
define("LIGNE_PAR_PAGES",5);
include("includes/header.php");
?>

<?php
if(!isset($_SESSION['user_login'])) // si la session n'est pas initialisée
  {
    header("location:index.php?logged=no"); // redirection avc msg d'erreur
  }
?>

<h1>Liste des clubs</h1>

<?php
	$mot_cle = '';
	if(!empty($_POST['recherche']['motcle'])) { // si les critères sont vides
		$mot_cle = $_POST['recherche']['motcle'];
	}
	$sql = 'SELECT *,site.codeSite,departement.departement_code FROM site,adherent,departement WHERE (nomSite LIKE :motcle OR localite LIKE :motcle OR nomAdherent LIKE :motcle OR departement_code LIKE :motcle) AND responsable_id = codeAdherent AND departement_nom=localite';

	$per_page_html = '';
	$page = 1;
	$page_debut=0;

	if(!empty($_POST["page"])) {
		$page = $_POST["page"];
		$page_debut=($page-1) * LIGNE_PAR_PAGES;
	}

	$limite=" LIMIT " . $page_debut . "," . LIGNE_PAR_PAGES;
	$pagination = $db->prepare($sql);
	$pagination->bindValue(':motcle', '%' . $mot_cle . '%', PDO::PARAM_STR);
	$pagination->execute();

	$nb_lignes = $pagination->rowCount();

	if(!empty($nb_lignes)){ // si zéro lignes
		$per_page_html .= "<div style='text-align:center;margin:20px 0px;'>";
		$page_count=ceil($nb_lignes/LIGNE_PAR_PAGES);
		if($page_count>1) {
			for($i=1;$i<=$page_count;$i++){
				if($i==$page){ // si il n'y a qu'une page
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page current" />';
				} else { // sinon
					$per_page_html .= '<input type="submit" name="page" value="' . $i . '" class="btn-page" />';
				}
			}
		}
		$per_page_html .= "</div>";
	}

	$query = $sql.$limite;
	$requete = $db->prepare($query);
	$requete->bindValue(':motcle', '%' . $mot_cle . '%', PDO::PARAM_STR);
	$requete->execute();
	$result = $requete->fetchAll();
?>
<div class="row">
<form action='' method='post'>
<div style='text-align:right;margin:20px 0px;'><input type='text' name='recherche[motcle]' value="<?php echo $mot_cle; ?>" placeholder="Rechercher un club..." id='motcle' maxlength='35'></div>
<table class='tbl-qa'>
  <thead>
	<tr>
	  <th class='table-header' width='20%'>Nom du club</th>
	  <th class='table-header' width='15%'>Localité</th>
    <th class='table-header' width='10%'>Nombre de membres</th>
    <th class='table-header' width='10%'>Nombre de parcours</th>
	  <th class='table-header' width='15%'>Responsable</th>
		<th class='table-header' width='10%'>Voir</th>
	</tr>
  </thead>
  <tbody id='table-body'>
	<?php
	if(!empty($result)) { // si on a des résultats
		foreach($result as $row) { // pour tout les résultat sous la variable $row
	?>
	  <tr class='table-row'>
		<td><?php echo htmlspecialchars($row['nomSite']); //affiche le nom du club ?>&nbsp;
      <?php if ($row["statut"] == 1){ // si le statut du club = 1
        echo "(<FONT color=\"green\"><b>Ouvert</b>)";
      } else if ($row["statut"] == 0){ // si le statut du club = 0
        echo "(<FONT color=\"red\"><b>Fermé</b></FONT>)";
      } else if ($row["statut"] == -1){ // si le statut du club = -1
        echo "(<FONT color=\"grey\"><b>Banni</b></FONT>)";
      } else {
        echo ""; // sinon on affiche rien
      } ?>
    </td>
		<td><?php echo $row['localite']; // on affiche le departement?>
      <?php
      $localite = $row["localite"];
      $code = $db->query("SELECT departement_code FROM departement WHERE departement_nom=\"$localite\"")->fetch(); // on recherche son numéro grace a son nom
       ?>
       (<?php echo $code["departement_code"]; // et on l'affiche?>)
    </td>
    <td><?php
    $idSite = $row['codeSite'];
    echo $db->query("SELECT * FROM adherent WHERE codeSite = $idSite")->rowCount(); // on affiche le nb d'adherent a ce club
    ?></td>
     <td><?php
     echo $db->query("SELECT * FROM voie WHERE codeSite = $idSite")->rowCount(); // on affiche le nb de parcours de ce club
     ?></td>
		<td style="text-transform:uppercase;">M./Mme <?php echo $row['nomAdherent']; // on affiche le responsable ?></td>
		<td><a href="view_club.php?id=<?php echo $row['codeSite']; ?>">Voir</a></td>
		</tr>
    <?php
		}
	}
	?>
  </tbody>
</table>
</div>
<?php echo $per_page_html; // on affiche les boutons pour changer de page ?>
</form>

<?php include("includes/footer.php"); ?>
