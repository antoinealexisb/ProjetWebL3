<!DOCTYPE html>
<?php require('verif.php'); ?>
<?php
/**
 * Page PHP qui permet de validé les demandes de comptes utilisateurs ou de supprimer ces demandes.
 *
 * @package       /Admin/
 */
?>
<html>
<head>
	<title>Panel Admin | Demande d'ajout</title>
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
	<h1> Visualisation des demandes d'ajouts de joueur </h1>
	<br>
<div class="row"><div class="col-sm-12" >
	<table class="table table-bordered dataTable no-footer" id="tableParties">

    </table>

</div>
</div>
</div>
</div>

	<script>
	$('#tableParties').load('requetes/reqDemandes.php');
	$(document).ready(function(){
		setInterval(function(){
			$('#tableParties').load('requetes/reqDemandes.php');
		}, 1000);
	});

	function rejoindre(id){
		$.post(
			'requetes/reqDemandes.php',
			{
				ajouter:id
			});
	}

	function supp(id){
		$.post(
			'requetes/reqDemandes.php',
			{
				supp:id
			});
	}
	</script>

</body>	
</html>