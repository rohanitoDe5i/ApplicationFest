
<?php

// FONCTIONS DE CONNEXION

function connect()
{
   $hote='localhost';
   $login='root';
   $mdp='';
   try
   {
    	$connexion = new PDO('mysql:host='.$hote.';dbname=festival', $login, $mdp);
   }
   catch(Exception $e)
   {
   		die('erreur: '.$e->getmessage());
   }
   return $connexion;
}

function selectBase($connexion)
{
   $query="SET CHARACTER SET utf8";
   // Modification du jeu de caractères de la connexion
   $connexion->exec($query);
   return $connexion;
}

// FONCTIONS DE GESTION DES ÉTABLISSEMENTS


function obtenirReqEtablissements()
{
   $req="select id, nom, nombreChambresOffertes from Etablissement order by id";
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

function obtenirDetailEtablissement($connexion, $id)
{
   $req="select * from Etablissement where id='$id'";
   $rsEtab=$connexion->query($req);
   return $rsEtab->fetch();
}

function supprimerEtablissement($connexion, $id)
{
   $req="delete from Etablissement where id='$id'";
   $connexion->query($req);
   return $connexion;
}
 
function modifierEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, 
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
   
  $connexion->query($req);
  return $connexion;
}

function creerEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, 
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
   
  $connexion->query($req);
  return $connexion;
}


function estUnIdEtablissement($connexion, $id)
{
   $req="select * from Etablissement where id='$id'";
   $rsEtab=$connexion->query($req);
   return $rsEtab->fetch();
}

function estUnNomEtablissement($connexion, $mode, $id, $nom)
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
   $rsEtab=$connexion->query($req);
   return $rsEtab->fetch();
}

function obtenirNbEtab($connexion)
{
   $req="select count(*) as nombreEtab from Etablissement";
   $rsEtab->query($req);
   $lgEtab->array($rsEtab);
   return $lgEtab["nombreEtab"];
}

function obtenirNbEtabOffrantChambres($connexion)
{
   $req="select count(*) as nombreEtabOffrantChambres from Etablissement where 
         nombreChambresOffertes!=0";
   $rsEtabOffrantChambres=$connexion->query($req);
   $lgEtabOffrantChambres=$rsEtabOffrantChambres->fetch();
   return $lgEtabOffrantChambres['nombreEtabOffrantChambres'];
}

// Retourne false si le nombre de chambres transmis est inférieur au nombre de 
// chambres occupées pour l'établissement transmis 
// Retourne true dans le cas contraire
function estModifOffreCorrecte($connexion, $idEtab, $nombreChambres)
{
   $nbOccup=obtenirNbOccup($connexion, $idEtab);
   return ($nombreChambres>=$nbOccup);
}

// FONCTIONS RELATIVES AUX GROUPES

function obtenirReqIdNomGroupesAHeberger()
{
   $req="select id, nom, nomPays from Groupe where hebergement='O' order by id";
   return $req;
}

function obtenirNomGroupe($connexion, $id)
{
   $req="select nom from Groupe where id='$id'";
   $rsGroupe=$connexion->query($req);
   $lgGroupe=$rsGroupe->fetch();
   return $lgGroupe['nom'];
}

// FONCTIONS RELATIVES AUX ATTRIBUTIONS


// Retourne le nombre d'attributions
function obtenirNbAttrib($connexion,$id)
{
	$req="select count(*) as nbAttrib from Attribution where idEtab='$id'";
	$rsAttrib=$connexion->query($req);
	$lgAttrib=$rsAttrib->fetch();
	return $lgAttrib['nbAttrib'];
}

// Teste la présence d'attributions pour l'établissement transmis    
function existeAttributionsEtab($connexion, $id)
{
   $req="select * From Attribution where idEtab='$id'";
   $rsAttrib=$connexion->query($req);
   return $rsAttrib->fetch();
}

// Retourne le nombre de chambres occupées pour l'id étab transmis
function obtenirNbOccup($connexion, $idEtab)
{
   $req="select IFNULL(sum(nombreChambres), 0) as totalChambresOccup from
        Attribution where idEtab='$idEtab'";
   $rsOccup=$connexion->query($req);
   $lgOccup=$rsOccup->fetch();
   return $lgOccup['totalChambresOccup'];
}

// Met à jour (suppression, modification ou ajout) l'attribution correspondant à
// l'id étab et à l'id groupe transmis
function modifierAttribChamb($connexion, $idEtab, $idGroupe, $nbChambres)
{
   $req="select count(*) as nombreAttribGroupe from Attribution where idEtab=
        '$idEtab' and idGroupe='$idGroupe'";
   $rsAttrib=$connexion->query($req);
   $lgAttrib=$rsAttrib->fetch();
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
   $connexion->query($req);
}

// Retourne la requête permettant d'obtenir les id et noms des groupes affectés
// dans l'établissement transmis
function obtenirReqGroupesEtab($id)
{
   $req="select distinct id, nom, nomPays from Groupe, Attribution where 
        Attribution.idGroupe=Groupe.id and idEtab='$id'";
   return $req;
}
            
// Retourne le nombre de chambres occupées par le groupe transmis pour l'id étab
// et l'id groupe transmis
function obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe)
{
   $req="select nombreChambres From Attribution where idEtab='$idEtab'
        and idGroupe='$idGroupe'";
   $rsAttribGroupe=$connexion->query($req);
   if ($lgAttribGroupe=$rsAttribGroupe->fetch())
      return $lgAttribGroupe["nombreChambres"];
   else
      return 0;
}

?>

