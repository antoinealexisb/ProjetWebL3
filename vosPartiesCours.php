<!DOCTYPE html>
<?php require('verifClient.php'); ?>
<?php
/**
 * Page qui permet de visualiser la liste des parties que cet utilisateur a en cours.
 * pour chaque partie, mettre en évidence le joueur dont c'est le tour.
 *
 * @package       /
 */
?>
<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
	<title>Vous parties en cours</title>
	<?php require('css.php');?>
</head>
<body>
	<?php require('header.php');?>
	<div style="padding: 30px;"></div>
	<div class="wrapper">
<div class="content-wrapper" style="min-height: 465px;">
	<div class="row">
		<div class="col-sm-12" >
		<table class="table table-bordered dataTable no-footer" id="tableParties">
    	</table>
		</div>
	</div>
</div>
</div>

	<script>
	$('#tableParties').load('./requetes/reqVosParties.php');
	$(document).ready(function(){
		setInterval(function(){
			$('#tableParties').load('./requetes/reqVosParties.php');
		}, 1000);
	});

	function jouer(idtable){
		var form = $('<form action="./jeu/plateau.php" method="post" hidden="true">' + '<input type="password" name="idtable" value="'+idtable+'" />' + '</form>');
		$('body').append(form);
		form.submit();
	};
	</script>
</body>
</html>