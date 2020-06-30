<!DOCTYPE html>
<?php
/**
 * Page PHP qui affiche les parties en attentes de joueurs.
 * Même les parties òu se trouve le joueurs sont affichées. (mais celui-ci ne pourra rejoindre la partie).
 * @package       /
 */
?>
<?php require('verifClient.php'); ?>
<html>
<head>
	<title>Rejoindre une partie</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
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
	$('#tableParties').load('./requetes/reqpartiesRejoindre.php');
	$(document).ready(function(){
		setInterval(function(){
			$('#tableParties').load('./requetes/reqpartiesRejoindre.php');
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