<!DOCTYPE xhtml>
<?php require('verif.php'); ?>
<?php
/**
 * Page PHP qui permet de creer des comptes utilisateurs.
 *
 * @package       /Admin/
 */
$bdd = new PDO("sqlite:../projet.sqlite3");
if (isset($_POST['validation'])){
	$pseudo = htmlspecialchars($_POST['username'], ENT_QUOTES);
	$pseudo = SQLite3::escapeString($pseudo);
	$mdp = htmlspecialchars($_POST['password'], ENT_QUOTES);
	$mdp = SQLite3::escapeString($mdp);
	$email = htmlspecialchars($_POST['email'], ENT_QUOTES);
	$email = SQLite3::escapeString($email);
	if (isset($_POST['admin']) && htmlspecialchars($_POST['admin'], ENT_QUOTES)==1){
		$admin=1;
	}
	else{
		$admin=0;
	}
	if (!empty($pseudo) AND !empty($mdp)){
		//$mdp = hash('sha512',$mdp); 
		/*Avertissement

L'option Salt a été désapprouvée à partir de PHP 7.0.0. Il est maintenant préférable d'utiliser simplement le sel qui est généré par défaut.
*/
		$mdp = password_hash($mdp, PASSWORD_DEFAULT);
		$requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo=?"); //requete prepare pour savoir si pas déjà un speudo identique
		$requser->execute(array($pseudo));
		//$comptes = $requser->rowCount(); //cette commande ne fonctionne pas pour sqlite3 :joy:
		$comptes = $requser->fetchall();
		$nbComptes = count($comptes);
		if ($nbComptes >= 1){
			$erreur = "Le pseudo est déjà existant";
		}
		else{
			/**faire la redirection vers la page d'antho */
			$requser = $bdd->prepare("INSERT INTO membres (pseudo, mdp, email, actif, admin) VALUES (?,?,?,?,?)");
			$requser->execute(array($pseudo, $mdp, $email, 1, $admin));
			$erreur = "Compte ajouté";
		}


	}
	else{
		$erreur = "Tous les champs doivent être complétés"; //Ne pas oublier de que l'user est un chien et qu'il peut modifier son code client ;)
	}
}


 ?>
<html>
<head>
	<title>Panel Admin | Demande d'ajout</title>
	<script src="./jquery.min.js"></script>
	<?php require('css.php'); ?>
</head>
	<div class="wrapper">
<?php require('header.php');
require('droit.php');?>
<div class="content-wrapper" style="min-height: 465px;">
	<h1> Bonjour Administrateur </h1>
	<br>
	<h1> Création d'un compte </h1>
	<br>
		<div id="contenair" align="center">
    	<form action="" method="POST">
            <label>
                <b> Pseudo : </b>
            </label>
            <input type="text" placeholder="Pseudo" name="username" required>
            <br/>
            <label>
                <b> Mot de passe : </b>
            </label>
            <input type="text" placeholder="Entrer le mot de passe" name="password" required>
            <br/>
            <label>
                <b> Email : </b>
            </label>
            <input type="email" placeholder="Entrer l'adresse mail" name="email" required>
            <br/>
            <label>
                <b> Admin : </b>
            </label>
            <input type="checkbox" id="admin" name="admin" value="1">
            <br/>
            <br/>
            <input type="submit" id="validation" name="validation" value="Enregistrer">
        </form>
        <?php
        	if(isset($erreur)) {
        		echo '<font color="red">'.$erreur."</font>";
        	}
        ?>
	    </div>
	</div>
</div>
</body>
</html>