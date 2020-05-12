<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }

?>


<?php

  $sql = "SELECT * FROM adherent"; // select tous les adherents
  $q = $db->query($sql);
  $q->setFetchMode(PDO::FETCH_ASSOC);
  ?>



<div class="row">
<h1>Liste des utilisateurs</h1>
<div class="card material-table">
  <div class="table-header">
    <span class="table-title">Gestion des utilisateurs</span>
    <div class="actions">
      <a href="#addClientes" class="modal-trigger waves-effect btn-flat nopadding"><i class="material-icons">person_add</i></a>
      <a href="#" class="search-toggle waves-effect btn-flat nopadding"><i class="material-icons">search</i></a>
    </div>
  </div>
  <table id="datatable" style="width: 100%;">
    <thead>
      <tr>
      <th>ID
      <th>Nom
      <th>Prénom
      <th>Pseudo
      <th>Adresse mail
      <th>Adresse IP
      <th>Rang
      <th>Actions
  </thead>
  <tbody>
    <?php while ($row = $q->fetch()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['codeAdherent']) ?></td>
      <td><?php echo htmlspecialchars($row['nomAdherent']); ?></td>
      <td><?php echo htmlspecialchars($row['prenomAdherent']); ?></td>
      <td><?php echo htmlspecialchars($row['nomUtilisateur']) ?></td>
      <td><?php echo htmlspecialchars($row['adresseMailAdherent']); ?></td>
      <td><?php echo htmlspecialchars($row['ipAdherent']); ?></td>
      <td><?php
      if ($row['is_admin'] == 1){
        echo "<FONT color=\"RED\"><b>Administrateur</b></FONT>";
      } else if ($row['is_admin'] == 2) {
        echo "<FONT color=\"GREEN\"><b>Responsable</b></FONT>";
      } else {
        echo "Utilisateur";
      }?></td>
      <td>
        <a href="edit_user.php?id=<?php echo $row['codeAdherent']?>" class="extrasmall_btn"><i class="fas fa-pencil-alt"></i></a>
        <a href="delete_user.php?id=<?php echo $row['codeAdherent']?>" class="extrasmall_btn" style="background-color:#b80000;border:2px solid #7c0000;"><i class="fas fa-trash-alt"></i></a></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</div>
<?php include("includes/footer.php"); ?>
