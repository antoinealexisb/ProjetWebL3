<?php
session_start();
if (isset($_SESSION['admin'])){
	if ($_SESSION['admin'] != 1){
		header("Location: ./../deco.php");
	}
}
else{
	header("Location: ./../deco.php");
}
?>