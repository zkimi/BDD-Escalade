<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login']) || $row['is_admin']==0) //check unauthorize user
  {
    header("location:../index.php?logged=no_access");
  }
?>
<div class="row">
<h1>Gestion du site internet</h1>
<div id="box" style="margin:0 auto;">
<center>
<h4 style="margin:2px;">Gestion de la maintenance du site</h4>
<hr>

<?php
if(isset($_REQUEST['submitMaintenanceMode'])) // lors du click sur "submitMaintenanceMode"
{
  $value_maintenance = strip_tags($_REQUEST['maintenance_mode']); // la valeur (0 ou 1)
  $reason_maintenance = strip_tags($_REQUEST['maintenance_raison']); // l'eventuelle raison

   try
   {
     $db->query("UPDATE website SET maintenance=$value_maintenance,raison_maintenance=\"$reason_maintenance\"");   // on modifie la BDD

    $maintenanceMessage="Les paramètres maintenance ont étés modifiés."; // et on met un message de succès
   }
   catch(PDOException $e)
   {
    echo $e->getMessage();
   }
  }
 ?>
<?php
 if(isset($maintenanceMessage))
 {
 ?>
  <div class="success">
    <?php echo $maintenanceMessage; ?>
  </div>
 <?php
 }
 ?>

<form method="POST">
  <input type="radio" name="maintenance_mode" value="1"/><label>Activée</label>
  <input type="radio" name="maintenance_mode" value="0" checked="checked"/><label>Désactivée</label>
<br>
  <input type="text" name="maintenance_raison" value="" placeholder="Raison de la maintenance...">
  <button type="submit" name="submitMaintenanceMode">Modifier le statut maintenance</button>
</form>

</center>
</div>

<br>

<div id="box" style="margin:0 auto;">
<center>
<h4 style="margin:2px;">Changement de nom du site</h4>
<hr>

<?php
if(isset($_REQUEST['submitNameEditing'])) // lors du click sur "submitNameEditing"
{
  $name = strip_tags($_REQUEST['websiteName']); // on prend le nom rentré

   try
   {
     $db->query("UPDATE website SET titre=\"$name\"");   // on modifie le champ titre en BDD

    $nameMessage="Les paramètres de nom ont étés modifiés."; // message de succès
   }
   catch(PDOException $e)
   {
    echo $e->getMessage();
   }
  }
 ?>
<?php
 if(isset($nameMessage))
 {
 ?>
  <div class="success">
    <?php echo $nameMessage; ?>
  </div>
 <?php
 }
 ?>

<form method="POST">
  <input type="text" name="websiteName" value="" placeholder="Titre du site...">
  <button type="submit" name="submitNameEditing">Modifier le nom du site</button>
</form>

</center>
</div>

</div>
