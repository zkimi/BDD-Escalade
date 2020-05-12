<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }

?>


<?php

  $sql = "SELECT *,site.codeSite FROM site,adherent WHERE site.responsable_id = adherent.codeAdherent"; // on séléctionne tout les clubs avec leur responsables
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>



<div class="row">
<h1>Liste des clubs</h1>
<div class="card material-table">
  <div class="table-header">
    <span class="table-title">Gestion des clubs</span>
    <div class="actions">
      <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
      <a href="new_club.php" class="waves-effect btn-flat nopadding"><i class="fas fa-plus"></i></a>
    </div>
  </div>
  <table id="datatable" style="width: 100%;">
    <thead>
      <tr>
      <th>ID
      <th>Nom
      <th>Localité
      <th>Nb de membres
      <th>Responsable
      <th>Actions
  </thead>
  <tbody>
    <?php while ($row = $q->fetch()): // pour chaque ligne ?>
    <tr>
      <td><?php echo htmlspecialchars($row['codeSite']) ?></td>
      <td><?php echo htmlspecialchars($row['nomSite']); ?></td>
      <td><?php echo htmlspecialchars($row['localite']); ?></td>
      <td><?php
      $idSite = $row['codeSite'];
      $sqlcount = "SELECT * FROM adherent WHERE codeSite = $idSite"; // requête pour le nb d'adh. par club
      echo $db->query($sqlcount)->rowCount(); // on affiche le nb de lignes reçues
       ?></td>
      <td><?php echo $row['nomAdherent']; ?> (<?php echo $row['codeAdherent']; ?>)</td>
      <td>
        <a href="edit_club.php?id=<?php echo $row['codeSite']?>" class="extrasmall_btn"><i class="fas fa-pencil-alt"></i></a>
        <a href="delete_club.php?id=<?php echo $row['codeSite']?>" class="extrasmall_btn" style="background-color:#b80000;border:2px solid #7c0000;"><i class="fas fa-trash-alt"></i></a></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</div>
<?php include("includes/footer.php"); ?>
