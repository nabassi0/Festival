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

// EFFECTUER OU MODIFIER LES ATTRIBUTIONS POUR L'ENSEMBLE DES ÉTABLISSEMENTS

// CETTE PAGE CONTIENT UN TABLEAU CONSTITUÉ DE 2 LIGNES D'EN-TÊTE (LIGNE TITRE ET 
// LIGNE ÉTABLISSEMENTS) ET DU DÉTAIL DES ATTRIBUTIONS 
// UNE LÉGENDE FIGURE SOUS LE TABLEAU

// Recherche du nombre d'établissements offrant des chambres pour le 
// dimensionnement des colonnes
$nbEtabOffrantChambres=obtenirNbEtabOffrantChambres($dbh);
$nb=$nbEtabOffrantChambres+3; // on rajoute plus 3 car on rajoute 2 autres colonnes pour créer une ligne uniforme pour l'attribution.
// Détermination du pourcentage de largeur des colonnes "établissements"
$pourcCol=50/$nbEtabOffrantChambres;

$action=$_REQUEST['action'];

// Si l'action est validerModifAttrib (cas où l'on vient de la page 
// donnerNbChambres.php) alors on effectue la mise à jour des attributions dans 
// la base 
if ($action=='validerModifAttrib')
{
   $idEtab=$_REQUEST['idEtab'];
   $idGroupe=$_REQUEST['idGroupe'];
   $nbChambres=$_REQUEST['Yann'];
   modifierAttribChamb($dbh, $idEtab, $idGroupe, $nbChambres);
}

