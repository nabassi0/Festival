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

// MODIFIER UN ÉTABLISSEMENT 
$tabhebergement=array("O","N"); 
$action=$_REQUEST['action'];
$id=$_REQUEST['id'];

// Si on ne "vient" pas de ce formulaire, il faut récupérer les données à partir 
// de la base (en appelant la fonction obtenirDetailEtablissement) sinon on 
// affiche les valeurs précédemment contenues dans le formulaire
if ($action=='demanderModifGroupe')
{
$lgGroupe=obtenirDetailGroupe($dbh, $id);

$nom=$lgGroupe['nom'];
$idres=$lgGroupe['identiteResponsable'];
$adressePostale=$lgGroupe['adressePostale'];
$nbpersonnes=$lgGroupe['nombrePersonnes'];
$nomPays=$lgGroupe['nomPays'];
$hebergement=$lgGroupe['hebergement'];
}
else
{
   $nom=$_REQUEST['nom']; 
   $idres=$_REQUEST['idres'];
   $adressePostale=$_REQUEST['adressePostale'];
   $nbpersonnes=$_REQUEST['nbpersonnes'];
   $nomPays=$_REQUEST['nomPays'];
   $hebergement=$_REQUEST['hebergement'];

   verifierDonneesGroupeM($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                        $nomPays, $hebergement);      
   if (nbErreurs()==0)
   {        
      modifierGroupe($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                     $nomPays, $hebergement);
   }
}

echo "
<form method='POST' action='modificationGroupe.php?'>
   <input type='hidden' value='validerModifGroupe' name='action'>
   <table width='85%' cellspacing='0' cellpadding='0' align='center' 
   class='tabNonQuadrille'>
   
      <tr class='enTeteTabNonQuad'>
         <td colspan='3'>$nom ($id)</td>
      </tr>
      <tr>
         <td><input type='hidden' value='$id' name='id'></td>
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
if ($action=='validerModifGroupe')
{
   if (nbErreurs()!=0)
   {
      afficherErreurs();
   }
   else
   {
      echo "
      <h5><center>La modification du Groupe a été effectué</center></h5>";
   }
}

?>
