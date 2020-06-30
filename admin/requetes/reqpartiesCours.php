<?php
/**
 * Page PHP qui permet de lister les partis en cours.
 *
 * @package       /Admin/requetes
 */
if(!isset($_SESSION)) {
     require("./../verif.php"); //petite sécurité si un utilisateur connait le lien.
}

$bdd = new PDO("sqlite:./../../projet.sqlite3");
$allGame = $bdd->query("SELECT * FROM avJeu WHERE actif=1 ORDER BY id");//si jeu actif.
?>
<tr><td>Id </td><td>Id de la table </td><td>id du Joueur1 </td><td>id du Joueur2</td><td>Variante </td></tr><br>
<?php
	while ($game = $allGame->fetch())
	{
		?>
		<tr>
			<td><?= $game['id']?></td>
			<td><?= $game['idtable']?></td>
			<td><?= $game['Joueur1']?></td>
			<td><?= $game['Joueur2']?></td>
			<td><?php echo (($game['variante'] == 1) ? "Oui" : "Non");?></td>
			<td><input type="button" value="Rejoindre ?" class="btn btn-warning" onclick="rejoindre(<?= $game['idtable'] ?>);"/></td>
			<td><input type="button" value="Supprimer ?"class="btn btn-danger"  onclick="supp(<?= $game['id'] ?>,<?= $game['idtable'] ?>)"/></td></tr>

<?php
	}
?>