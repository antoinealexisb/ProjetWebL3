<?php
/*
* Fichier Admin.
* Permet de visualiser la liste des parties à rejoindre (parties en attente d'un joueur). 
* la version admin ne montre que les id des joueurs.
*/
//TODO LIST : faire le bouton pour rejoindre la partie.
if(!isset($_SESSION)) {
     require("../verif.php"); //seulement les membres peuvent recevoir les informations.
}

$bdd = new PDO("sqlite:./../../projet.sqlite3");
$allGame = $bdd->query("SELECT * FROM avJeu WHERE actif=0 ORDER BY id");//si jeu en attente d'un joueur.
?>
<tr><td>Id<td>Id de la table </td><td>Id du Joueur (Elephant) </td><td>Id du Joueur (Rhino) </td><td>Variante ?</td><td>Rejoindre ?</td></tr><br>
<?php
	while ($game = $allGame->fetch())
	{
		?>
		<tr>
			<td><?= $game['id']; ?></td>
			<td><?= $game['idtable']; ?></td>
			<td><?= $game['Joueur1']; ?></td>
			<td><?= $game['Joueur2']; ?></td>
			<td><?php echo (($game['variante'] == 1) ? "Oui" : "Non");?></td>
			<td><input type="button" value="Rejoindre ?" class="btn btn-info" onclick="rejoindre(<?= $game['id']; ?>,<?= $game['idtable']; ?>);"/></td>
		</tr>

<?php
	}
?>