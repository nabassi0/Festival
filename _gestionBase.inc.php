<?php

// FONCTIONS DE CONNEXION

function connect()
{ 
$user = 'root'; 
$pass = ''; 
$dsn = 'mysql:host=localhost;dbname=festival'; 
try{ 
	global $dbh;
   $dbh = new PDO($dsn, $user, $pass); 
} catch (PDOException $e){ 
print "Erreur ! :" . $e->getMessage() . "<br/>"; 
die(); }
$dbh->query("SET NAMES UTF8");
return true;
}

// FONCTIONS DE GESTION DES ÉTABLISSEMENTS

function obtenirReqEtablissements()
{
   $req="select id, nom from Etablissement order by id";
   return $req;
}

function obtenirReqEtablissementsOffrantChambres()
{
   $req="select id, nom, nombreChambresOffertes from Etablissement where 
         nombreChambresOffertes!=0 order by id";
   return $req;
}

function obtenirReqEtablissementsAyantChambresAttribuées()
{
   $req="select distinct id, nom, nombreChambresOffertes from Etablissement, 
         Attribution where id = idEtab order by id";
   return $req;
}

function obtenirDetailEtablissement($dbh, $id)
{
   $req="select * from Etablissement where id='$id'";
   $rsEtab=$dbh->query($req);
   $lgEtab=$rsEtab->fetch(PDO::FETCH_ASSOC);
   return $lgEtab;
}

function supprimerEtablissement($dbh, $id)
{
   $req="delete from Etablissement where id='$id'";
   $rsEtab=$dbh->query($req);
}
 
function modifierEtablissement($dbh, $id, $nom, $adresseRue, $codePostal, 
                               $ville, $tel, $adresseElectronique, $type, 
                               $civiliteResponsable, $nomResponsable, 
                               $prenomResponsable, $nombreChambresOffertes)
{  
   $nom=str_replace("'", "''", $nom);
   $adresseRue=str_replace("'","''", $adresseRue);
   $ville=str_replace("'","''", $ville);
   $adresseElectronique=str_replace("'","''", $adresseElectronique);
   $nomResponsable=str_replace("'","''", $nomResponsable);
   $prenomResponsable=str_replace("'","''", $prenomResponsable);
  
   $req="update Etablissement set nom='$nom',adresseRue='$adresseRue',
         codePostal='$codePostal',ville='$ville',tel='$tel',
         adresseElectronique='$adresseElectronique',type='$type',
         civiliteResponsable='$civiliteResponsable',nomResponsable=
         '$nomResponsable',prenomResponsable='$prenomResponsable',
         nombreChambresOffertes='$nombreChambresOffertes' where id='$id'";
   
   $rsEtab=$dbh->query($req);
}

function creerEtablissement($dbh, $id, $nom, $adresseRue, $codePostal, 
                            $ville, $tel, $adresseElectronique, $type, 
                            $civiliteResponsable, $nomResponsable, 
                            $prenomResponsable, $nombreChambresOffertes)
{ 
   $nom=str_replace("'", "''", $nom);
   $adresseRue=str_replace("'","''", $adresseRue);
   $ville=str_replace("'","''", $ville);
   $adresseElectronique=str_replace("'","''", $adresseElectronique);
   $nomResponsable=str_replace("'","''", $nomResponsable);
   $prenomResponsable=str_replace("'","''", $prenomResponsable);
   
   $req="insert into Etablissement values ('$id', '$nom', '$adresseRue', 
         '$codePostal', '$ville', '$tel', '$adresseElectronique', '$type', 
         '$civiliteResponsable', '$nomResponsable', '$prenomResponsable',
         '$nombreChambresOffertes')";
   
   $rsEtab=$dbh->query($req);
}


function estUnIdEtablissement($dbh, $id)
{
   $req="select * from Etablissement where id='$id'";
   $rsEtab=$dbh->query($req);
   return $rsEtab->fetch(PDO::FETCH_ASSOC);
}

