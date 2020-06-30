<!DOCTYPE xhtml>
<?php
if(!isset($_SESSION)) {
	session_start();
	if (isset($_SESSION['id'])){
		header("Location: panel.php");
	}
}


if (isset($_POST['validation'])){
    $bdd = new PDO("sqlite:./projet.sqlite3");
    $pseudo = htmlspecialchars($_POST['username'], ENT_QUOTES);
    $pseudo = SQLite3::escapeString($pseudo);
    $mdp = htmlspecialchars($_POST['password'], ENT_QUOTES);
    $mdp = SQLite3::escapeString($mdp);
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
    $email = SQLite3::escapeString($email);
    if (!empty($pseudo) AND !empty($mdp)){
        //$mdp = hash('sha512',$mdp);  //update
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
            $requser->execute(array($pseudo, $mdp, $email, 0, 0));
            $erreur = "Demande envoyée !";
        }


    }
    else{
        $erreur = "Tous les champs doivent être complétés"; //Ne pas oublier de que l'user est un chien et qu'il peut modifier son code client ;)
    }
}



?>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="./css/styleRefus.css" media="screen" type="text/css" />
        <title> ACCES DENIED </title>
    </head>
    <body>
        <div id=contenair>
        <form method="POST" action="">
            <h1> Acces Denied </h1>
            <p> Vous n'etes pas dans la base de donne.</p>
            <p> Pour vous enregistrez remplisser le formulaire ci-dessous un administrateur vous rajoutera sous X jours </p>
            <br>
            <label>
                Nom d'utilisateur
            </label>
            <input type="text" placeholder="Entrer votre nom d'utilisateur" name="username" required>

            <br/>

            <label>
                 Mot de passe :
            </label>
            <input type="password" placeholder="Entrer votre mot de passe" name="password" required>

            <br/>
            <label>
                Adresse Mail :
            </label>
            <input type="email" placeholder="Entre votre adresse mail" name="email" required>
            <br/>

           <input type="submit" id="validation" name="validation" value="Envoyer !">
        </form>
        <?php
            if(isset($erreur)) {
                echo '<font color="red">'.$erreur."</font>";
            }
        ?>
        </div>
    </body>
</html>
