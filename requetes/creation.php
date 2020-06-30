<?php 
/**
 * Page PHP qui creer la partie de l'utilsateur dans la BD (dans la table avJeu).
 *
 * @package       /
 */
if(!isset($_SESSION)) {
     require("./verifClient.php");
}

$bdd = new PDO("sqlite:./projet.sqlite3");
if (isset($_POST['validation'])){
	if (isset($_POST['piece']) && isset($_POST['variante'])){
		$piece = htmlspecialchars($_POST['piece']);
   		$piece = SQLite3::escapeString($piece);
		$variante = htmlspecialchars($_POST['variante']);
    	$variante = SQLite3::escapeString($variante);
		$idtable = time();
		if ((($piece < 0) OR ($piece >1)) OR ($variante<0 OR $variante>1)){
			$erreur = "Votre action ne peut aboutir";
		}
		else{
			if ($piece == 0){
				$insert = $bdd->prepare('INSERT INTO avJeu (idtable, Joueur1, Joueur2, variante, actif) VALUES(?, ?, ?, ?, ?)');
				$insert->execute(array($idtable, $_SESSION['id'],0 , $variante, 0));
			}
			else{
				$insert = $bdd->prepare('INSERT INTO avJeu (idtable, Joueur1, Joueur2, variante, actif) VALUES(?, ?, ?, ?, ?)');
				$insert->execute(array($idtable, 0, $_SESSION['id'], $variante, 0));
			}
			$erreur = "Votre partie a été créée !";
		}
	}
	else{
		$erreur = "Vous devez remplir tout les champs"; //Ne pas oublier de que l'user est un chien et qu'il peut modifier son code client ;)
	}
}
?>