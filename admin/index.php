<!DOCTYPE html>
<?php require('verif.php'); ?>
<?php
/**
 * Index de l'administrateur.
 *
 * @package       /Admin/
 */
$bdd = $bdd = new PDO("sqlite:./../projet.sqlite3");
$allGame = $bdd->query("SELECT COUNT(*) FROM membres WHERE actif=1");
$tmp = $allGame->fetch()[0];
$allGame = $bdd->query("SELECT COUNT(*) FROM membres WHERE actif=0");
$tmp1 = $allGame->fetch()[0];
$allGame = $bdd->query("SELECT COUNT(*) FROM membres WHERE actif=2");
$tmp2 = $allGame->fetch()[0];
?>
<html>
<head>
	<title>Panel Admin | accueil</title>
	<script src="./js/jquery.min.js"></script>
	<script src="monjs.js"></script>
	<?php require('css.php'); ?>
</head>
<body>
	<div class="wrapper">
<?php require('header.php');
require('droit.php');?>

<div class="content-wrapper" style="min-height: 465px;">
	<div style="padding: 15px;"></div>
	<section class="content-header">
		<h1> Panel Administrateur <small>Version 2.0</small></h1>
		<ol class="breadcrumb">
			<li><a href="./index.php"><i class="fa fa-user-secret"></i> Accueil</a></li> <!--https://fontawesome.com/v4.7.0/icons/-->
			<li class="active">Panel Administrateur</li>
		</ol>
	</section>
	<section class="content">
		<div>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div>
					<div class="inner">
						<font size="5px"><?= $tmp ?></font> <i class="fa fa-user"></i> <span>Utilisateurs enregistrés</span>
					</div>
				</div>
    		</div>
    		<div class="col-md-3 col-sm-6 col-xs-12">
    			<div class="small-box bg-red">
    				<div class="inner">
    					<font size="5px"><?= $tmp1 ?></font> <i class="fa fa-user"></i> <span>Demande d'utilisateur en attente</span>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12">
    			<div class="small-box bg-red">
    				<div class="inner">
    					<font size="5px"><?= $tmp2 ?></font> <i class="fa fa-user"></i> <span>Utilisateurs bannis</span>
					</div>
				</div>
			</div>
		</div>
	</section>
	</div>
	</div>

</body>	
</html>