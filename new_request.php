<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) //check unauthorize user
  {
    header("location:index.php?logged=no");
  }
  $id = $_SESSION['user_login'];
?>

<h1>Créer un nouvelle demande</h1>

<div class="row">


  <?php
  if(isset($_REQUEST['submitRequest'])) // si le formulaire est envoyé avec le bouton submitRequest
  {
    $motif = strip_tags($_REQUEST["motif"]); // on stock les variables reçues
    $titre = strip_tags($_REQUEST["titre"]);
    $message = strip_tags($_REQUEST["msg"]);
     try
     {
      $select_requests=$db->prepare("SELECT codeDemande FROM demandes WHERE utilisateur_id=:id AND statut=1"); // on séléctionne les demandes créé par cet user et qui sont en traitement

      $select_requests->execute(array(':id'=>$id)); // on execute avec les paramètres
      $result_requests=$select_requests->fetch(PDO::FETCH_ASSOC);

      if($select_requests->rowCount() > 3){ // si y'en a plus de 3
       $errorRequestMessage[]="Vous avez déjà créé 3 demandes, afin d'éviter le spam, nous vous bloquons la création de nouvelles demandes tant que les anciennes ne sont pas traitées."; 
      }

      if (empty($titre) || strlen($titre) < 3){ // si le titre est vide ou inférieur a 3 caractères
        $errorRequestMessage[]="Merci de saisir un titre valide";
      }

      if (empty($message)){ // si le msg est vide
        $errorRequestMessage[]="Merci de saisir un message valide";
      }

      else if(!isset($errorRequestMessage)) // si aucune erreur :
      {
        if ($motif == 0){ // gérer le motif donné par l'utilisateur
          $motif = "Implémentation d'un club";
        } else if ($motif == 1){
          $motif = "Demande de permissions";
        } else if ($motif == 2){
          $motif = "Demande de parcours";
        } else if ($motif == 3) {
          $motif = "Autre";
        }

       $insert_request=$db->prepare("INSERT INTO demandes VALUES (NULL, :id, :titre, :motif, :message, NOW(), 1);");   // on créer la demande dans la BDD

       if($insert_request->execute(array(':id'=>$id, ':titre'=>$titre, ':motif'=>$motif, ':message'=>$message))){

        $goodRequestMessage="La demande a été créée avec succès. Redirection..."; // message de succès
        header("refresh:1; requests.php");
       }
      }
     }
     catch(PDOException $e)
     {
      echo $e->getMessage();
     }
    }
   ?>

   <div id="box" style="margin: 0 auto;">
     <?php
     if(isset($errorRequestMessage))
     {
      foreach($errorRequestMessage as $errorrequest)
      {
      ?>
       <div class="error"><?php echo $errorrequest; ?></div>
         <?php
      }
     }
     if(isset($goodRequestMessage))
     {
     ?>
      <div class="success">
        <?php echo $goodRequestMessage; ?>
      </div>
     <?php
     }
     ?>
    <center> <h3>Création d'une nouvelle demande</h3>
      <hr>

      <form method="POST">
        <label>Motif de votre demande :</label>
        <div class="select">
        <select id="motif" name="motif">
          <option selected="selected">Motif</option>
          <option value="0">Implémentation d'un club</option>
          <option value="1">Demande de permissions</option>
          <option value="2">Demande de parcours</option>
          <option value="3">Autre</option>
        </select>
        <div class="select_arrow">
    </div>
  </div>
        <br><label>Votre titre :</label>
        <input type="text" id="titre" name="titre" placeholder="Titre" />

        <label>Votre message :</label>
        <textarea type="textarea" id="msg" name="msg" placeholder="Message" style="text-align: left;height:140px;resize:none;" /></textarea>


      <script type="text/javascript">

          var select = document.getElementById("motif");
          var selectArray = [];
          var textArray = ["Motif : Implémentation d'un club\nNom du club: \nLocalité: \nResponsable: \nNuméro de téléphone: \nAutre: ",
          "Motif : Demande de permissions\nNom : \nPrénom: \nClub concerné: \n",
          "Motif : Demande de parcours\n\nBonjour j'aimerais savoir...",
          "Motif : ...\n\nBonjour, ..."];
          var tArea = document.getElementById("msg");

          select.onclick = function(){
             textArray[parseInt(select.options[select.selectedIndex].value)]=tArea.value;
          }
          select.onchange = function(){
             tArea.value = textArray[parseInt(select.options[select.selectedIndex].value)];
          }
      </script>

        <button type="submit" name="submitRequest" id="submitRequest">Envoyer la demande</button>
      </form>

    </center>
   </div>


</div>
<br>
<?php include("includes/footer.php"); ?>
