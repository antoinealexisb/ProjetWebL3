<?php
if(!isset($_SESSION)) {
     require("../verif.php"); //petite sécurité si un utilisateur connait le lien.
}

$bdd = new PDO("sqlite:../../projet.sqlite3");
$allGame = $bdd->prepare("SELECT * FROM avJeu WHERE actif=1 AND (Joueur1=? OR Joueur2=?)  ORDER BY id");//si jeu actif.
$allGame->execute(array($_SESSION['id'], $_SESSION['id']));
?>
<tr><td>Id </td><td>Id de la table </td><td>id du Joueur1 </td><td>id du Joueur2</td><td>Variante </td></tr><br>
<?php
	while ($game = $allGame->fetch())
	{
		$idtable = $game['idtable'];
		$tmp = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? ORDER BY id DESC LIMIT 1");
		$tmp->execute(array($idtable));
		$tmp = $tmp->fetch();
		$tmp2 = ($tmp['tour']%2==0);
		?>
		<tr>
			<td><?= $game['id']?></td>
			<td><?= $game['idtable']?></td>
			<?php if ($tmp2){?>
				<td><font color="red"><?= $game['Joueur1']?></font></td>
				<td><?= $game['Joueur2']?></td>

				<?php }else{?>
			<td><?= $game['Joueur1']?></td>
			<td><font color="red"><?= $game['Joueur2']?></font></td>
		<?php }?>
			<td><?php echo (($game['variante'] == 1) ? "Oui" : "Non");?></td>
			<td><input type="button" value="Jouer dans votre partie ?" class="btn btn-info" onclick="jouer('<?= $game['idtable']; ?>');"/></td>
		</tr>

<?php
	}
?>