echo "
<table width='80%' cellspacing='0' cellpadding='0' align='center' 
class='tabQuadrille'>";

   // AFFICHAGE DE LA 1ÈRE LIGNE D'EN-TÊTE
   echo "
   <tr class='enTeteTabQuad'>
      <td colspan=$nb><strong>Attributions</strong></td>
   </tr>";
      
   // AFFICHAGE DE LA 2ÈME LIGNE D'EN-TÊTE (ÉTABLISSEMENTS)
   echo "
   <tr class='ligneTabQuad'>
      <td>Nom des equipes</td>
      <td>Nombre de chambres demandées</td>"; // on rajoute les td
      
   $req=obtenirReqEtablissementsOffrantChambres();
   $rsEtab=$dbh->query($req);
   $lgEtab=$rsEtab->fetchAll(PDO::FETCH_ASSOC);

   // Boucle sur les établissements (pour afficher le nom de l'établissement et 
   // le nombre de chambres encore disponibles)
   foreach ($lgEtab as $row)
   {
      $idEtab=$row["id"];
      $nom=$row["nom"];
      $nbOffre=$row["nombreChambresOffertes"];
      $nbOccup=obtenirNbOccup($dbh, $idEtab);
                    
      // Calcul du nombre de chambres libres
      $nbChLib = $nbOffre - $nbOccup;
      echo "
      <td valign='top' width='$pourcCol%'><i>Disponibilités : $nbChLib </i> <br>
      $nom </td>";
      $lgEtab=$rsEtab->fetchAll(PDO::FETCH_ASSOC);
   }
   echo "
   <td>Nombre de chambres louées</td></tr>"; 

   // CORPS DU TABLEAU : CONSTITUTION D'UNE LIGNE PAR GROUPE À HÉBERGER AVEC LES 
   // CHAMBRES ATTRIBUÉES ET LES LIENS POUR EFFECTUER OU MODIFIER LES ATTRIBUTIONS
         
   $req=obtenirReqIdNomGroupesAHeberger();
   $rsGroupe=$dbh->query($req);
   $lgGroupe=$rsGroupe->fetchAll(PDO::FETCH_ASSOC);
   
         
   // BOUCLE SUR LES GROUPES À HÉBERGER 
   foreach ($lgGroupe as $grow)
   {
      $idGroupe=$grow['id'];
      $nom=$grow['nom'];
      $nomdespays=$grow['nomPays'];
      $nbChambres =  intdiv($grow['nombrePersonnes'],3); // fonction pour l'attribution des chambres, si reste on rajoute une autre chambre.
      if($grow['nombrePersonnes']%3>0)
      {
         $nbChambres = $nbChambres + 1;
      }
      echo "
      <tr class='ligneTabQuad'>
         <td width='25%'>$nom ($nomdespays)</td>
         <td> $nbChambres</td>"; // ajout nompays et le nombre de chambres
         $nbTotaldeschambres = 0; // on initialise pour faire les sommes et c'est pour ça que nb de chambre ( collone) est a la fin
      $req=obtenirReqEtablissementsOffrantChambres();
      $rsEtab=$dbh->query($req);
      $lgEtab=$rsEtab->fetchAll(PDO::FETCH_ASSOC);
           
      // BOUCLE SUR LES ÉTABLISSEMENTS
      foreach ($lgEtab as $row)
      {
         $idEtab=$row["id"];
         $nbOffre=$row["nombreChambresOffertes"];
         $nbOccup=obtenirNbOccup($dbh, $idEtab);

         echo"<form method='POST' action='modificationAttributions.php'> 
         <input type='hidden' value='validerModifAttrib' name='action'>
         <input type='hidden' value='$idEtab' name='idEtab'>
         <input type='hidden' value='$idGroupe' name='idGroupe'>"; // création du post qui est un formulaire utiliser dans les boutons valider.

                   
         // Calcul du nombre de chambres libres
         $nbChLib = $nbOffre - $nbOccup;
                  
         // On recherche si des chambres ont déjà été attribuées à ce groupe
         // dans cet établissement
         $nbOccupGroupe=obtenirNbOccupGroupe($dbh, $idEtab, $idGroupe);
         $nbTotaldeschambres += $nbOccupGroupe;
         // Cas où des chambres ont déjà été attribuées à ce groupe dans cet
         // établissement
         if ($nbOccupGroupe!=0)
         {
            if($nbChLib + $nbOccupGroupe < $nbOccupGroupe+$nbChambres-$nbTotaldeschambres)  // pour créer une limite.
            {
               $nbMax = $nbChLib + $nbOccupGroupe;
            }
            else
            {
               $nbMax = $nbOccupGroupe+$nbChambres-$nbTotaldeschambres;
            }
            // Le nombre de chambres maximum pouvant être demandées est la somme 
            // du nombre de chambres libres et du nombre de chambres actuellement 
            // attribuées au groupe (ce nombre $nbmax sera transmis si on 
            // choisit de modifier le nombre de chambres) ET ON A EFFACER NBMAX
            echo "
            <td class='reserve'>
              <select name='Yann'>";
              for ($i=0;$i<=$nbMax; $i++)
             {

                if ($nbOccupGroupe == $i)
                echo "<option selected> $i </option>";
                else
                echo "<option>$i</option>";
             } 
             echo "</select>&nbsp<input type='submit' value='valider'></form></td>"; // Menu déroulant, condition pour selectionner 
              
         }
         else
         {
            // Cas où il n'y a pas de chambres attribuées à ce groupe dans cet 
            // établissement : on affiche un lien vers donnerNbChambres s'il y a 
            // des chambres libres sinon rien n'est affiché     
            if ($nbChLib != 0)
            {
               if($nbChLib + $nbOccupGroupe < $nbOccupGroupe+$nbChambres-$nbTotaldeschambres)  // pour créer une limite.
               {
                  $nbMax = $nbChLib + $nbOccupGroupe;
               }
               else
               {
                  $nbMax = $nbOccupGroupe+$nbChambres-$nbTotaldeschambres;
               }
               echo "<td class='reserveSiLien'> <select name='Yann'><option selected> 0 </option>";
               for($i=1; $i<=$nbMax;$i++)
               echo "<option> $i</option>"; // balise option va donner la valuer de quelque chose dans une liste
               echo " </select> 
               <input type = 'submit' value = 'valider'> 
               </select> </form> </td>"; // on supprime $nbsp qui est l'espace.
               
            }
            else
            {
               echo "<td class='reserveSiLien'>&nbsp;</td>";
            }
         }

         $lgEtab=$rsEtab->fetchAll(PDO::FETCH_ASSOC);
      } // Fin de la boucle sur les établissements 
      echo "<td> $nbTotaldeschambres</td> </tr>";  
      $lgGroupe=$rsGroupe->fetchAll(PDO::FETCH_ASSOC);
   } // Fin de la boucle sur les groupes à héberger
echo "
</table>"; // Fin du tableau principal

// AFFICHAGE DE LA LÉGENDE
echo "
<table align='center' width='80%'>
   <tr>
      <td width='34%' align='left'><a href='consultationAttributions.php'>Retour</a>
      </td>
      <td class='reserveSiLien'>&nbsp;</td>
      <td width='30%' align='left'>Réservation possible si lien</td>
      <td class='reserve'>&nbsp;</td>
      <td width='30%' align='left'>Chambres réservées</td>
   </tr>
</table>";

?>
