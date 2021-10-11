<?php

echo"<title> Acceuil > Liste des équipes </title>";

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

// AFFICHER L'ENSEMBLE DES GROUPES
// CETTE PAGE CONTIENT UN TABLEAU CONSTITUÉ D'1 LIGNE D'EN-TÊTE ET D'1 LIGNE PAR
// ÉTABLISSEMENT

echo "
<table width='70%' cellspacing='0' cellpadding='0' align='center' 
class='tabNonQuadrille'>
   <tr class='enTeteTabNonQuad'>
      <td colspan='4'>Groupes</td>
   </tr>";

   $req=obtenirReqGroupes();
   $rsGroupe=$dbh->query($req);
   $lgGroupe=$rsGroupe->fetchAll(PDO::FETCH_ASSOC);
   // BOUCLE SUR LES EQUIPES
   foreach ($lgGroupe as $row)
   {
      $id=$row['id'];
      $nom=$row['nom'];
      echo "
		<tr class='ligneTabNonQuad'>
         <td width='52%'>$nom</td>
         
         <td width='16%' align='center'> 
         <a href='detailGroupe.php?id=$id'>
         Voir détail</a></td>
         
         <td width='16%' align='center'> 
         <a href='modificationGroupe.php?action=demanderModifGroupe&amp;id=$id'>
         Modifier</a></td>";
      	
         // S'il existe déjà des attributions pour l'établissement, il faudra
         // d'abord les supprimer avant de pouvoir supprimer l'établissement
			if (!existeAttributionsGroupes($dbh, $id))
			{
            echo "
            <td width='16%' align='center'> 
            <a href='suppressionGroupe.php?action=demanderSupprGroupe&amp;id=$id'>
            Supprimer</a></td>";
         }
         else
         {
            echo "
            <td width='16%'>&nbsp; </td>";          
			}
			echo "
      </tr>";
      $lgGroupe=$rsGroupe->fetchAll(PDO::FETCH_ASSOC);
   }  
   echo "
   <tr class='ligneTabNonQuad'>
      <td colspan='4'><a href='creationGroupe.php?action=demanderCreGroupe'>
      Création d'un groupe</a ></td>
  </tr>
</table>";

?>