function estUnNomEtablissement($dbh, $mode, $id, $nom)
{
   $nom=str_replace("'", "''", $nom);
   // S'il s'agit d'une création, on vérifie juste la non existence du nom sinon
   // on vérifie la non existence d'un autre établissement (id!='$id') portant 
   // le même nom
   if ($mode=='C')
   {
      $req="select * from Etablissement where nom='$nom'";
   }
   else
   {
      $req="select * from Etablissement where nom='$nom' and id!='$id'";
   }
   $rsEtab=$dbh->query($req);
   return $rsEtab->fetch(PDO::FETCH_ASSOC);
}

function obtenirNbEtab($dbh)
{
   $req="select count(*) as nombreEtab from Etablissement";
   $rsEtab=$dbh->query($req);
   $lgEtab=$rsEtab->fetch(PDO::FETCH_ASSOC);
   return $lgEtab["nombreEtab"];
}

function obtenirNbEtabOffrantChambres($dbh)
{
   $req="select count(*) as nombreEtabOffrantChambres from Etablissement where 
         nombreChambresOffertes!=0";
   $rsEtabOffrantChambres=$dbh->query($req);
   $lgEtabOffrantChambres=$rsEtabOffrantChambres->fetch(PDO::FETCH_ASSOC);
   return $lgEtabOffrantChambres["nombreEtabOffrantChambres"];
}

// Retourne false si le nombre de chambres transmis est inférieur au nombre de 
// chambres occupées pour l'établissement transmis 
// Retourne true dans le cas contraire
function estModifOffreCorrecte($dbh, $idEtab, $nombreChambres)
{
   $nbOccup=obtenirNbOccup($dbh, $idEtab);
   return ($nombreChambres>=$nbOccup);
}

// FONCTIONS RELATIVES AUX GROUPES

function obtenirReqIdNomGroupesAHeberger()
{
   $req="select id, nom, nomPays, nombrePersonnes from Groupe where hebergement='O' order by id"; // on rajoute nomPays et bnombrePersonnes
   return $req;
}

function obtenirNomGroupe($dbh, $id)
{
   $req="select nom from Groupe where id='$id'";
   $rsGroupe=$dbh->query($req);
   $lgGroupe=$rsGroupe->fetch(PDO::FETCH_ASSOC);
   return $lgGroupe["nom"];
}

// FONCTIONS RELATIVES AUX ATTRIBUTIONS

// Teste la présence d'attributions pour l'établissement transmis    
function existeAttributionsEtab($dbh, $id)
{
   $req="select * From Attribution where idEtab='$id'";
   $rsAttrib=$dbh->query($req);
   $lgAttrib=$rsAttrib->fetch(PDO::FETCH_ASSOC);
   return $lgAttrib;
}

// Retourne le nombre de chambres occupées pour l'id étab transmis
function obtenirNbOccup($dbh, $idEtab)
{
   $req="select IFNULL(sum(nombreChambres), 0) as totalChambresOccup from
        Attribution where idEtab='$idEtab'";
   $rsOccup=$dbh->query($req);
   $lgOccup=$rsOccup->fetch(PDO::FETCH_ASSOC);
   return $lgOccup["totalChambresOccup"];
}

// Met à jour (suppression, modification ou ajout) l'attribution correspondant à
// l'id étab et à l'id groupe transmis
function modifierAttribChamb($dbh, $idEtab, $idGroupe, $nbChambres)
{
   $req="select count(*) as nombreAttribGroupe from Attribution where idEtab=
        '$idEtab' and idGroupe='$idGroupe'";
   $rsAttrib=$dbh->query($req);
   $lgAttrib=$rsAttrib->fetch(PDO::FETCH_ASSOC);
   if ($nbChambres==0)
      $req="delete from Attribution where idEtab='$idEtab' and idGroupe='$idGroupe'";
   else
   {
      if ($lgAttrib["nombreAttribGroupe"]!=0)
         $req="update Attribution set nombreChambres=$nbChambres where idEtab=
              '$idEtab' and idGroupe='$idGroupe'";
      else
         $req="insert into Attribution values('$idEtab','$idGroupe', $nbChambres)";
   }
   $dbh->query($req);
}

