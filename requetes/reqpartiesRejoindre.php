<?php
/**
 * Fichier Joueur.
 * Permet de visualiser la liste des parties à rejoindre (parties en attente d'un joueur). 
 * @package       /requetes/
 */
if(!isset($_SESSION)) {
     require("./../verifClient.php"); //seulement les membres peuvent recevoir les informations.
}

$bdd = new PDO("sqlite:./../projet.sqlite3");
$allGame = $bdd->query("SELECT * FROM avJeu WHERE actif=0 ORDER BY id");//si jeu en attente d'un joueur.
?>
<tr><td>Numéro de la table </td><td>Nom du Joueur (Elephant) </td><td>Nom du Joueur (Rhino) </td><td>Variante ?</td><td>Rejoindre ?</td></tr><br>
<?php
	while ($game = $allGame->fetch())
	{
		$sortie = array();
		if ($game['Joueur1'] > 0){
			$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
			$requser->execute(array($game['Joueur1']));
			$sortie[] = $requser->fetch()[0];
			$sortie[] ="";
		}
		else{
			$sortie[] = "";
			$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
			$requser->execute(array($game['Joueur2']));
			$sortie[] = $requser->fetch()[0];
		}
		?>
		<tr>
			<td><?= $game['idtable']?></td>
			<td><?= $sortie[0]?></td>
			<td><?= $sortie[1]?></td>
			<td><?php echo (($game['variante'] == 1) ? "Oui" : "Non");?></td>
			<td><input type="button" value="Rejoindre ?" onclick="rejoindre(<?= $game['id']; ?>,<?= $game['idtable']; ?>);"/></td>
		</tr>

<?php
	}
?>