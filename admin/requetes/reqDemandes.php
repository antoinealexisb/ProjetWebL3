<?php
/**
 * Page PHP qui change le status du compte.
 *
 * @package       /Admin/
 */
if(!isset($_SESSION)) {
     require("./../verif.php"); //petite sécurité si un utilisateur connait le lien.
}

$bdd = new PDO("sqlite:./../../projet.sqlite3");

if (isset($_POST['ajouter']) && !empty($_POST['ajouter'])){
	$id = htmlspecialchars($_POST['ajouter']);
	$tmp = $bdd->prepare("UPDATE membres SET actif=? WHERE id=?");
	$tmp->execute(array(1,$id));
}

if (isset($_POST['supp']) && !empty($_POST['supp'])){
	$id = htmlspecialchars($_POST['supp']);
	$tmp = $bdd->prepare("DELETE FROM membres WHERE id=?");
	$tmp->execute(array($id));
}


$allGame = $bdd->query("SELECT * FROM membres WHERE actif=0 ORDER BY id");//si joueur non actif.
?>
<tr><th>Id</th><th>Pseudo</th><th>email</th><th></th><th></th></tr><br/>
<?php
	while ($game = $allGame->fetch())
	{
		?>
		
		<tr>
			<td><?= $game['id']?></td>
			<td><?= $game['pseudo']?></td>
			<td><?= $game['email']?></td>
			<td><input type="button" class="btn btn-info" value="ajouter le joueur ?" onclick="rejoindre(<?= $game['id']; ?>);"/></td>
			<td><input type="button" class="btn btn-danger" value="Supprimer la demande" onclick="supp(<?= $game['id']; ?>)"/></td>
		</tr>
<?php
	}
?>