// Retourne la requête permettant d'obtenir les id et noms des groupes affectés
// dans l'établissement transmis
function obtenirReqGroupesEtab($id)
{
   $req="select distinct id, nom, nomPays from Groupe, Attribution where 
        Attribution.idGroupe=Groupe.id and idEtab='$id'"; // on rajoute nomPays dans la fonction.
   return $req;
}
            
// Retourne le nombre de chambres occupées par le groupe transmis pour l'id étab
// et l'id groupe transmis
function obtenirNbOccupGroupe($dbh, $idEtab, $idGroupe)
{
   $req="select nombreChambres From Attribution where idEtab='$idEtab'
        and idGroupe='$idGroupe'";
   $rsAttribGroupe=$dbh->query($req);
   if ($lgAttribGroupe=$rsAttribGroupe->fetch(PDO::FETCH_ASSOC))
      return $lgAttribGroupe["nombreChambres"];
   else
      return 0;
}

function obtenirReqGroupes()
{
   $req="select * From Groupe";
   return $req;
}

// Teste la présence d'attributions pour l'établissement transmis    
function existeAttributionsGroupes($dbh, $id)
{
   $req="select * From Attribution where idGroupe='$id'";
   $rsAttrib=$dbh->query($req);
   $lgAttrib=$rsAttrib->fetch(PDO::FETCH_ASSOC);
   return $lgAttrib;
}

function obtenirDetailGroupe($dbh, $id)
{
   $req="select * From Groupe where id='$id'";
   $rsGroupe=$dbh->query($req);
   $lgGroupe=$rsGroupe->fetch(PDO::FETCH_ASSOC);
   return $lgGroupe;
}

function estUnNomGroupe($dbh, $id, $nom)
{
   $nom=str_replace("'", "''", $nom);
   // S'il s'agit d'une création, on vérifie juste la non existence du nom sinon
   // on vérifie la non existence d'un autre groupe (id!='$id') portant 
   // le même nom
      $req="select * from Groupe where nom='$nom'";
      $rsGroupe=$dbh->query($req);
      return $rsGroupe->fetch(PDO::FETCH_ASSOC);
}

function modifierGroupe($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                        $nomPays, $hebergement)    
{  
   $nom=str_replace("'", "''", $nom);
   $idres=str_replace("'","''", $idres);
   $adressePostale=str_replace("'","''", $adressePostale);
   $nbpersonnes=str_replace("'","''", $nbpersonnes);
   $nomPays=str_replace("'","''", $nomPays);
   $hebergement=str_replace("'","''", $hebergement);
  
   $req="update Groupe set nom='$nom',identiteResponsable='$idres',
         adressePostale='$adressePostale',nombrePersonnes='$nbpersonnes',nomPays='$nomPays',
         hebergement='$hebergement' where id='$id'";
   
   $rsGroupe=$dbh->query($req);
}

function creerGroupe($dbh, $id, $nom, $idres, $adressePostale, $nbpersonnes,  
                        $nomPays, $hebergement)    
{ 
   $nom=str_replace("'", "''", $nom);
   $idres=str_replace("'","''", $idres);
   $adressePostale=str_replace("'","''", $adressePostale);
   $nbpersonnes=str_replace("'","''", $nbpersonnes);
   $nomPays=str_replace("'","''", $nomPays);
   $hebergement=str_replace("'","''", $hebergement);
   
   $req="insert into Groupe values ('$id', '$nom', '$idres', '$adressePostale', 
         '$nbpersonnes', '$nomPays', '$hebergement')";
   
   $rsGroupe=$dbh->query($req);
}

function supprimerGroupe($dbh, $id)
{
   $req="delete from Groupe where id='$id'";
   $rsGroupe=$dbh->query($req);
}
?>

