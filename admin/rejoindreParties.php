<!DOCTYPE html>
<?php require('verif.php'); ?>
<?php
/**
 * Page PHP qui permet à l'admin de rejoindre les parties.
 *
 * @package       /Admin/
 */

?>
<html>
<head>
	<title>Panel Admin | Rejoindre partie</title>
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
	<h1> Visualisation des parties en attentes d'un joueur</h1>
	<br>
<div class="row"><div class="col-sm-12" >
	<table class="table table-bordered dataTable no-footer" id="tableParties">

    </table>

</div>
</div>
</div>
</div>
	<script>
	$('#tableParties').load('requetes/reqpartiesRejoindre.php');
	$(document).ready(function(){
		setInterval(function(){
			$('#tableParties').load('requetes/reqpartiesRejoindre.php');
		}, 1000);
	});

	function rejoindre(id, idtable){
		var form = $('<form action="./requetes/reqRejoindre.php" method="post" hidden="true">' + '<input type="password" name="id" value="'+id+'" />' + '<input type="password" name="idtable" value="'+idtable+'" />' + '</form>');
		$('body').append(form);
		form.submit();
	};

	</script>

</body>	
</html>