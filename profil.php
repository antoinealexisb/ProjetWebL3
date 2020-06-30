<!DOCTYPE html>
<?php require('verifClient.php'); ?>
<?php
/**
 * Page de profil de l'utilisateur.
 * Ici il peut changer sa photo de profil, son mot de passe et sa bio.
 *
 * @package       /
 */
	if (isset($_POST['validation'])){
		$oldmdp = htmlspecialchars($_POST['mdp']);
    $oldmdp = SQLite3::escapeString($oldmdp);
		$newmdp = htmlspecialchars($_POST['mdp2']);
    $newmdp = SQLite3::escapeString($newmdp);
		if (!empty($oldmdp) AND !empty($newmdp)){
			$bdd = new PDO("sqlite:projet.sqlite3");
			$oldmdp = hash('sha512',$oldmdp);
			$newmdp = hash('sha512',$newmdp);
			$requser = $bdd->prepare("SELECT COUNT(*) from membres WHERE mdp=? AND id=?");
			$requser->execute(array($oldmdp, $_SESSION['id']));
			$nbcomptes = $requser->fetchall()[0]['COUNT(*)'];
			if ($nbcomptes == 1){
				$requser = $bdd->prepare("UPDATE membres set mdp=? where mdp=? AND id=?");
				$requser->execute(array($newmdp, $oldmdp, $_SESSION['id']));
				$erreur = "Mot de passe changé";
			}
			else {
				$erreur = "Votre mot de passe actuel ne correspond pas !";
			}
		}
		else{
			$erreur = "Vous devez renseigné tout les champs.";
		}
	}




?>
<html>
<head>
	<title>Profil de <?= $_SESSION['pseudo']; ?></title>
  <?php require('css.php');?>
</head>
<body>
  <?php require('header.php');?>
  <div style="padding: 30px;"></div>


<div id="contenair" align="left">
 <form method="POST" action="">
    <table style="width: 50%;">
       <tr>
          <td align="right">
             <label for="mdp">Ancien mot de passe :</label>
          </td>
          <td>
             <input type="password" placeholder="Votre mot de passe Actuel" id="mdp" name="mdp" />
          </td>
       </tr>
       <tr>
          <td align="right">
             <label for="mdp2">Nouveau du mot de passe :</label>
          </td>
          <td>
             <input type="password" placeholder="Votre nouveau mot de passe" id="mdp2" name="mdp2" />
          </td>
       </tr>
       <tr>
          <td></td>
          <td align="center">
             <br />
             <input type="submit" name="validation" value="Modifier !" />
          </td>
       </tr>
    </table>
 </form>
 <?php
 if(isset($erreur)) {
    echo '<font color="red">'.$erreur."</font>";
 }
 ?>
</div>
</body>
</html>