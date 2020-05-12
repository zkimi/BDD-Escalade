<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }
?>

<style>
#row {
  width: 100%;
  text-align: center;
}

#card_recap{
  width: 100%;
  height: 75px;
  padding: 5px;
  border-radius: 5px;
  display:inline-block;
  margin:0 auto;
  color: #fff;
  background-image: linear-gradient(-20deg, #2b5876 0%, #4e4376 100%) !important;
}

.text_recap{
float:left;
font-weight: bold;
opacity: .8;
font-size: 16px;
}


.text_right {
    float: right;
    font-weight: bold;
    font-size: 2.3rem;
    line-height: 0.8;
}

table {
  margin: 0 auto;
  width: 100%;
  table-layout: fixed;
}

td {
    width: 33.3%;
    padding-right: 17px;
}

span {
  font-size: 14px;
opacity: .5;
}

.row {
  width: 80%;
  margin: 0 auto;
}

</style>

<div class="row">
<h1>Bienvenue <?php echo $row['nomUtilisateur']; ?> sur le panel administrateur!</h1>

<table>
<tbody>
<tr>
<td>

  <?php
   // requêtes pour statistiques
    $sql_users = "SELECT * FROM adherent"; // prendre tout les utilisateurs
    $result_users = $db->query($sql_users);

    $sql_requests = "SELECT * FROM demandes"; // prendre toutes les demandes
    $result_requests = $db->query($sql_requests);

    $sql_clubs = "SELECT * FROM site"; // prendre tout les clubs
    $result_clubs = $db->query($sql_clubs);


    ?>

  <div id="card_recap">
<div class="text_recap">Utilisateurs</div><br>
<span>Nombre total d'utilisateurs</span>
<div class="text_right"><?php echo $result_users->rowCount(); ?></div>
</div>

</td>
<td>  <div id="card_recap" style="background-image: radial-gradient(circle 248px at center, #16d9e3 0%, #30c7ec 47%, #46aef7 100%) !important;">
  <div class="text_recap">Demandes</div><br>
  <span>Nombre total de demandes</span>
  <div class="text_right"><?php echo $result_requests->rowCount(); ?></div>
</div></td>
<td>  <div id="card_recap" style="background-image: linear-gradient(to top, #0ba360 0%, #3cba92 100%) !important;">
  <div class="text_recap">Clubs</div><br>
  <span>Nombre de clubs</span>
  <div class="text_right"><?php echo $result_clubs->rowCount(); ?></div>
</div></td>
</tr>
</tbody>
</table><br>
<hr>
<H3>Statistiques :</H3>
<?php
/* nb moyen parcours */
$nb_parcours=$db->query("SELECT COUNT(codeVoie) as cnt FROM voie,site WHERE voie.codeSite=site.codeSite")->fetch();
$nb_clubs=$db->query("SELECT * FROM site")->rowCount();

/* nb moyen membres de club */
$nb_membres_clubs=$db->query("SELECT COUNT(codeAdherent) as cnt FROM adherent,site WHERE site.codeSite=adherent.codeSite")->fetch();
$nb_membres=$db->query("SELECT * FROM adherent")->rowCount();

/* nb demandes / membres */
$nb_membres_demandes=$db->query("SELECT COUNT(distinct demandes.utilisateur_id) as cnt FROM demandes,adherent WHERE demandes.utilisateur_id=adherent.codeAdherent")->fetch();


/* nb responsables / membres */
$nb_membres_responsables=$db->query("SELECT COUNT(distinct site.responsable_id) as cnt FROM site,adherent WHERE site.responsable_id=adherent.codeAdherent")->fetch();

/* moyenne tout avis confondus */
$avg_notes=$db->query("SELECT AVG(note) as avg FROM voie_notes")->fetch();

?>
<u>Nombre moyen de parcours par club :</u> <?php echo number_format($nb_parcours["cnt"]/$nb_clubs,1);?> parcours<br>
<u>Pourcentage de membres du site ayant rejoint un club :</u> <?php echo number_format($nb_membres_clubs["cnt"]/$nb_membres*100,2);?>%<br>
<u>Pourcentage de membres du site ayant créé une demande :</u> <?php echo number_format($nb_membres_demandes["cnt"]/$nb_membres*100,2);?>%<br>
<u>Pourcentage de membres du site étant responsables :</u> <?php echo number_format($nb_membres_responsables["cnt"]/$nb_membres*100,2);?>%<br>
<u>Moyenne des notes notés sur les parcours tout club confondus :</u> <?php echo number_format($avg_notes["avg"],1);?>★/5★<br>

<hr>

<?php
  $user_id = $_SESSION['user_login'];
  $sql = "SELECT * FROM demandes WHERE statut=1"; // on récupère les demandes ou le statut == 1 soit en cours de traitement
  $result = $db->query($sql);
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>

<div class="card material-table">
  <div class="table-header">
    <span class="table-title">Demandes non traitées</span>
    <div class="actions">
      <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
    </div>
  </div>
  <table id="datatable" style="width: 100%;">
    <thead>
      <tr>
      <th>N° demande
      <th>Motif
      <th>Statut
      <th>Date
      <th>Actions
  </thead>
  <tbody>
    <?php while ($row = $q->fetch()): // pour chaque ligne on affiche ?>
    <tr>
      <td><?php echo htmlspecialchars($row['codeDemande']) ?></td>
      <td><?php echo htmlspecialchars($row['motif']); ?></td>
      <td><?php check_status_request($row["statut"]); ?></td>
      <td><?php echo edit_date_format($row['date']); ?></td>
      <td>  <a href="manage_request.php?id=<?php echo $row['codeDemande']?>" class="extrasmall_btn"><i class="fas fa-eye"></i></a></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</div>


<?php include("includes/footer.php"); // inclusion du footer ?>
