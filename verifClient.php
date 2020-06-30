<?php
/**
 * Page qui vérifie si un utilisateur est bien connecté.
 * Si ce n'est pas le cas il passe par la page deconnexion pour enlever les données de session.
 *
 * @package       /
 */
	session_start();
	if (!isset($_SESSION['id'])){
		header("Location: ./deco.php");
	}
?>