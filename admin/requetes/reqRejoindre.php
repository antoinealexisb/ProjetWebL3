<?php
/**
 * Page PHP qui creer la partie de l'utilsateur dans la BD (dans la table PartieEnCours).
 * Met le statut de la partie dans avJeu en en cours.
 *
 * @package       /Admin/requetes/
 */
$plateau = '{"plateau": [[["00",-1],["01",-1],["02",-1],["03",-1],["04",-1]],[["10",-1],["11",-1],["12",-1],["13",-1],["14","-1"]],[["20",-1],["70",0],["71",0],["72",0],["24",-1]],[["30",-1],["31",-1],["32",-1],["33",-1],["34",-1]],[["40",-1],["41",-1],["42",-1],["43",-1],["44",-1]],[["50",0],["51",0],["52",0],["53",0],["54",0]],[["60",0],["61",0],["62",0],["63",0],["64",0]]]}';

if(!isset($_SESSION)) {
     require("../verif.php"); //seulement les membres peuvent recevoir les informations.
}
$bdd = new PDO("sqlite:./../../projet.sqlite3");
if (isset($_POST['id']) && isset($_POST['idtable']) && !empty($_POST['id']) && !empty($_POST['idtable'])){
	$id = htmlspecialchars($_POST['id']);
	$idtable = htmlspecialchars($_POST['idtable']);
	$requser = $bdd->prepare("SELECT * FROM avJeu WHERE id=?");
	$requser->execute(array($id));
	$retour = $requser->fetch();
	if (($_SESSION['id']==$retour['Joueur1']) OR ($_SESSION['id']==$retour['Joueur2'])){
		header("Location: ./../rejoindreParties.php");
	}
	else if ($retour['actif'] == 0){//vérification, on ne sait pas si quelqu'un à deja cliquer et de l'autre côté la partie était encore proposé.
		$tmp = $bdd->prepare("UPDATE avJeu set actif=?, Joueur1=?, Joueur2=? WHERE id=?");
		if ($retour['Joueur1']>0){
			$tmp->execute(array(1, $retour['Joueur1'], $_SESSION['id'], $id));
			$retour['Joueur2'] = $_SESSION['id'];
		}
		else{
			$tmp->execute(array(1, $_SESSION['id'], $retour['Joueur2'], $id));
			$retour['Joueur1'] = $_SESSION['id'];
		}
		$tmp = $bdd->prepare("INSERT INTO PartieEnCours(idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2)VALUES (?,?,?,?,?,?,?,?)");
		$tmp->execute(array($idtable, $retour['Joueur1'], $retour['Joueur2'], $plateau, 0, $retour['variante'], 0, 0));
		$tmp = $bdd->prepare("INSERT INTO secuJoueur(idtable, secuJ1, secuJ2)VALUES (?,?,?)");
		$tmp->execute(array($idtable, rand(1,10000), rand(1,10000)));
		?>
		<html>
		<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
		</head>
		<body>
		<script>
		var form = $('<form action="../../jeu/plateau.php" method="post" hidden="true">' + '<input type="password" name="idtable" value="'+<?= $retour['idtable']; ?>+'" />' + '</form>');
		$('body').append(form);
		form.submit();
		</script>
		</body>
		</html>
		<?php

	}
	else{
		header("Location: ./../rejoindreParties.php");
	}

}
	else{
		header("Location: ./../rejoindreParties.php"); //si l'utilisateur à ecrit nimps.
	}
?>