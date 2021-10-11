<?php

echo"<title> Acceuil > Gestion des équipes> Supprimer des équipes </title>";

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

// SUPPRIMER UN ÉTABLISSEMENT 

$id=$_REQUEST['id'];  

$lgGroupe=obtenirDetailGroupe($dbh, $id);
$nom=$lgGroupe['nom'];

// Cas 1ère étape (on vient de listeEtablissements.php)

if ($_REQUEST['action']=='demanderSupprGroupe')    
{
   echo "
   <br><center><h5>Souhaitez-vous vraiment supprimer le groupe $nom ? 
   <br><br>
   <a href='suppressionGroupe.php?action=validerSupprGroupe&amp;id=$id'>
   Oui</a>&nbsp; &nbsp; &nbsp; &nbsp;
   <a href='listeEquipes.php?'>Non</a></h5></center>";
}

// Cas 2ème étape (on vient de suppressionEtablissement.php)

else
{
   supprimerGroupe($dbh, $id);
   echo "
   <br><br><center><h5>Le groupe $nom a été supprimé</h5>
   <a href='listeEquipes.php?'>Retour</a></center>";
}

?>
