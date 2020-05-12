<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }

?>


<?php

  $sql = "SELECT *,nomUtilisateur FROM demandes,adherent WHERE codeAdherent=utilisateur_id"; // séléctionne toutes les demandes 
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>



<div class="row">
<h1>Liste des demandes</h1>
<div class="card material-table">
  <div class="table-header">
    <span class="table-title">Gestion des demandes</span>
    <div class="actions">
      <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
    </div>
  </div>
  <table id="datatable" style="width: 100%;">
    <thead>
      <tr>
      <th>N° demande
      <th>Utilisateur
      <th>Motif
      <th>Date
      <th>Statut
      <th>Actions
  </thead>
  <tbody>
    <?php while ($row = $q->fetch()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['codeDemande']) ?></td>
      <td><?php echo htmlspecialchars($row['nomUtilisateur']); ?> (<?php echo htmlspecialchars($row['utilisateur_id']); ?>)</td>
      <td><?php echo htmlspecialchars($row['motif']); ?></td>
      <td><?php echo edit_date_format($row['date']) ?></td>
      <td><?php echo check_status_request($row['statut']); ?></td>
      <td>
        <a href="manage_request.php?id=<?php echo htmlspecialchars($row['codeDemande']) ?>" class="extrasmall_btn"><i class="fas fa-pencil-alt"></i></a>
        <a href="delete_request.php?id=<?php echo $row['codeDemande']?>" class="extrasmall_btn" style="background-color:#b80000;border:2px solid #7c0000;"><i class="fas fa-trash-alt"></i></a></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</div>
<?php include("includes/footer.php"); ?>
