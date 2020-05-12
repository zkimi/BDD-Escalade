<?php
function edit_date_format($datetime){ // fonction pour eviter répétition du code pour styliser un champ de type DATETIME
  list($date, $time) = explode(" ", $datetime);
  list($hour, $min, $sec) = explode(":", $time);
  list($year, $month, $day) = explode("-", $date);
  $lastmodified = "$day/$month/$year $time";
  $months = array("janvier", "février", "mars", "avril", "mai", "juin",
      "juillet", "août", "septembre", "octobre", "novembre", "décembre");
  $lastmodified = "le $day ".$months[$month-1]." $year à ${hour}h${min}m${sec}s";
  echo $lastmodified;
}

function check_status_request($statut){ // fonction pour eviter répétition du code pour savoir le statut d'une demande
  if ($statut == 0){
    echo "<b><FONT color=\"red\">Demande annulée</FONT></b>";
  } else if ($statut == 1){
    echo "<b><FONT color=\"gold\">En cours de traitement</FONT></b>";
  } else if ($statut == 2){
    echo "<b><FONT color=\"#00cce2\">Mis à jour</FONT></b>";
  } else if ($statut == 3){
    echo "<b><FONT color=\"red\">Demande cloturée</FONT></b>";
  } else if ($statut == 4)
    echo "<b><FONT color=\"green\">Demande validée</FONT></b>";
    else {
    echo "<b>Inconnu</b>";
  }
}
 ?>
