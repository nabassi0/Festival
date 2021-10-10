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

// CRÉER UN ÉTABLISSEMENT 

// Déclaration du tableau des civilités
$tabhebergement=array("O","N"); 
$action=$_REQUEST['action'];

// S'il s'agit d'une création et qu'on ne "vient" pas de ce formulaire (on 
// "vient" de ce formulaire uniquement s'il y avait une erreur), il faut définir 
// les champs à vide sinon on affichera les valeurs précédemment saisies
if ($action=='demanderCreGroupe') 
{
$id='';  
$nom='';
$idres='';
$adressePostale='';
$nbpersonnes='';
$nomPays='';
$hebergement='';
}
else
{
   $id=$_REQUEST['id'];
   $nom=$_REQUEST['nom']; 
   $idres=$_REQUEST['idres'];
   $adressePostale=$_REQUEST['adressePostale'];
   $nbpersonnes=$_REQUEST['nbpersonnes'];
   $nomPays=$_REQUEST['nomPays'];
   $hebergement=$_REQUEST['hebergement'];

   verifierDonneesGroupeC($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                        $nomPays, $hebergement);      
   if (nbErreurs()==0)
   {        
      creerGroupe($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                     $nomPays, $hebergement);
   }
}

echo "
<form method='POST' action='creationgroupe.php?'>
   <input type='hidden' value='validerCreGroupe' name='action'>
   <table width='85%' align='center' cellspacing='0' cellpadding='0' 
   class='tabNonQuadrille'>
   
      <tr class='enTeteTabNonQuad'>
         <td colspan='3'>Nouveau Groupe</td>
      </tr>
      <tr class='ligneTabNonQuad'>
         <td> Id*: </td>
         <td><input type='text' value='' name='id' size ='10' 
         maxlength='8'></td>
      </tr>";
     
       echo '
      <tr class="ligneTabNonQuad">
         <td> Nom*: </td>
         <td><input type="text" value="'.$nom.'" name="nom" size="50" 
         maxlength="45"></td>
      </tr>
      <tr class="ligneTabNonQuad">
         <td> Identité Responsable : </td>
         <td><input type="text" value="'.$idres.'" name="idres" 
         size="50" maxlength="45"></td>
      </tr>
      <tr class="ligneTabNonQuad">
         <td> Adresse Postale : </td>
         <td><input type="text" value="'.$adressePostale.'" name="adressePostale" 
         size="50" maxlength="50"></td>
      </tr>
      <tr class="ligneTabNonQuad">
         <td> Nombre de personnes : </td>
         <td><input type="text" value="'.$nbpersonnes.'" name="nbpersonnes" size="2" 
         maxlength="2"></td>
      </tr>
      <tr class="ligneTabNonQuad">
         <td> Nom du Pays: </td>
         <td><input type="text" value="'.$nomPays.'" name="nomPays" size ="20" 
         maxlength="10"></td>
      </tr>';
      
           echo "
         <tr class='ligneTabNonQuad'>
            <td> Hebergement : </td>
            <td> <select name='hebergement'>";
               for ($i=0; $i<2; $i=$i+1)
                  if ($tabhebergement[$i]==$hebergement) 
                  {
                     echo "<option selected>$tabhebergement[$i]</option>";
                  }
                  else
                  {
                     echo "<option>$tabhebergement[$i]</option>";
                  }
               
   echo "</tr>
   <table align='center' cellspacing='15' cellpadding='0'>
      <tr>
         <td align='right'><input type='submit' value='Valider' name='valider'>
         </td>
         <td align='left'><input type='reset' value='Annuler' name='annuler'>
         </td>
      </tr>
      <tr>
         <td colspan='2' align='center'><a href='listeEquipes.php'>Retour</a>
         </td>
      </tr>
   </table>
  
</form>";

// En cas de validation du formulaire : affichage des erreurs ou du message de 
// confirmation
if ($action=='validerCreGroupe')
{
   if (nbErreurs()!=0)
   {
      afficherErreurs();
   }
   else
   {
      echo "
      <h5><center>La création du groupe a été effectué</center></h5>";
   }
}

?>
