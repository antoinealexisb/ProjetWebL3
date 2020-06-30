<?php
/**
 * Page PHP qui permet de visualiser la liste des parties que cet utilisateur a en cours.
 * pour chaque partie, mettre en évidence le joueur dont c'est le tour.
 *
 * @package       /requetes/
 */
if(!isset($_SESSION)) {
     require("./../verifClient.php"); //petite sécurité si un utilisateur connait le lien.
}

$bdd = new PDO("sqlite:./../projet.sqlite3");
$allGame = $bdd->prepare("SELECT * FROM avJeu WHERE actif=1 AND (Joueur1=? OR Joueur2=?)  ORDER BY id");//si jeu actif.
$allGame->execute(array($_SESSION['id'], $_SESSION['id']));
?>
<tr><td>Numero de la table </td><td>nom du Joueur1 </td><td>nom du Joueur2</td><td>Variante </td></tr><br>
<?php
	while ($game = $allGame->fetch())
	{
		$sortie=array();
		$idtable = $game['idtable'];
		$tmp = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? ORDER BY id DESC LIMIT 1");
		$tmp->execute(array($idtable));
		$tmp = $tmp->fetch();
		$tmp2 = ($tmp['tour']%2==0);
		$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
		$requser->execute(array($tmp['Joueur1']));
		$sortie[] = $requser->fetch()[0];
		$requser = $bdd->prepare("SELECT pseudo FROM membres WHERE id=?");
		$requser->execute(array($tmp['Joueur2']));
		$sortie[] = $requser->fetch()[0];
		?>
		<tr>
			<td><?= $game['idtable']?></td>
			<?php if ($tmp2){?>
				<td><font color="red"><?= $sortie[0]?></font></td>
				<td><?= $sortie[1]?></td>

			<?php }else{?>
				<td><?= $sortie[0]?></td>
				<td><font color="red"><?= $sortie[1]?></font></td>
			<?php }?>
			<td><?php echo (($game['variante'] == 1) ? "Oui" : "Non");?></td>
			<td><input type="button" value="Jouer dans votre partie ?" onclick="jouer('<?= $idtable; ?>');"/></td>
		</tr>

<?php
	}
?>