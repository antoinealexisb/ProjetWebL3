<!DOCTYPE xhtml>
<?php require('./../verifClient.php'); ?>
<?php /*
	mettre une verification si table existe pas retour sur le menu panel.
*/
		if (!isset($_POST['idtable'])){
			header("Location: ./../panel.php");
		}
		$idtable = htmlspecialchars($_POST['idtable']);
		$idtable = SQLite3::escapeString($idtable);
		$bdd = new PDO("sqlite:./../projet.sqlite3");
		$requser = $bdd->prepare("SELECT COUNT(*) FROM PartieEnCours WHERE idtable=? LIMIT 1");
		$requser->execute(array($idtable));
		$tmp = $requser->fetch()[0];
		if ($tmp <=0){
			header("Location: ./../panel.php");
		}
		else{
			$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? ORDER BY id DESC LIMIT 1");
			$requser->execute(array($idtable));
			$tmp2 = $bdd->prepare("SELECT * FROM secuJoueur WHERE idtable=? ORDER BY id DESC LIMIT 1");
			$tmp2->execute(array($idtable));
			$tmp = $requser->fetch();
			$tmp2 = $tmp2->fetchall()[0];
			if ($_SESSION['id'] == $tmp['Joueur1']){
				$secu = $tmp2['secuJ1'];
			}
			else{
				$secu = $tmp2['secuJ2'];
			}
		}

		/**regarder dans bd si table existe sinon degage*/

?>
<html>
<head>
	<script src="./js/jquery.min.js"></script> 
	<script src="./js/ajaxJeu.js"></script>
	<?php require("css.php") ?>
	<script>
		idtable=<?= $_POST['idtable']; ?>;
		idjoueur=<?= $_SESSION['id'];?>;
		secu=<?= $secu;?>;
	</script>
	<title>Jeu SIAM</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="./css/styleGame.css">
</head>
<body>
	<?php require("header.php"); ?>
    <div id="VS" class="btn btn-success"></div>
    <div id="blabla" class="btn btn-warning"></div>
<?php
	function affichageJoueur1($index){
		echo "<table id=\"Zpiece\"> \n <tr>\n";
		echo "</table>";
	}

	function affichageJoueur2($index){
		echo "<table id=\"Zpiece2\"> \n <tr>\n";
		echo "</table>";
	}

	function affichagePlateau(){
		global $obj;
		echo "<table id='PGame'> \n";
		echo "</table> \n";
	}

	function affichageSprite($direction, $nom, $position){
		return "<td id=\"".$position."\"><input type=\"button\" name=\"case1\" style=\"transform: rotate(".(90*$direction)."deg); width: 80px; height: 80px; background-image: url(&quot;img/".$nom.".png&quot;);\"/></td>\n";
	}

	affichageJoueur1(5);
	affichagePlateau();
	affichageJoueur2(6);
?>
<table>
	<tr>
		<td><button id="rotateUp" name="rotate">Rotation Haute</button></td>
		<td><button id="rotateDown" name="rotate">Rotation Bas</button></td>
		<td><button id="rotateLeft" name="rotate">Rotation Gauche</button></td>
		<td><button id="rotateRight" name="rotate">Rotation Droite</button></td>
		<td><button id="push" name="push">Pousser</button></td>
		<td><button id="end" name="end">Finir Tour</button></td>
		<td><button id="recup" name="recup">Recuperer sa piece </button></td>
	</tr>
</table>

<?php
if (isset($_SESSION['admin'])){
	if ($_SESSION['admin'] == 1){
		$bdd = new PDO("sqlite:./../projet.sqlite3");
		$tmp = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? LIMIT 1");
		$tmp->execute(array($_POST['idtable']));
		$tmp = $tmp->fetch();
		$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
		$requser->execute(array($tmp['Joueur1']));
		$sortie[] = $requser->fetch()[0];
		$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
		$requser->execute(array($tmp['Joueur2']));
		$sortie[] = $requser->fetch()[0];
		?>
		<br>
		<br>
		<div id="admin"> Mode Administrateur : <br>
			<select name="pets" id="pet-select">
		    	<option value="">Selectionnez le joueur que vous voulez incarner</option>
		    	<option value="" onclick="idjoueur=<?= $tmp['Joueur1']; ?>;"><?= $sortie[0]?></option>
		    	<option value="" onclick="idjoueur=<?= $tmp['Joueur2']; ?>;"><?= $sortie[1]?></option>
		    	<option value="hamster" onclick="idjoueur=<?= $_SESSION['id']; ?>;" selected="">Votre id de base</option>
			</select>
		</div>
		<?php
	}
}
?>
<?php require("footer.php"); ?>
</body>
</html>