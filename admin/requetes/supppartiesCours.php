<?php
/**
 * Page PHP qui permet de supprimer les partis en cours.
 *
 * @package       /Admin/
 */
if(!isset($_SESSION)) {
     require("./../verif.php"); //petite sécurité si un utilisateur connait le lien.
}

if (isset($_POST['idAvJeu']) && isset($_POST['idtablePC'])){
	//si admin s'est fait voler son compte, prévoir des mesures anti-injection, merci :D.
	$bdd = new PDO("sqlite:./../../projet.sqlite3");
	$requete = $bdd->prepare("DELETE FROM PartieEnCours WHERE idtable=?");
	$requete->execute(array($_POST['idtablePC']));
	$requete = $bdd->prepare("DELETE FROM avJeu WHERE id=?");
	$requete->execute(array($_POST['idAvJeu']));
}

?>