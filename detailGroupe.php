<?php

echo"
<title> Acceuil > Gestion des équipes> Détails équipes> Modifier les détails des équipes </title>";

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

$id=$_REQUEST['id'];  

// OBTENIR LE DÉTAIL DE L'ÉTABLISSEMENT SÉLECTIONNÉ

$lgGroupe=obtenirDetailGroupe($dbh, $id);

$nom=$lgGroupe['nom'];
$idres=$lgGroupe['identiteResponsable'];
$adressePostale=$lgGroupe['adressePostale'];
$nbpersonnes=$lgGroupe['nombrePersonnes'];
$nomPays=$lgGroupe['nomPays'];
$hebergement=$lgGroupe['hebergement'];

echo "
<table width='60%' cellspacing='0' cellpadding='0' align='center' 
class='tabNonQuadrille'>
   
   <tr class='enTeteTabNonQuad'>
      <td colspan='3'>$nom</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td  width='20%'> Id : </td>
      <td>$id</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td> Responsable : </td>
      <td>$idres</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td> Adresse Postale : </td>
      <td>$adressePostale</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td> Nombre de personnes : </td>
      <td>$nbpersonnes</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td> Pays : </td>
      <td>$nomPays</td>
   </tr>
   <tr class='ligneTabNonQuad'>
      <td> Hebergement : </td>
      <td>$hebergement</td>
   </tr>
</table>
<table align='center'>
   <tr>
      <td align='center'><a href='listeEquipes.php'>Retour</a>
      </td>
   </tr>
</table>";
?>
