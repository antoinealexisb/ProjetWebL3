<?php
/**
 * Page PHP qui affiche les joueurs et peut changer leur rang.
 *
 * @package       /Admin/
 */
if(!isset($_SESSION)) {
     require("./../verif.php"); //petite sécurité si un utilisateur connait le lien.
}

$bdd = new PDO("sqlite:./../../projet.sqlite3");

if (isset($_POST['admin']) && !empty($_POST['admin'])){
	$admin = htmlspecialchars($_POST['admin']);
	$admin = SQLite3::escapeString($admin);
	$requser = $bdd->prepare("SELECT * FROM membres WHERE id=?");
	$requser->execute(array($admin));
	$tmp2 = $requser->fetchall();
	$tmp2 = $tmp2[0];
	$tmp = $bdd->prepare("UPDATE membres SET admin=? WHERE id=?");
	$tmp->execute(array((($tmp2['admin']+1)%2),$admin));
}

if (isset($_POST['banni']) && !empty($_POST['banni'])){
	$banni = htmlspecialchars($_POST['banni']);
	$banni = SQLite3::escapeString($banni);
	$requser = $bdd->prepare("SELECT * FROM membres WHERE id=?");
	$requser->execute(array($banni));
	$tmp2 = $requser->fetchall()[0];
	$tmp = $bdd->prepare("UPDATE membres SET actif=? WHERE id=?");
	if ($tmp2['actif'] == 2){
		$tmp->execute(array(1,$banni));
	}
	else{
		$tmp->execute(array(2,$banni));
	}
}

if (isset($_POST['supp']) && !empty($_POST['supp'])){
	if ($_SESSION['admin'] == 1){
		$id = htmlspecialchars($_POST['supp']);
		$tmp = $bdd->prepare("DELETE FROM membres WHERE id=?");
		$tmp->execute(array($id));
	}
}


$allGame = $bdd->query("SELECT * FROM membres WHERE actif=1 OR actif=2 ORDER BY id");
?>
<tr><th>Id</th><th>Pseudo</th><th>email</th><th>Admin ?</th><th> Banni ?</th><th></th> Supprimer</tr><br/>
<?php
	while ($game = $allGame->fetch())
	{
		?>
		
		<tr>
			<td><?= $game['id']?></td>
			<td><?= $game['pseudo']?></td>
			<td><?= $game['email']?></td>
			<td><input type="checkbox" id="admin" name="banni" onclick="admini(<?= $game['id']; ?>)" <?php if($game['admin'] == 1){ echo "checked"; } ?>> </td>
			<td><input type="checkbox" id="banni" name="banni" onclick="bannir(<?= $game['id']; ?>)" <?php if($game['actif'] == 2){ echo "checked"; } ?>></td>
			<td><input type="button" class="btn btn-danger" value="Supprimer" onclick="supp(<?= $game['id']; ?>)"/></td>
		</tr>
<?php
	}
?>