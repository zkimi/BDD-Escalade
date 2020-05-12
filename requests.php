<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
  }
?>

<?php
  $user_id = $_SESSION['user_login'];
  $sql = "SELECT * FROM demandes WHERE utilisateur_id=$user_id"; // Je selectionne toutes les demandes de l'utilisateur loggé.
  $result = $db->query($sql);
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>

<h1>Gérer mes demandes</h1>

<div style="width:85%;margin: 0 auto;">

  <div class="card material-table">
    <div class="table-header">
      <span class="table-title">Gestion des demandes utilisateur</span>
      <div class="actions">
        <a href="new_request.php" class="waves-effect btn-flat nopadding" title="Créer une demande"><i class="fas fa-folder-plus"></i></a>
        <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
      </div>
    </div>
    <table id="datatable" style="width: 100%;">
      <thead>
        <tr>
        <th>N° demande
        <th>N° utilisateur
        <th>Motif
        <th>Statut
        <th>Date
        <th>Actions
    </thead>
    <tbody>
      <?php while ($row = $q->fetch()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['codeDemande']) ?></td>
        <td><?php echo htmlspecialchars($row['utilisateur_id']); ?></td>
        <td><?php echo htmlspecialchars($row['motif']); ?></td>
        <td><?php check_status_request($row['statut']); ?></td>
        <td><?php echo edit_date_format($row['date']); ?></td>
        <td>  <a href="view_request.php?id=<?php echo $row['codeDemande']?>" class="extrasmall_btn"><i class="fas fa-eye"></i></a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</div>
</div>


<?php include("includes/footer.php"); ?>
