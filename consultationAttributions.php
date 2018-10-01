<?php

include("_debut.inc.php");
echo"<title>Accueil > ListeEtablissement</title>";
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
if (!selectBase($connexion))
{
   ajouterErreur("La base de données festival est inexistante ou non accessible");
   afficherErreurs();
   exit();
}

if(!isset($_REQUEST['action']))
	$action='lol';
else
	$action=$_REQUEST['action'];

if($action=='validerModifAttrib')
{
   $idEtab=$_REQUEST['idEtab'];
   $idGroupe=$_REQUEST['idGroupe'];
   $nbChambres=$_REQUEST['nbChambres'];
   modifierAttribChamb($connexion, $idEtab, $idGroupe, $nbChambres);
}

// CONSULTER LES ATTRIBUTIONS DE TOUS LES ÉTABLISSEMENTS

// IL FAUT QU'IL Y AIT AU MOINS UN ÉTABLISSEMENT OFFRANT DES CHAMBRES POUR  
// AFFICHER LE LIEN VERS LA MODIFICATION
$nbEtab=obtenirNbEtabOffrantChambres($connexion);
if ($nbEtab!=0)
{
   echo "
   <table width='75%' cellspacing='0' cellpadding='0' align='center'
   <tr><td>
   <a href='modificationAttributions.php?action=demanderModifAttrib'>
   Effectuer ou modifier les attributions</a></td></tr></table><br><br>";
   
   // POUR CHAQUE ÉTABLISSEMENT : AFFICHAGE D'UN TABLEAU COMPORTANT 2 LIGNES 
   // D'EN-TÊTE ET LE DÉTAIL DES ATTRIBUTIONS
   $req=obtenirReqEtablissementsAyantChambresAttribuées();
   $rsEtab=$connexion->query($req);
   $lgEtab=$rsEtab->fetch();
   // BOUCLE SUR LES ÉTABLISSEMENTS AYANT DÉJÀ DES CHAMBRES ATTRIBUÉES
   while($lgEtab!=FALSE)
   {
      $idEtab=$lgEtab['id'];
      $nomEtab=$lgEtab['nom'];
   
      echo "
      <table width='75%' cellspacing='0' cellpadding='0' align='center' 
      class='tabQuadrille'>";
      
      $nbOffre=intval($lgEtab["nombreChambresOffertes"]);
      $nbOccup=intval(obtenirNbOccup($connexion, $idEtab));
      // Calcul du nombre de chambres libres dans l'établissement
      $nbChLib = $nbOffre - $nbOccup;
      
      // AFFICHAGE DE LA 1ÈRE LIGNE D'EN-TÊTE 
      echo "
      <tr class='enTeteTabQuad'>
         <td colspan='4' align='left'><strong>$nomEtab</strong>&nbsp;
         (Offre : $nbOffre&nbsp;&nbsp;Disponibilités : $nbChLib)
         </td>
      </tr>";
          
      // AFFICHAGE DE LA 2ÈME LIGNE D'EN-TÊTE 
      echo "
      <tr class='ligneTabQuad'>
         <td width='35%' align='center'><i><strong>Nom équipe</strong></i></td>
		 <td width='15%' align='center'><i><strong>Pays</strong></i>
         <td width='25%' align='center'><i><strong>Chambres attribuées</strong></i>
		 <td width='25%' align='center'><i><strong>Modifier</strong></i>
         </td>
      </tr>";



        
      // AFFICHAGE DU DÉTAIL DES ATTRIBUTIONS : UNE LIGNE PAR GROUPE AFFECTÉ 
      // DANS L'ÉTABLISSEMENT       
      $req=obtenirReqGroupesEtab($idEtab);
      $rsGroupe=$connexion->query($req);
      $lgGroupe=$rsGroupe->fetch();
      // BOUCLE SUR LES GROUPES (CHAQUE GROUPE EST AFFICHÉ EN LIGNE)
      while($lgGroupe!=FALSE)
      {
         $idGroupe=$lgGroupe['id'];
         $nomGroupe=$lgGroupe['nom'];
         $nomPays=$lgGroupe['nomPays'];

         echo "
         <tr class='ligneTabQuad'>
            <td width='35%' align='left'>$nomGroupe</td>;
			<td width='15%' align='left'>$nomPays</td>";
         // recherche de chambres déjà attribuées à ce groupe
         $nbOccupGroupe=obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe);
         echo "

            <td width='35%' align='right'>
             <form  method='POST' action='consultationAttributions.php'>
			</td>
			<td width='35%' align='left'>
             $nbOccupGroupe
			
			 <input type='hidden' value='validerModifAttrib' name='action'>
			 <input type='hidden' value='$idEtab' name='idEtab'>
			 <input type='hidden' value='$idGroupe' name='idGroupe'>
             <select name='nbChambres'>";
		for ($i=0; $i<=$nbOffre-$nbOccup+$nbOccupGroupe; $i++)
		{
			echo "<option";
			if($nbOccupGroupe==$i)
				echo" selected";
			echo">$i</option>";
		}
   	echo"</select>
   		 <input type='submit' value='Valider' name='valider'></form></td>
         </tr>";
         $lgGroupe=$rsGroupe->fetch();
      }
      
      echo "
      </table><br>";
      $lgEtab=$rsEtab->fetch();
   }
}

?>
