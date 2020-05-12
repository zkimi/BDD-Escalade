<?php include("includes/header.php"); ?>

<?php
if(!isset($_SESSION['user_login'])) // si l'utilisateur n'est pas connecté
  {
    header("location:index.php?logged=no"); // redirection avec message d'erreur!
  }

  if (!isset($_GET["id"])){ // si id n'est pas initalisé
    echo "&nbsp;Vous devez sélectionner un club.";
    die;
  } else if(empty($_GET["id"])){ // si id est vide
    echo "&nbsp;Vous n'avez sélectionné aucun club.";
    die;
  }
  else if (is_numeric($_GET["id"])){
  $id = $_GET["id"]; // stockage de l'id pour plus de simplicité.
  $sql = "SELECT *,site.codeSite FROM site,adherent WHERE site.codeSite=$id AND responsable_id=adherent.codeAdherent LIMIT 1"; // selectionne les infos du club ainsi que son responsable
  $interprete = $db->query($sql)->fetch(); // stockage dans $interprete du resultat

  $count_members = $db->query("SELECT * FROM adherent WHERE codeSite = $id")->rowCount(); // stockage dans $count_members de la valeur de la fonction rowCount() sur la requête sql
  $count_parcours = $db->query("SELECT * FROM voie WHERE codeSite = $id")->rowCount(); // stockage dans $count_members de la valeur de la fonction rowCount() sur la requête sql

  if ($result->rowCount() == 0){ // si il n'y a pas d'occurence
    echo "&nbsp;Le club sélectionné n'existe pas";
    die; // on affiche rien d'autre !
  }
} else { // sinon
  echo "&nbsp;Il y a une erreur dans l'URL de la page (attention aux caractères spéciaux!)";
  die; // on affiche rien d'autre !
}

  if ($interprete["statut"] == 0){ // si le club est verrouillé
    $InfoClubMessage[] = "Désolé, ce club est actuellement verrouillé, les nouvelles admissions ne sont plus autorisées.";
  }

if (isset($_GET["join"])){ // si join est initalisé
  $id_adherent = $_SESSION['user_login']; // stockage de l'id pour plus de simplicité.
  $results_adherent = $db->query("SELECT * FROM adherent WHERE codeAdherent=$id_adherent LIMIT 1")->fetch(); // on récup l'utilisateur

  if (isset($_GET["join"]) && $_GET["join"] == 1 && is_null($results_adherent["codeSite"]) && $interprete["statut"] == 1){ // on regarde si join=1 et que l'adherent n'a pas déjà de club
    $db->query("UPDATE adherent SET codeSite=$id WHERE codeAdherent=$id_adherent"); // on lui assigne le code du club
    header("location:view_club.php?id=$id"); // redirection

  } else if (isset($_GET["join"]) && $_GET["join"] == 0 && $results_adherent["codeSite"] == $id && $row["codeAdherent"] != $interprete["responsable_id"]){ // si join=0, qu'il s'agit birn du club en question et qu'il n'est pas responsable
    $db->query("UPDATE adherent SET codeSite=NULL WHERE codeAdherent=$id_adherent");  // on lui enleve le code du club = NULL
    $id_adherent = $row["codeAdherent"];

    $db->query("DELETE FROM guide WHERE codeAdherent=$id_adherent"); // si il est guide, on le supprime

    $count_notes = $db->query("SELECT voie.codeVoie FROM voie_notes,voie WHERE voie_notes.codeSite=$id AND voie_notes.codeAdherent=$id_adherent")->rowCount();
    for ($i=0; $i < $count_notes ; $i++) { // pour tout les votes de la personnes on détruit tout.
      $db->query("DELETE FROM voie_notes WHERE voie_notes.codeSite=$id AND voie_notes.codeAdherent=$id_adherent"); // supprime chaque vote 1 a 1
    }


    header("location:view_club.php?id=$id"); // redirection

  } else { // sinon
    echo "&nbsp;<u>Vous ne pouvez pas effectuer cette action pour une des raisons suivantes :</u>
    <ul>
      <li>Vous faites déjà parti d'un club ou de ce club</li>
      <li>Le club que vous tentez de rejoindre est verrouillé ou banni</li>
      <li>Vous essayez de quitter ce club alors que vous n'en êtes pas membre</li>
      <li>Vous êtes responsable de ce club et par conséquent, vous ne pouvez pas le quitter à moins de le supprimer</li>
    </ul>";
    die; // on affiche rien d'autre !
  }
}

