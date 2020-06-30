<!DOCTYPE html>
<?php require('verif.php'); ?>
<html>
<head>
	<title>Panel Admin | Partie en cours</title>
	<script src="./jquery.min.js"></script>
	<?php require('css.php'); ?>
</head>
<body>
	<div class="wrapper">
<?php require('header.php');
require('droit.php');?>
<div class="content-wrapper" style="min-height: 465px;">
	<h1> Bonjour Administrateur </h1>
	<br>
	<h1> Visualisation de vos parties en cours</h1>
	<br>
<div class="row"><div class="col-sm-12" >
	<table class="table table-bordered dataTable no-footer" id="tableParties">

    </table>

</div>
</div>
</div>
</div>
	<script>
	$('#tableParties').load('requetes/reqVosParties.php');
	$(document).ready(function(){
		setInterval(function(){
			$('#tableParties').load('requetes/reqVosParties.php');
		}, 1000);
	});

	function jouer(idtable){
		var form = $('<form action="./../jeu/plateau.php" method="post" hidden="true">' + '<input type="password" name="idtable" value="'+idtable+'" />' + '</form>');
		$('body').append(form);
		form.submit();
	};
	</script>

</body>	
</html>