<!DOCTYPE html>
<?php require('verif.php'); ?>
<?php
/**
 * Page pour creer une partie.
 *
 * @package       /Admin/
 */
?>
<html>
<head>
	<title>Panel Admin | Creer une partie</title>
	<script src="./jquery.min.js"></script>
	<?php require('css.php'); ?>
</head>
	<div class="wrapper">
<?php require('header.php');
require('droit.php');
require('./requetes/creation.php'); ?>
<div class="content-wrapper" style="min-height: 465px;">
	<h1> Bonjour Administrateur </h1>
	<br>
	<h1> Création d'une partie </h1>
	<br>
	<div id="contenair" align="center">
    	<form action="" method="POST">
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
</div>
</div>
	
</body>	
</html>