if (isset($_GET["lock"]) && $interprete["statut"] != -1){ // si la valeur lock est initialisée
  if ($_GET["lock"] == 1 && $interprete["responsable_id"] == $row["codeAdherent"]){ // on regarde si lock=1 et que seulement le responsable puisse
    $db->query("UPDATE site SET statut=0 WHERE codeSite=$id"); // on passe le statut du club à 0
    header("location:view_club.php?id=$id"); // redirection

  } else if (isset($_GET["lock"]) && $_GET["lock"] == 0 && $interprete["responsable_id"] == $row["codeAdherent"]){ // sinon si le join=0 et que c'est le responsable
    $db->query("UPDATE site SET statut=1 WHERE codeSite=$id"); // on passe le statut du club à 1
    header("location:view_club.php?id=$id"); // redirection

  } else { // sinon
    echo "&nbsp;<u>Vous ne pouvez pas effectuer cette action pour une des raisons suivantes :</u>
    <ul>
      <li>Vous n'êtes pas responsable de ce club</li>
      <li>Vous avez tenté d'entrer une valeur incorrecte à l'intérieur de la base de donnée par l'intermédiaire de l'URL</li>
    </ul>";
    die; // on affiche rien d'autre !
  }
}

if (isset($_GET["delete"]) && $interprete["statut"] != -1){ // si la valeur delete est initialisée
  if ($_GET["delete"] == 1 && $interprete["responsable_id"] == $row["codeAdherent"]){ // on regarde si delete=1 et que seulement le responsable puisse
    $nb_notes = $db->query("SELECT * FROM voie_notes WHERE codeSite = $id;")->rowCount();
    for ($i=0; $i < $nb_notes; $i++) {
      $db->query("DELETE FROM voie_notes WHERE codeSite=$id"); // on supprime tout les avis sur les parcours de ce club
    }
    $nb_cordees = $db->query("SELECT * FROM cordee WHERE cordee.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_cordees; $i++) {
      $db->query("DELETE FROM cordee WHERE cordee.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $nb_sorties = $db->query("SELECT * FROM sortie WHERE sortie.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_cordees; $i++) {
      $db->query("DELETE FROM sortie WHERE sortie.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $db->query("ALTER TABLE voie DISABLE KEYS;");
    $nb_parcours = $db->query("SELECT * FROM voie WHERE voie.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_parcours; $i++) {
      $db->query("DELETE FROM voie WHERE voie.codeSite=$id;"); // on supprime tout les parcours de ce club
    }
    $db->query("ALTER TABLE voie ENABLE KEYS;");
    $nb_guides = $db->query("SELECT * FROM guide WHERE guide.codeSite=$id;")->rowCount();
    for ($i=0; $i < $nb_guides; $i++) {
      $db->query("DELETE FROM guide WHERE guide.codeSite=$id;"); // on supprime tout les guides de ce club
    }
    $db->query("UPDATE adherent SET codeSite=NULL WHERE codeSite=$id"); // on vire tout les membres du club
    $db->query("DELETE FROM site WHERE codeSite=$id"); // et enfin, on supprime le club de la BDD.
    header("location:clubs.php"); // redirection
  }else { // sinon
    echo "&nbsp;<u>Vous ne pouvez pas effectuer cette action pour une des raisons suivantes :</u>
    <ul>
      <li>Vous n'êtes pas responsable de ce club</li>
      <li>Vous avez tenté d'entrer une valeur incorrecte à l'intérieur de la base de donnée par l'intermédiaire de l'URL</li>
    </ul>";
    die; // on affiche rien d'autre !
  }
}

if ($interprete["statut"] == -1){ // si le club a été banni par un administrateur
  echo "&nbsp;<u>Désolé le club que vous tentez de consulter a été <b><i>banni</i></b> pour une des raisons suivantes :</u>
  <ul>
    <li>Non respect des CGU du site</li>
  </ul><br>&nbsp;Si vous êtes responsable et ne comprenez toujours pas la cause de votre sanction, nous vous invitons à contacter <a href=\"requests.php\">le support</a>.";
  die;
}

// espace voie_notes

if (isset($_GET["rate"])){
  if (isset($_GET["mark"])){

    $id_voie_note = $_GET["rate"];
    $note_attribuee = $_GET["mark"];
    $id_adherent = $row["codeAdherent"];

    if ($row["codeSite"] != $interprete["codeSite"]){ // si le club de l'utilisateur n'est pas ce club
      echo"&nbsp;Vous devez être membre de ce club pour pouvoir donner votre avis sur ce parcours !";
      die;
    }

    if (!is_numeric($note_attribuee)){ // si la note nest pas numérique
      echo"&nbsp;Merci de saisir une note correcte !";
      die;
    }

    if ($note_attribuee <= 0 || $note_attribuee > 5){ // si la note n'est pas 1<=note<=5
      echo"&nbsp;Petit malin, la note doit être comprise entre <b>1 et 5</b> !";
      die;
    }

    if ($interprete["statut"] != 1){ // si le statut n'est pas OUVERT.
      echo"&nbsp;Le statut de ce club ne permet pas d'accepter de nouveaux avis sur ses parcours";
      die;
    }

    $sql_find_rate = $db->query("SELECT * FROM voie_notes,adherent WHERE voie_notes.codeAdherent=$id_adherent AND voie_notes.codeVoie=$id_voie_note AND voie_notes.codeAdherent=adherent.codeAdherent");

    if ($sql_find_rate->rowCount() == 0){
      $db->query("INSERT INTO voie_notes (codeNote, codeAdherent, codeVoie, codeSite, note) VALUES (NULL, $id_adherent, $id_voie_note, $id, $note_attribuee)");
      $successMsg = "Vous venez d'attribuer une note à ce parcours.";
    } else {
      echo"&nbsp;Vous avez déjà voté pour ce parcours !";
      die;
    }

  }
}


if (isset($_GET["participate"])){
  $id_cordee = $_GET["participate"];

  if (empty($id_cordee)){
    echo "&nbsp;Merci de choisir une sortie.";
    die;
  }

  $test_code = $db->query("SELECT codeSite FROM sortie WHERE codeSortie=$id_cordee")->fetch();
  if ($test_code["codeSite"] != $id){
    echo "&nbsp;Vous essayez de rejoindre le parcours d'un autre club.";
      die;
  }


  if (isset($_GET["action"])){


    $action = $_GET["action"];


    if (!is_numeric($action)){
      echo "&nbsp;Merci de saisir une action valide.";
      die;
    }

    $codeAdherent = $_SESSION['user_login'];
    $get_sorties_member_state = $db->query("SELECT * FROM cordee WHERE codeCordee = $id_cordee AND codeAdherent = $codeAdherent;");

    if ($action == 1){
      if ($get_sorties_member_state->rowCount() > 0){
        echo "&nbsp;Vous êtes déjà inscrit à cette sortie.";
      die;
      }
      $db->query("INSERT INTO cordee VALUES ($id_cordee, $codeAdherent, $id);");
      $successMsg = "Vous venez de vous inscrire à cette sortie";
    }

    if ($action == 0){
      $db->query("DELETE FROM cordee WHERE codeCordee = $id_cordee AND codeAdherent = $codeAdherent;");
      $successMsg = "Vous venez de vous désinscrire de cette sortie";
    }
  }
}
    ?>

<h1>Consultation du club : <?php echo $interprete["nomSite"]; ?></h1>

<div class="row">
  <div id="box">
    <?php
    if(isset($InfoClubMessage)) // si le tableau $InfoClubMessage est initialisé
    {
     foreach($InfoClubMessage as $yellowLabel) // pour chaque ligne du tableau on initalise une variable
     {
     ?>
      <div class="info"><?php echo $yellowLabel; // on affiche cette variable ?></div>
        <?php
     }
    }
    if(isset($successMsg)) // si un message de succès est initalisé
    {
    ?>
     <div class="success">
       <?php echo $successMsg; // on l'affiche ?>
     </div>
    <?php
    }
    ?>

    <div style="font-size:32px;margin:5px">
      <i class="fas fa-hiking"></i>
      <?php echo htmlspecialchars($interprete["nomSite"]); ?>
      <?php
      $codeSite = $interprete["codeSite"];
      if (isset($codeClub)){
        if ($codeClub == $interprete["codeSite"] && $row["codeAdherent"] != $interprete["responsable_id"]){
          echo "<i style=\"font-size:13px;\">(Vous êtes membre de ce club!)</i>";
          echo "&nbsp;<div class=\"actionbtn red\"><a href=\"view_club.php?id=$codeSite&join=0\">Quitter ce club</a></div>";
        }
      } else if ($interprete["statut"] != 0) {
        echo "<div class=\"actionbtn green\"><a href=\"view_club.php?id=$codeSite&join=1\">Rejoindre ce club</a></div>";
      } else if ($interprete["statut"] == 0){
        echo "<div class=\"actionbtn grey\"><a href=\"#\">Club verrouillé</a></div>";
      }
       ?>

       <?php
       $guides_sql = $db->query("SELECT *,nomAdherent,prenomAdherent FROM guide,adherent WHERE guide.codeSite=$id AND guide.codeAdherent = adherent.codeAdherent;");
       $count_guides = $guides_sql->rowCount();
       ?>

      <i style="float:right; font-size:18px;opacity:.8;"><a href="view_club_guides.php?id=<?php echo $id;?>">Voir les guides (<?php echo $count_guides; ?>)</a></i>
      <i style="float:right; font-size:18px;opacity:.8;">Responsable : M./MME <?php echo strtoupper($interprete["nomAdherent"]); ?>&nbsp;|&nbsp;</i><br>
      </div>
      <?php if($interprete["nomUtilisateur"] == $row["nomUtilisateur"] && $row["codeSite"] == $interprete["codeSite"]){ // si l'utilisateur est le responsable et qu'il est membre du club
        echo "&nbsp;<b><i>Vous êtes responsable de ce club.</i></b>";
        if ($interprete["statut"] == 1){
        echo "&nbsp;<p><div class=\"actionbtn grey\"><a href=\"view_club.php?id=$codeSite&lock=1\">Verrouiller le club</a></div>";
      } else {
        echo "&nbsp;<p><div class=\"actionbtn green\"><a href=\"view_club.php?id=$codeSite&lock=0\">Déverrouiller le club</a></div>";
      }
        echo "&nbsp;<div class=\"actionbtn yellow\"><a href=\"edit_club.php?id=$codeSite\">Éditer les informations</a></div>";
        echo "&nbsp;<div class=\"actionbtn red\"><a href=\"#\" class=\"modal-trigger\" data-modal=\"modal-delete\">Supprimer le club</a></div>

        <div class=\"modal\" id=\"modal-delete\">
       <div class=\"modal-sandbox\"></div>
       <div class=\"modal-box\">
         <div class=\"modal-header\">
           <div class=\"close-modal\">&#10006;</div>
           <h1>Êtes-vous sûr de vouloir supprimer votre club ?</h1>
         </div>
         <div class=\"modal-body\">
           <p>Si vous supprimez votre club, il ne sera <b>plus possible</b> de faire marche arrière !</p>
           <p>Vous perdrez tout vos membres, parcours et avis.</p>
           <p>Si vous en êtes certains alors, cliquez sur le bouton ci-dessous.</p>
             <br />
           <button onclick=\"location.href='view_club.php?id=$codeSite&delete=1';\" class=\"close-modal\"  style=\"text-align:center;background-color: #256363;border: 2px solid #184242;\">Supprimer le club</button>
         </div>
       </div>
     </div>";
        echo "<div class=\"actionbtn blue\"><a href=\"view_club_members.php?id=$codeSite\">Voir les membres</a></div>";
        echo "&nbsp;<div class=\"actionbtn purple\"><a href=\"view_club_courses.php?id=$codeSite\">Voir les parcours</a></div>";
        echo "&nbsp;<div class=\"actionbtn darkgreen\"><a href=\"new_cordee.php?id=$codeSite\">Créer une cordée</a></div></p>";

      }?>

    <hr>
    <div class="label">Département : <?php echo $interprete["localite"]; ?>
      <?php
      $localite = $interprete["localite"];
      $code = $db->query("SELECT departement_code FROM departement WHERE departement_nom=\"$localite\"")->fetch();
       ?>
       (<?php echo $code["departement_code"];?>)
    </div>
    <div class="label">Nombre de membres : <?php echo $count_members; ?></div>
    <div class="label">Nombre de parcours proposés : <?php echo $count_parcours; ?></div>

  

    <?php 
    $get_sorties = $db->query("SELECT *,voie.nomVoie,sortie.niveau FROM sortie,voie,guide,adherent WHERE sortie.codeSite=$id AND sortie.codeVoie=voie.codeVoie GROUP BY sortie.codeSortie;");
    if ($get_sorties->rowCount() > 0){ // si le club possède des sorties
      echo"<hr>";
    ?>

    <?php while ($sortie = $get_sorties->fetch()): // pour chaque ligne de la DB on crée une case ?>
      <div class="sortie">
        <div class="titre">

          <?php echo $sortie["descriptionSortie"]; ?>
          <br><u><b>Parcours : <?php echo $sortie["nomVoie"]; ?></u></b>
          <br><u><b>Type : <?php echo $sortie["styleAscension"]; ?></u></b>
          <br><u><b>Niveau : <?php echo $sortie["niveau"]; ?></u></b>
          <br><u><b>Date : <?php echo date('d/m/Y', strtotime($sortie["dateSortie"])); ?></u></b>
        </div>

        <?php 
        $codeSortie = $sortie["codeSortie"]; 
        $codeAdherent = $_SESSION['user_login'];
        $get_participants_sortie = $db->query("SELECT * FROM cordee WHERE codeCordee = $codeSortie;");
        $get_membre_etat_sortie =  $db->query("SELECT * FROM cordee WHERE codeCordee = $codeSortie AND codeAdherent=$codeAdherent;");
        $get_guides = $db->query("SELECT *,nomAdherent FROM sortie,adherent,guide WHERE codeSortie = $codeSortie AND sortie.codeGuide=guide.codeGuide AND guide.codeAdherent=adherent.codeAdherent;")->fetch();
        ?>

        <div class="join">
        <b><?php echo $get_participants_sortie->rowCount(); ?>/<?php echo $sortie["nbMaxSortie"]; ?></b>
        <br><small style="font-size: 9px;">participants</small>
        <br>

        <?php if ($get_membre_etat_sortie->rowCount() > 0){
         echo "<div class=\"extrasmall_btn\" style=\"margin:10px auto;width:75%;\"><a href=\"view_club.php?id=$id&participate=$codeSortie&action=0\" style=\"color:#fff;font-weight: normal;\">Se désinscrire</a></div>";
        } else {
          echo "<div class=\"extrasmall_btn\" style=\"margin:10px auto;width:75%;\"><a href=\"view_club.php?id=$id&participate=$codeSortie&action=1\" style=\"color:#fff;font-weight: normal;\">Participer</a></div>";
        } ?>
        <br>
        
        <span style="font-size: 13px;font-weight: bold;text-transform: uppercase;"><?php echo $get_guides["nomAdherent"]; ?></span>
      </div>
      </div>
    <?php endwhile; ?>


    <?php
    }
    ?>
    


    <hr>
    <?php
      $id_adherent = $row["codeAdherent"];
      $role_guide = $db->query("SELECT * FROM guide WHERE codeAdherent = $id_adherent AND codeSite=$id;");
      $interprete_guide = $role_guide->fetch();
      if ($role_guide->rowCount() == 1){
        if (!is_null($interprete_guide["niveau"])){
          $lvl_guide = strtoupper($interprete_guide["niveau"]);
          echo "<p>Vous êtes guide dans ce club ! Votre niveau d'encadrement maximal : <b>$lvl_guide</b></p><hr>";
        } else {
        echo "<p><b><FONT color=\"red\">Vous êtes guide dans ce club, mais vous n'avez pas encore mis le niveau maximal que vous pouvez encadrer !</b></FONT> Faites-le <a href=\"guide_setlevel.php?id=$id_adherent&club=$id\">ici</a> !</p><hr>";
      }
    }
    ?>
    <?php
      $result=$db->query("SELECT * FROM voie WHERE codeSite=$id;");
      $result->setFetchMode(PDO::FETCH_ASSOC);
    ?>

    <?php while ($select = $result->fetch()): ?>
      <div class="parcours">
        <h1><?php echo htmlspecialchars($select['nomVoie']) ?>


    <?php
    $id_voie = $select['codeVoie'];
    $id_club = $interprete["codeSite"];
    $sql_rate = $db->query("SELECT * FROM voie_notes WHERE codeAdherent = $id_adherent AND codeVoie = $id_voie;");
    $sql_rate->setFetchMode(PDO::FETCH_ASSOC);
    $find_rate = $sql_rate->fetch();

    /* Partie Statistiques affiché nb de votre et note moyenne */
    $sql_stats_avg = $db->query("SELECT avg(note) AS avg FROM voie_notes WHERE codeVoie = $id_voie;")->fetch();
    $sql_stats_count = $db->query("SELECT COUNT(*) AS count FROM voie_notes WHERE codeVoie = $id_voie;")->fetch();

    if ($sql_rate->rowCount() > 0){ // si on trouve un vote
      echo"<div class=\"rating rated\">";

      for ($i=1; $i < 6-$find_rate["note"] ; $i++) { // afficher les etoiles restantes
        echo "<a style=\"color:#aaa;\" href=\"#\">★</a>";
      }

      for ($i=1; $i < $find_rate["note"]+1 ; $i++) { // afficher le vote
        echo "<a href=\"#\">★</a>";
      }

      echo"</div>";
    } else { // sinon on affiche les 5 etoiles pour que l'user vote
      echo"<div class=\"rating rating2\">";
      for ($i=5; $i > 0 ; $i--) {
        echo "<a href=\"view_club.php?id=$id_club&rate=$id_voie&mark=$i\" title=\"Donner $i étoiles\">★</a>";
      }
      echo"</div>";
    }

     ?>
&nbsp;
<?php if ($sql_stats_count["count"] == 0){
  echo"<span style=\"font-size:11px;\">(aucun vote)</span>";
} else {
  echo"<span style=\"font-size:11px;\">(noté ~"; echo number_format($sql_stats_avg["avg"],1); echo" pour "; echo $sql_stats_count["count"]; echo" votes)</span>";
} ?>

        </h1>

        <div class="level"><?php echo htmlspecialchars($select['difficulteVoie']) ?></div>
      <u><b>Description :</b></u> <?php echo htmlspecialchars($select['description']) ?>
      <br><u><b>Dénivelé :</b></u> <?php echo htmlspecialchars($select['longueurVoie']) ?> cm
      <br><u><b>Type :</b></u> <?php echo htmlspecialchars($select['typeVoie']) ?>
    </div>
    <?php endwhile; ?>


  </div>

</div>
<br>
<?php include("includes/footer.php"); ?>
