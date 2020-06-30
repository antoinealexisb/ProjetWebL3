<!DOCTYPE html>
<?php require('verifClient.php'); ?>
<?php
/**
 * Page permettant à l'utilisateur de creer une partie.
 * Il peut choisir d'être un rhinocéros ou elephant et un jeu avec ou sans variante.
 *
 * @package       /
 */
?>
<html>
<head>
	<title>Rejoindre une partie</title>
</head>
<body>
<?php require('css.php');
require('header.php');?>


<?php require('./requetes/creation.php'); ?>
	<br>
	<div id="contenair" align="center">
    	<form action="" method="POST">
          <h1> Creation d'une partie </h1>
		  <p>Veuillez choisir votre type de pièce :</p>
		  <div>
		    <input type="radio" id="piece"
		     name="piece" value="0" checked="">
		    <label for="elephant">Elephant</label>

		    <input type="radio" id="piece"
		     name="piece" value="1">
		    <label for="rhinoceros">Rhinocéros</label>
		  </div>
		  <br>
		  <p>Veuillez choisir votre type de partie :</p>
		  <div>
		    <input type="radio" id="variante"
		     name="variante" value="0" checked="">
		    <label for="elephant">Sans variante</label>

		    <input type="radio" id="variante"
		     name="variante" value="1">
		    <label for="rhinoceros">Avec variante</label>
		  </div>
		  <div>
		    <input type="submit" id="validation" name="validation" value="Envoyer !">
		  </div>
        </form>
        <?php
        	if(isset($erreur)) {
        		echo '<font color="red">'.$erreur."</font>";
        	}
        ?>
	</div>
	<?php require('footer.php');?>
</body>
</html>