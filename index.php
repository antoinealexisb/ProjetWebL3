<!DOCTYPE xhtml>
<?php 
/**
 * Page de connexion des utilisateurs.
 *
 * @package       /
 */
session_start();
$dbname = './projet.sqlite3';
$bdd = new PDO("sqlite:$dbname");

if (isset($_SESSION) && isset($_SESSION['id'])){
	if (isset($_SESSION['admin']) && isset($_SESSION['admin'])==1){
		header("Location: admin/index.php");
	}
	else{
		header("Location: panel.php");
	}
}

//si formulaire envoyer.
if (isset($_POST['validation'])){

	/*Attention il ne faut pas confondre les injections SQL et XSS.
	Ici le htmlspecialchars sert surtout pour eviter XSS, et il ne faut pas oublier de mettre le ENT_QUOTES. (la fonction htmlentities ne sert plus trop de nos
	jours car était surtout utilisé pour les lettres avec accents mais de nos jours c'est compatible).
	Pour eviter les injections SQL il faut utiliser mysqli_real_escape_string() ou sqlite_escape_string() ici se sera SQLite3::escapeString() comme conseiller dans la doc;
	*/
	$pseudo = htmlspecialchars($_POST['username'], ENT_QUOTES);
	$pseudo = SQLite3::escapeString($pseudo);
	$mdp = htmlspecialchars($_POST['password'], ENT_QUOTES);
	$mdp = SQLite3::escapeString($mdp);
	//$mdp = hash('sha512',$mdp); /*Nous sommes sur une sécurité en sha512 en attendant */
	if (!empty($pseudo) AND !empty($mdp)){
		$requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo=?"); //requete prepare
		$requser->execute(array($pseudo));
		//$comptes = $requser->rowCount(); //cette commande ne fonctionne pas pour sqlite3 en tout cas sur wamp :joy:
		$comptes = $requser->fetchall();
		$nbComptes = count($comptes);
		if ($nbComptes == 1){
			if ($comptes[0]['actif'] == 2){
				$erreur = "Votre compte est banni.";
			}
			elseif ($comptes[0]['actif'] == 0){
				$erreur = "Votre compte n'a pas encore été activer par un administrateur.";
			}
			else{
				if (password_verify($mdp, $comptes[0]['mdp']))
				$_SESSION['id'] = $comptes[0]['id'];
				$_SESSION['pseudo'] = $comptes[0]['pseudo'];
				if ($comptes[0]['admin'] == 1){
					$_SESSION['admin'] = 1;
					header("Location: admin/index.php");
				}
				else{
					header("Location: panel.php");
				}
         	}
		}
		else{
			/**faire la redirection vers la page d'antho */
			$requser = $bdd->prepare("SELECT * FROM membres WHERE pseudo=?");
			$requser->execute(array($pseudo));
			if (count($requser->fetchall()) == 0){
				header("Location: pageRefus.php");
			}
			else{
				$erreur = "Votre identifiant ou mot de passe n'est pas valide";
			}
		}


	}
	else{
		$erreur = "Tous les champs doivent être complétés"; //Ne pas oublier de que l'user est un chien et qu'il peut modifier son code client ;)
	}
}


 ?>
<html>
<head>
	<title>Page de connexion</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="./css/style.css" media="screen" type="text/css" />
</head>
<body>
	<div id="contenair" align="center">
    	<form action="" method="POST">
        	<h1> Connexion </h1>
            <label>
                <b> Nom d'utilisateur : </b>
            </label>
            <input type="text" placeholder="Entrer votre nom d'utilisateur" name="username" required>
            <br/>
            <label>
                <b> Mot de passe : </b>
            </label>
            <input type="password" placeholder="Entrer votre mot de passe" name="password" required>
            <br/>
            <br/>
            <input type="submit" id="validation" name="validation" value="LOGIN">
        </form>
        <?php
        	if(isset($erreur)) {
        		echo '<font color="red">'.$erreur."</font>";
        	}
        ?>
	    </div>
</body>
</html>