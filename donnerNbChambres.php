<?php

include("_debut.inc.php");
include("_gestionBase.inc.php"); 
include("_controlesEtGestionErreurs.inc.php");

// CONNEXION AU SERVEUR MYSQL PUIS SÉLECTION DE LA BASE DE DONNÉES festival

$connexion=connect();
if (!$connexion)
{
   ajouterErreur("Echec de la connexion au serveur MySql");
   afficherErreurs();
   exit();
}

// SÉLECTIONNER LE NOMBRE DE CHAMBRES SOUHAITÉES

$idEtab=$_REQUEST['idEtab'];
$idGroupe=$_REQUEST['idGroupe'];
$nbChambres=$_REQUEST['nbChambres'];

echo "
<form method='POST' action='modificationAttributions.php'>
	<input type='hidden' value='validerModifAttrib' name='action'>
   <input type='hidden' value='$idEtab' name='idEtab'>
   <input type='hidden' value='$idGroupe' name='idGroupe'>";
   $nomGroupe=obtenirNomGroupe($dbh, $idGroupe);
   
   echo "
   <br><center><h5>Combien de chambres souhaitez-vous pour le 
   groupe $nomGroupe dans cet établissement ?";
   
   echo "&nbsp;<select name='nbChambres'>";
   for ($i=0; $i<=$nbChambres; $i++)
   {
      echo "<option>$i</option>";
   }
   echo "
   </select></h5>
   <input type='submit' value='Valider' name='valider'>&nbsp&nbsp&nbsp&nbsp
   <input type='reset' value='Annuler' name='Annuler'><br><br>
   <a href='modificationAttributions.php?action=demanderModifAttrib'>Retour</a>
   </center>
</form>";

?>
