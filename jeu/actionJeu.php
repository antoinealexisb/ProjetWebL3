<?php
/**
 * Page qui gère le jeu en fonction des actions de l'utilisateur.
 *
 * @package       /
 */
if (!isset($_SESSION)){
	require("./../verifClient.php");
}

$secu = htmlspecialchars($_POST['securite'], ENT_QUOTES);
$secu = SQLite3::escapeString($secu);


header('Content-Type: application/json; charset=utf-8');
$bdd = new PDO("sqlite:./../projet.sqlite3");
if (isset($_POST['id_table']) && isset($_POST['id_joueur']) && isset($_POST['script']) && isset($_POST['element'])){
	$id_table = htmlspecialchars($_POST['id_table'], ENT_QUOTES);
	$id_table = SQLite3::escapeString($id_table);
	$id_joueur = htmlspecialchars($_POST['id_joueur'], ENT_QUOTES);
	$id_joueur = SQLite3::escapeString($id_joueur);
	$script = htmlspecialchars($_POST['script'], ENT_QUOTES);
	$script = SQLite3::escapeString($script);
	$element = htmlspecialchars($_POST['element'], ENT_QUOTES);
	$element = SQLite3::escapeString($element);
	$petite = array($id_table, $id_joueur, $script, $element);
	$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? AND (joueur1=? OR joueur2=?) ORDER BY id DESC LIMIT 1");
	$requser->execute(array($id_table, $id_joueur, $id_joueur));
	$requser2 = $bdd->prepare("SELECT * FROM secuJoueur WHERE idtable=?");
	$requser2->execute(array($id_table));
	$userinfo2 = $requser2->fetchall()[0];
	$userinfo = $requser->fetchall();
	$nbreq = count($userinfo);
	if ($nbreq > 0){
		$userinfo = $userinfo[0];
		if ($userinfo['finit'] != ""){
			echo '{"status":"2","error":"Partie terminée !","expected":1,"code":500}';
		}
		else if ((!isset($_SESSION['admin'])) && (($userinfo['tour']%2==0 && ($userinfo2['secuJ1'] != $secu)) || ($userinfo['tour']%2==1 && ($userinfo2['secuJ2'] != $secu)))){
			echo '{"status":"0","error":"Le code de sécurité n\'est pas valide. Veuillez recharger la page !","expected":1,"code":500}';
		}
		else if ((($userinfo['tour']%2==0)&&($userinfo['Joueur1']==$id_joueur)) || (($userinfo['tour']%2==1 )&&($userinfo['Joueur2']==$id_joueur))){
			//faire un switch pour les différentes fonctionne à prendre en fonction du script;
			//je recupere la representation du tableau en JSON.
			$json = $userinfo['plateau'];
			$obj = json_decode($json)->plateau;

			if ($userinfo['bloque'] == 1){
				switch($script){
				case "onFaceLeft": //tuile actuelle (select)
					echo onFaceLeft();
					break;
				case "onFaceRight": //tuile actuelle (select)
					echo onFaceRight();
					break;
				case "onFaceUp": //tuile actuelle (select)
					echo onFaceUp();
					break;
				case "onFaceDown":
					echo onFaceDown();
					break;
				case "onSkip":
					echo onSkip();
					break;
				default:
					echo '{"status":"1","error":"L\'action demandé n\'est pas possible vous ne pouvez que faire des rotations ou finir le tour !","expected":1,"code":971}';
					break;
				}

			}
			else{
				switch($script){
					case "recup":
						echo recup();
						break;
					case "onClickPiece" : //quand on clique sur une piece du joueur dans son tableau isolé.
						echo onClickPiece($element);
						break;
					case "onStay": //tuile actuelle (select)
						echo onStay();
						break;
					case "onPush": //tuile actuelle (select)
						echo onPush();
						break;
					case "onClickCell":
						echo onClickCell($element);
						break;
					case "onFaceLeft": //tuile actuelle (select)
						echo onFaceLeft();
						break;
					case "onFaceRight": //tuile actuelle (select)
						echo onFaceRight();
						break;
					case "onFaceUp": //tuile actuelle (select)
						echo onFaceUp();
						break;
					case "onFaceDown":
						echo onFaceDown();
						break;
					case "onSkip":
						echo onSkip();
						break;
					case "onClickArrow":
						echo onClickArrow($element);
						break;
					default:
						echo '{"status":"1","error":"L\'action demandé n\'est pas possible !","expected":1,"code":970}';
						break;
				}
			}




			//echo '{"status":"1","data":{"valid":1}}';
		} 
		else {
		echo '{"status":"0","error":"Ce n\'est pas votre tour de jeu","expected":1,"code":960}';
		}
	}
	else {
		echo '{"status":"0","error":"Votre action est suspecte ou alors un admin a supprimé votre partie","expected":1,"code":950}';
	}

} else {
	echo '{"status":"0","error":"Vous devez être dans le jeu pour faire des actions","expected":1,"code":900}';
}

/**
* Fonction qui place la tuile selectionné si possible ou non.
*/
function onClickCell($element){
	global $userinfo;
	global $obj;
	global $bdd;
	if ($element>64){
		echo '{"status":"1","error":"La cellule cliqué n\'est pas valide !","expected":1,"code":1000}';
	}
	elseif ($element >= 50){
		echo '{"status":"1","error":"Votre action est supecte !","expected":1,"code":1020}';
	}
	else{
		if ($userinfo['selectE'] != ""){
			$elementEmp = $obj[intval($element[0])][intval($element[1])][0];
			if (verifPieceEnsemble($elementEmp)){//si nous cliquons sur une de nos piece actualise la pièce selectionné
				$tmp = json_encode($obj[intval($element[0])][intval($element[1])]);
				$update = $bdd->prepare('UPDATE PartieEnCours SET selectE = ? WHERE id=?');
				$update->execute(array($tmp, $userinfo['id']));
				//update de la piece selectE
				echo '{"status":"1","data":{"valid":1,"updatePiece":1}}';
			}
			else{//si pas une pièce à nous:
				if ($elementEmp <=44){ //vérifie que la pièce cliquer est une piece de terrain
					if (verifDeplacement($elementEmp)){ //vérifie si la pièce peut être mit dans le terrain.
						$tmp = json_decode($userinfo['selectE']);
						if (!verifPieceJeu($tmp[0])){/*peut faire une rotation , faut bloquer le fait qu'il peut changer de pion */
							$obj[intval($element[0])][intval($element[1])] = json_decode($userinfo['selectE']);
							$tmp3 = json_decode($userinfo['selectE']);
							$obj[$tmp3[0][0]][$tmp3[0][1]] = ['00',0];
							$tmp = json_encode($obj);
							$tmp2='{"plateau": '.$tmp.'}';
							$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau ,tour, bloque, selectE , variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
							$insert->execute(array($userinfo['idtable'], $userinfo['Joueur1'], $userinfo['Joueur2'], $tmp2, $userinfo['tour'], 1, $userinfo['selectE'], $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
							secu();
							echo '{"status":"1","data":{"valid":1,"rotate":true,"secure":1}}';

						}
						else{ //doit pouvoir faire une rotation aussi après le placement
							$coord = PieceJeu(json_decode($userinfo['selectE'])[0]);
							$obj[intval($element[0])][intval($element[1])] = json_decode($userinfo['selectE']);
							$obj[intval($coord[0])][intval($coord[1])] = [$coord, 0];
							$tmp = json_encode($obj);
							//echo '{"plateau": '.$truc."}";
							$tmp2='{"plateau": '.$tmp.'}';
							$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, bloque, selectE, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
							$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp2, $userinfo['tour'], 1, $userinfo['selectE'], $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
							secu();
							echo '{"status":"1","data":{"valid":1,"rotate":true,"secure":1}}';

						}
					//verifDeplacement()
					//update()
					}
					else {
						echo '{"status":"1","error":"Vous ne pouvez pas faire ce mouvement !","expected":1,"code":1020}';
					}
				}
				else {
					echo '{"status":"1","error":"La pièce que vous avez selectionnez n\'est pas à vous !","expected":1,"code":1010}';
				}
			}
		}
		else{
			$elementEmp = $obj[intval($element[0])][intval($element[1])][0];
			if (verifPieceEnsemble($elementEmp)){//si pas selectE et que la piece est à joueur alors add selectE
				$tmp = json_encode($obj[intval($element[0])][intval($element[1])]);
				$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, selectE, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
				$insert->execute(array($userinfo['idtable'], $userinfo['Joueur1'], $userinfo['Joueur2'], $userinfo['plateau'], $userinfo['tour'], $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
				secu();
				echo '{"status":"1","data":{"valid":1,"secure":1}}';
			}
			else{ // si pas au joueur erreur.
				echo '{"status":"1","error":"La pièce que vous avez selectionnez n\'est pas à vous !","expected":1,"code":1010}';
			}
		}
	}
}

/**
* La fonction met la pièce cliqué par l'utilisateur dans la base de donnée pour "select".
* Si la pièce est dans le terrain ou n'existe pas, alors une erreur est retournée.
*/
function onClickPiece($element){
	global $bdd;
	global $userinfo;
	global $obj;
	if (json_encode($obj[intval($element[0])][intval($element[1])]) == $userinfo['selectE']){
		echo '{"status":"1","data":{"valid":1}}';
	}
	elseif (verifPieceEnsemble($element)){
		if (!verifPieceJeu($element)){
			$tmp = json_encode($obj[intval($element[0])][intval($element[1])]);
			$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, selectE, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
			$insert->execute(array($userinfo['idtable'], $userinfo['Joueur1'], $userinfo['Joueur2'], $userinfo['plateau'], $userinfo['tour'], $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
			secu();
			echo '{"status":"1","data":{"valid":1,"secure":1}}';
		}
		else{
			echo '{"status":"1","error":"Votre pièce est déjà dans le jeu !","expected":1,"code":990}';
		}
	}
	else{
		echo '{"status":"1","error":"La pièce de votre jeu n\'est pas valide !","expected":1,"code":980}';
	}
}

/**
* La fonction verifie si un element appartient bien l'utilisateur voulant jouer.
* //utilisation implicite de la conversion de php ;)
*/
function verifPieceEnsemble($element){
	global $userinfo;
	return (((($userinfo['tour']%2==0)&&(50<=$element) && ($element<=54)))||(($userinfo['tour']%2==1)&&((60<=$element) && ($element<=64))));
}


/**
* La fonction verifie si une piece du joueur est ou non dans le terrain.
*/
function verifPieceJeu($element){
	global $obj;
	for ($i=0; $i<5; $i++){
		for ($j=0; $j<5; $j++){
			if ($element == $obj[$i][$j][0]){
				return true;
			}
		}
	}
	return false;
}

/**
* La fonction retour les coordonnées de la piece.
*/
function PieceJeu($element){
	global $obj;
	for ($i=0; $i<5; $i++){
		for ($j=0; $j<5; $j++){
			if ($element == $obj[$i][$j][0]){
				return (strval($i).strval($j));
			}
		}
	}
	return "99";
}

/**
* La fonction verifie si une piece peut-être placer à un endroit precis.
*/
function verifDeplacement($element){
	global $userinfo;
	$tmp = json_decode($userinfo['selectE']);
	if (!verifPieceJeu($tmp[0])){//si pas encore dans le plateau
		$caseValides = array("00", "01", "02", "03", "04", "10", "14", "20", "24", "30", "34", "40", "41", "42", "43", "44");
		if ($userinfo["variante"] == 1 && $userinfo['tour'] <4){
			//echo "je fais quelque chose";
			if (($key = array_search("02", $caseValides)) !== false) {
			    unset($caseValides[$key]);
			}
			if (($key = array_search("42", $caseValides)) !== false) {
			    unset($caseValides[$key]);
			}
		}
		if (in_array($element, $caseValides)){
			return true;
		}
		return false;
	}
	else{
		//$orientation = $tmp[1];
		$tmp = PieceJeu($tmp[0]);
		$table = array();
		//nord
		if ($tmp[0]>0){
			$table[] = strval(intval($tmp[0])-1).$tmp[1];
		}
		//est
		if ($tmp[1]<4){
			$table[] = $tmp[0].strval(intval($tmp[1])+1);
		}
		//sud
		if ($tmp[0]<4){
			$table[] = strval(intval($tmp[0])+1).$tmp[1];	
		}
		//ouest
		if ($tmp[1]>0){
			$table[] = $tmp[0].strval(intval($tmp[1])-1);
		}
		if ($userinfo["variante"] == 1 && $userinfo['tour'] <4){
			//echo "je fais quelque chose";
			if (($key = array_search("02", $table)) !== false) {
			    unset($table[$key]);
			}
			if (($key = array_search("42", $table)) !== false) {
			    unset($table[$key]);
			}
		}

		return (in_array($element, $table));
		/*switch($orientation){
			case 0:
				$tmp2=strval(intval($tmp[0])-1).$tmp[1];
				break;
			case 1:
				$tmp2=$tmp[0].strval(intval($tmp[1])+1);
				break;
			case 2:
				$tmp2=strval(intval($tmp[0])+1).$tmp[1];
				break;
			default:
				$tmp2=$tmp[0].strval(intval($tmp[1])-1);
				break;
		}*/
		//return ($tmp2==$element);
	}
}

/**
* Il s'agit de la fontion de fin du tour.
*/
function onSkip(){
	global $bdd;
	global $petite;
	//$petite = array($id_table, $id_joueur, $script, $element);
	$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? AND (joueur1=? OR joueur2=?) ORDER BY id DESC LIMIT 1");
	$requser->execute(array($petite[0], $petite[1], $petite[1]));
	$userinfo = $requser->fetchall();
	$userinfo = $userinfo[0];
	$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
	$insert->execute(array($userinfo['idtable'], $userinfo['Joueur1'], $userinfo['Joueur2'], $userinfo['plateau'], ($userinfo['tour']+1), $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
	secu();
	return '{"status":"1","data":{"valid":1,"skip":1,"secure":1}}';
}

function rotation($rotation){
	global $bdd;
	global $obj;
	global $userinfo;
	if ($userinfo['selectE'] != null){
		$tmp = json_decode($userinfo['selectE']);

		if (verifPieceJeu($tmp[0])){
			if ($tmp[1] == $rotation){
				echo '{"status":"1","data":{"valid":1,"left":1}}';
			}else{
				$tmp2 = PieceJeu($tmp[0]);
				$tmp[1] = $rotation;
				$obj[intval($tmp2[0])][intval($tmp2[1])] = $tmp;
				$tmp3 = json_encode($obj);
				//echo '{"plateau": '.$truc."}";
				$tmp3='{"plateau": '.$tmp3.'}';
				$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, selectE, bloque, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
				$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour'], json_encode($tmp), 1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
				secu();
				echo '{"status":"1","data":{"valid":1,"rotate":1,"secure":1}}';
			}
		}
		else{
			echo '{"status":"1","error":"Votre pièce doit être sur le plateau !","expected":1,"code":995}';
		}
	}
	else {
		echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce !","expected":1,"code":994}';
	}
}

function onFaceLeft(){
	rotation(3);
}
function onFaceRight(){
	rotation(1);
}
function onFaceUp(){
	rotation(0);
}
function onFaceDown(){
	rotation(2);
}
function onStay(){
	onSkip();
}
function onPush(){
	global $userinfo;
	global $obj;
	global $bdd;
	if ($userinfo['selectE'] != ""){
		$tmp = json_decode($userinfo['selectE']);
		$direction = $tmp[1];
		if (verifPieceJeu($tmp[0])){
			$tmp = PieceJeu($tmp[0]);//coordonnées de la piece dans le plateau.
			//avoir les coordonnées de selectE dans le plateau
			if (peutPush($tmp, $direction)){
				//echo "Peut push";
				decallage($tmp, $direction);
			}
			else{
				echo '{"status":"1","error":"Rien à pousser !","expected":1,"code":995}';
			}
		}
		else{
			echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce dans le terrain !","expected":1,"code":995}';
		}
	}
	else{
		echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce !","expected":1,"code":994}';
	}
}

function peutPush($element, $direction, $isArrow=false){
		switch($direction){
		case 0://Nord
			return verifPushVH($element, $isArrow);
			break;
		case 1://Est
			return verifPushHD($element, $isArrow);
			break;
		case 2://Sud
			return verifPushVB($element, $isArrow);
			break;
		default:
			return verifPushHG($element, $isArrow);
			break;
	}
}

function decallage($element, $direction, $isArrow=false){
		switch($direction){
		case 0://Nord
			return decallageVH($element, $isArrow);
			break;
		case 1://Est
			return decallageHD($element, $isArrow);
			break;
		case 2://Sud
			return decallageVB($element, $isArrow);
			break;
		default:
			return decallageHG($element, $isArrow);
			break;
	}
}

function calculForce($direction, $direction2){
	$repdirection = $direction%2;
	if ($direction2%2 == $repdirection){
		if ($direction == $direction2){
			return 1;
		}
		else {
			return -1;
		}
	}
	else{
		return 0;
	}
}

function verifPushHD($element, $isArrow=false){
	global $obj;
	$force = 1;
	$rocher = 0;
	if ($isArrow){
		//echo "Salut".$element[0].$element[1]."  ";
		$element[0] = $element[1];
		$element[1] = 0;
	}
	else{
		if ($element[1]==4){
			return false;
		}
		$element[1] = $element[1]+1;
	}
	if ($element == $obj[$element[0]][$element[1]][0]){
		//echo $obj[$element[0]][$element[1]][0];
		//echo "Hello";
		return false;
	}
	else{
		while ($element[1]<5){
			if ($element == $obj[$element[0]][$element[1]][0]){
				break;
			}
			else if ($obj[$element[0]][$element[1]][0] >= 70){
				$rocher = $rocher + 1;
				$element[1] = $element[1]+1;
			}
			else if ($obj[$element[0]][$element[1]][0] <=44){
				break;
			}
			else{
				$force = $force + calculForce(1, $obj[$element[0]][$element[1]][1]);
				if ($force <= 0){
					return false;
				}
				else{
					$element[1] = $element[1]+1;
				}
			}
		}
		if ($force >= $rocher){
			return true;
		}
		else{
			return false;
		}
	}
}

function verifPushHG($element, $isArrow=false){
	global $obj;
	$force = 1;
	$rocher = 0;
	if ($isArrow){
		$saveElement = 4;
		$element[0] = $element[1];
		$element[1] = 4;
	}
	else{
		$saveElement = intval($element[1]);
		if ($saveElement==0){
			return false;
		}
		$saveElement = $saveElement-1;
		$element[1] = $element[1]-1;
	}
	if ($element == $obj[$element[0]][$element[1]][0]){
		return false;
	}
	else{
		while ($saveElement>=0){
			if ($element == $obj[$element[0]][$element[1]][0]){
				break;
			}
			else if ($obj[$element[0]][$element[1]][0] >= 70){
				$rocher = $rocher + 1;
				$saveElement = $saveElement-1;
				$element[1] = $element[1]-1;
			}
			else if ($obj[$element[0]][$element[1]][0] <=44){
				break;
			}
			else{
				$force = $force + calculForce(3, $obj[$element[0]][$element[1]][1]);
				if ($force <= 0){
					return false;
				}
				else{
					$saveElement = $saveElement-1;
					$element[1] = $element[1]-1;
				}
			}
		}
		if ($force >= $rocher){
			return true;
		}
		else{
			return false;
		}
	}
}

function verifPushVH($element, $isArrow=false){
	global $obj;
	$force = 1;
	$rocher = 0;
	$saveElement = intval($element[0]);
	if ($isArrow){
		$saveElement = 4;
		$element[0] = 4;
		$element[1] = $element[1];
	}
	else{
		if ($saveElement==0){
			return false;
		}
		$saveElement = $saveElement-1;
		$element[0] = $element[0]-1;
	}
	/*$saveElement = $saveElement-1;
	$element[0] = $element[0]-1;*/
	//echo $element[0].$element[1];
	if ($element == $obj[$element[0]][$element[1]][0]){
		return false;
	}
	else{
		while ($saveElement>=0){
			if ($element == $obj[$element[0]][$element[1]][0]){
				break;
			}
			else if ($obj[$element[0]][$element[1]][0] >= 70){
				$rocher = $rocher + 1;
				$saveElement = $saveElement-1;
				$element[0] = $element[0]-1;
			}
			else if ($obj[$element[0]][$element[1]][0] <=44){
				break;
			}
			else{
				$force = $force + calculForce(0, $obj[$element[0]][$element[1]][1]);
				if ($force <= 0){
					return false;
				}
				else{
					$saveElement = $saveElement-1;
					$element[0] = $element[0]-1;
				}
			}
		}
		if ($force >= $rocher){
			return true;
		}
		else{
			return false;
		}
	}


}

function verifPushVB($element, $isArrow=false){
	global $obj;
	$force = 1;
	$rocher = 0;
	if ($element[0]==4){
		return false;
	}
	if ($isArrow){
		$element[0] = 0;
		$element[1] = $element[1];
	}
	else{
		$element[0] = $element[0]+1;
	}
	if ($element == $obj[$element[0]][$element[1]][0]){
		return false;
	}
	else{
		while ($element[0]<5){
			if ($element == $obj[$element[0]][$element[1]][0]){
				break;
			}
			else if ($obj[$element[0]][$element[1]][0] >= 70){
				$rocher = $rocher + 1;
				$element[0] = $element[0]+1;
			}
			else if ($obj[$element[0]][$element[1]][0] <=44){
				break;
			}
			else{
				$force = $force + calculForce(2, $obj[$element[0]][$element[1]][1]);
				if ($force <= 0){
					return false;
				}
				else{
					$element[0] = $element[0]+1;
				}
			}
		}
		if ($force >= $rocher){
			return true;
		}
		else{
			return false;
		}
	}
}
////
function decallageHD($element, $isArrow=false){
	global $obj;
	global $bdd;
	global $userinfo;
	if ($isArrow){
		$tmp = json_decode($userinfo['selectE']);
		$saveTuile = $tmp;
		$obj[$tmp[0][0]][$tmp[0][1]] = ["00",-1];
		$element[0]=$element[1];
		$element[1]=0;
	}
	else{
		$saveTuile= $obj[$element[0]][$element[1]];
		$obj[$element[0]][$element[1]] = [$element, 0];
	}
	while (((!$saveTuile[0]<=44) && ($element[1]<5)) || (($isArrow) && ($element[1]<=5) )){
		if ((!$isArrow) && $element[1]==4){
			break;
		}
		else if ($isArrow && $element[1]==5){
			break;
		}
		else{
			if ($saveTuile[0] >=50){

				$tmpsave = $saveTuile;
				if ($isArrow){
					$saveTuile = $obj[$element[0]][$element[1]];
					$obj[$element[0]][$element[1]] = $tmpsave;
				}
				else{
					$saveTuile = $obj[$element[0]][$element[1]+1];
					$obj[$element[0]][$element[1]+1] = $tmpsave;
				}
				$element[1]=$element[1]+1;

				/*$obj[$element[0]][$element[1]+1] = $obj[$element[0]][$element[1]];
				$obj[$element[0]][$element[1]] = [$element, 0];
				$element[1]=$element[1]+1;*/
			}
			else{
				break;
			}
		}
	}
	if ($saveTuile[0]>=70){
		$tmp = partieTerminee($element[0].$element[1],1);
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, finit, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ? ,?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"finit":1,"secure":1}}';
	}
	else if($saveTuile[0]>=50){
		//mettre la piece dans la main fait pour
		$obj[$saveTuile[0][0]][$saveTuile[0][1]]=[$saveTuile[0],0];
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
	else{
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
}

function decallageHG($element, $isArrow=false){
	global $obj;
	global $bdd;
	global $userinfo;
	if ($isArrow){
		$tmp = json_decode($userinfo['selectE']);
		$saveTuile = $tmp;
		$obj[$tmp[0][0]][$tmp[0][1]] = ["00",-1];
		$element[0]=$element[1];
		$nb=4;
		$element[1]=4;
	}
	else{
		$saveTuile= $obj[$element[0]][$element[1]];
		$obj[$element[0]][$element[1]] = [$element, 0];
		$nb=$element[1];
	}
	while ((!$saveTuile[0]<=44) && ($element[1]>=0) ){
		if ((!$isArrow) && $nb==0){
			break;
		}
		else if ($isArrow && $nb==(-1)){
			$nb++;
			break;
		}
		else{
			/*if ($saveTuile[0] >=50){
				
				$tmpsave = $saveTuile;
				$saveTuile = $obj[$element[0]][$element[1]-1];
				$obj[$element[0]][$element[1]-1] = $tmpsave;
				$element[1]=$element[1]-1;*/

			if ($saveTuile[0] >=50){
				$tmpsave = $saveTuile;
				if ($isArrow){
					$saveTuile = $obj[$element[0]][$nb];
					$obj[$element[0]][$nb] = $tmpsave;
					//$obj[$element[0]][$element[1]] = $tmpsave;
				}
				else{
					$saveTuile = $obj[$element[0]][$nb-1];
					$obj[$element[0]][$nb-1] = $tmpsave;
				}
				$nb=$nb-1;
				$element[1]=$element[1]-1;
				/*$obj[$element[0]][$element[1]+1] = $obj[$element[0]][$element[1]];
				$obj[$element[0]][$element[1]] = [$element, 0];
				$element[1]=$element[1]+1;*/
			}
			else{
				break;
			}
		}
	}
	if ($saveTuile[0]>=70){//si rocher degage
		//$tmp2=PieceJeu($obj[$element[0]][$element]);
		//coordonnée de la piece 
		$tmp = partieTerminee($element[0].$element[1],3);
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, finit, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"finit":1,"secure":1}}';
	}
	else if($saveTuile[0]>=50){
		//mettre la piece dans la main fait pour
		$obj[$saveTuile[0][0]][$saveTuile[0][1]]=[$saveTuile[0],0];
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
	else{
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}	
}

function decallageVH($element, $isArrow=false){
	global $obj;
	global $bdd;
	global $userinfo;
	if ($isArrow){
		$tmp = json_decode($userinfo['selectE']);
		$saveTuile = $tmp;
		$obj[$tmp[0][0]][$tmp[0][1]] = ["00",-1];
		$element[0]=4;
		$nb = 4;
		$element[1]=$element[1];
	}
	else{
		$saveTuile= $obj[$element[0]][$element[1]];
		$obj[$element[0]][$element[1]] = [$element, 0];
		$nb=$element[0];
	}
	while ((!$saveTuile[0]<=44) && ($nb>=0) ){
		if ((!$isArrow) && $nb==0){
			break;
		}
		else if ($isArrow && $nb==(-1)){
			$nb++;
			break;
		}
		else{
			if ($saveTuile[0] >=50){
				//echo "je suis ici aussi";
				$tmpsave = $saveTuile;
				if ($isArrow){
					//echo "je suis la\n ";
					$saveTuile = $obj[$nb][$element[1]];
					$obj[$nb][$element[1]] = $tmpsave;
				}
				else{
					$saveTuile = $obj[$nb-1][$element[1]];
					$obj[$nb-1][$element[1]] = $tmpsave;
				}
				$nb=$nb-1;
				$element[0]=$element[0]-1;

				/*$tmpsave = $saveTuile;
				if ($isArrow){
					$saveTuile = $obj[$element[0]][$element[1]];
					$obj[$element[0]][$element[1]+1] = $tmpsave;
					//$obj[$element[0]][$element[1]] = $tmpsave;
				}
				else{
					$saveTuile = $obj[$element[0]][$element[1]-1];
					$obj[$element[0]][$element[1]-1] = $tmpsave;
				}
				$element[1]=$element[1]-1;*/


				/*$obj[$element[0]][$element[1]+1] = $obj[$element[0]][$element[1]];
				$obj[$element[0]][$element[1]] = [$element, 0];
				$element[1]=$element[1]+1;*/
			}
			else{
				break;
			}
		}
	}
	if ($saveTuile[0]>=70){
		$tmp = partieTerminee($element[0].$element[1],0);
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, finit, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"finit":1,"secure":1}}';
	}
	else if($saveTuile[0]>=50){
		//mettre la piece dans la main fait pour
		$obj[$saveTuile[0][0]][$saveTuile[0][1]]=[$saveTuile[0],0];
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		//echo $tmp3;
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
	else{
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
	
}

function decallageVB($element, $isArrow=false){
	global $obj;
	global $bdd;
	global $userinfo;
	if ($isArrow){
		$tmp = json_decode($userinfo['selectE']);
		$saveTuile = $tmp;
		$obj[$tmp[0][0]][$tmp[0][1]] = ["00",-1];
		$element[0]=0;
		$element[1]=$element[1];
	}
	else{
		$saveTuile= $obj[$element[0]][$element[1]];
		$obj[$element[0]][$element[1]] = [$element, 0];
	}
	while ((($saveTuile[0]>=50) && ($element[0]<5)) || (($isArrow) && ($element[0]<=5) )){
		if ((!$isArrow) && $element[0]==4 ){
			break;
		}
		else if ($isArrow && $element[0]==5){
			break;
		}
		else{
			if ($saveTuile[0] >=50){
				$tmpsave = $saveTuile;
				if ($isArrow){
					$saveTuile = $obj[$element[0]][$element[1]];
					$obj[$element[0]][$element[1]] = $tmpsave;
				}
				else{
					$saveTuile = $obj[$element[0]+1][$element[1]];
					$obj[$element[0]+1][$element[1]] = $tmpsave;
				}
				$element[0]=$element[0]+1;

				/*$obj[$element[0]][$element[1]+1] = $obj[$element[0]][$element[1]];
				$obj[$element[0]][$element[1]] = [$element, 0];
				$element[1]=$element[1]+1;*/
			}
			else{
				break;
			}
		}
	}
	if ($saveTuile[0]>=70){
		$tmp = partieTerminee($element[0].$element[1],2);
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, finit, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $tmp, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"finit":1,"secure":1}}';
	}
	else if($saveTuile[0]>=50){
		//mettre la piece dans la main fait pour
		$obj[$saveTuile[0][0]][$saveTuile[0][1]]=[$saveTuile[0],0];
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":12,,"secure":1}}';
	}
	else{
		$tmp3=json_encode($obj);
		$tmp3='{"plateau": '.$tmp3.'}';
		$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
		secu();
		echo '{"status":"1","data":{"valid":1,"decallage":1,"secure":1}}';
	}
}

function partieTerminee($element, $direction){
	global $userinfo;
	global $obj;
	global $bdd;
	//echo $obj[$element[0]][$element[1]][0];
	if ($obj[$element[0]][$element[1]][0] >= 70){
		switch ($direction){
			case 0:
				$tmp = strval(intval($element[0])+1).$element[1];
				return partieTerminee($tmp, $direction);
				break;
			case 1:
				$tmp = $element[0].strval(intval($element[1])-1);
				return partieTerminee($tmp, $direction);
				break;
			case 2:
				$tmp = strval(intval($element[0])-1).$element[1];
				return partieTerminee($tmp, $direction);
				break;
			default:
				$tmp = $element[0].strval(intval($element[1])+1);
				return partieTerminee($tmp, $direction);
				break;
		}
	}
	elseif ($obj[$element[0]][$element[1]][0] >= 60){
		$bdMembres = $bdd->prepare("UPDATE membres SET Score = Score+1, nbWin = nbWin + 1 WHERE id=?");
		$bdMembres->execute(array($userinfo['Joueur2']));
		$bdMembres = $bdd->prepare("UPDATE membres SET Score = Score-1, nbLose = nbLose + 1 WHERE id=?");
		$bdMembres->execute(array($userinfo['Joueur1']));
		$bdavJeu = $bdd->prepare("UPDATE avJeu SET actif = 2 WHERE idtable=?");
		$bdavJeu->execute(array($userinfo['idtable']));
		return $userinfo['Joueur2'];
	}
	else{
		$bdMembres = $bdd->prepare("UPDATE membres SET Score = Score+1, nbWin = nbWin + 1 WHERE id=?");
		$bdMembres->execute(array($userinfo['Joueur1']));
		$bdMembres = $bdd->prepare("UPDATE membres SET Score = Score-1, nbLose = nbLose + 1 WHERE id=?");
		$bdMembres->execute(array($userinfo['Joueur2']));
		$bdavJeu = $bdd->prepare("UPDATE avJeu SET actif = 2 WHERE idtable=?");
		$bdavJeu->execute(array($userinfo['idtable']));
		return $userinfo['Joueur1'];
	}
}

function onClickArrow($element){
	global $userinfo;
	global $obj;
	global $bdd;
	if ($userinfo['selectE'] != ""){
		$tmp = json_decode($userinfo['selectE']);
		$direction = $element[0];
		$tmp[1] = $direction;
		$userinfo['selectE'] = json_encode($tmp);
		if (!verifPieceJeu($tmp[0])){
			//$tmp = PieceJeu($tmp[0]);//coordonnées de la piece dans le plateau.
			//avoir les coordonnées de selectE dans le plateau
			if (peutPush($element, $direction, true)){
				//echo "Peut push";
				decallage($element, $direction, true);
			}
			else{
				echo '{"status":"1","error":"Rien à pousser !","expected":1,"code":995}';
			}
		}
		else{
			echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce qui n\'est pas dans le terrain !","expected":1,"code":995}';
		}
	}
	else{
		echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce !","expected":1,"code":994}';
	}
}


function estDedans($element){
	$caseValides = array("00", "01", "02", "03", "04", "10", "14", "20", "24", "30", "34", "40", "41", "42", "43", "44");
	if (in_array($element, $caseValides)){
		return true;
	}
	return false;
}

function recup(){
	global $userinfo;
	global $obj;
	global $bdd;
	if ($userinfo['selectE'] != ""){
		$tmp = json_decode($userinfo['selectE']);
		if (verifPieceJeu($tmp[0])){
			$coord = PieceJeu($tmp[0]);
			if (estDedans($coord)){
				if ($userinfo['variante'] == 1)
				{
					(($userinfo['tour']%2 == 0) &&($userinfo['nbJ1'] <1)) ? $userinfo['nbJ1'] =+1 : ((($userinfo['tour']%2 == 1) &&($userinfo['nbJ2'] <1)) ? $userinfo['nbJ1'] =+2 : ($erreur=1));
					if(isset($erreur)){
						echo '{"status":"1","error":"Vous avez atteint le nombre possible de retour de piece !","expected":1,"code":997}';
						return;
					}
				}
				$obj[$coord[0]][$coord[1]] = [$coord,-1];
				$obj[$tmp[0][0]][$tmp[0][1]] = [$tmp[0],0];
				$tmp3=json_encode($obj);
				$tmp3='{"plateau": '.$tmp3.'}';
				$insert = $bdd->prepare('INSERT INTO PartieEnCours (idtable, Joueur1, Joueur2, plateau, tour, variante, nbJ1, nbJ2) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
				$insert->execute(array($userinfo['idtable'],$userinfo['Joueur1'],$userinfo['Joueur2'],$tmp3, $userinfo['tour']+1, $userinfo['variante'], $userinfo['nbJ1'], $userinfo['nbJ2']));
				secu();
				echo '{"status":"1","data":{"valid":1,"recup":1,"secure":1}}';

			}
			else{
				echo '{"status":"1","error":"Cette pièce n\'est pas dans la bonne zone !","expected":1,"code":996}';
			}
		}
		else{
			echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce qui est dans le terrain dans la bonne zone !","expected":1,"code":995}';
		}
	}
	else{
		echo '{"status":"1","error":"Vous devez avoir selectionnez une pièce !","expected":1,"code":994}';
	}
}

function secu(){
	global $userinfo;
	global $bdd;
	$hello = $bdd->prepare("SELECT * FROM secuJoueur WHERE idtable=? LIMIT 1");
	$hello->execute(array($userinfo['idtable']));
	$helloinfo = $hello->fetchall();
	$helloinfo = $helloinfo[0];
	$securite = array();
	if ($userinfo['tour']%2==0){
		$securite[] = $helloinfo['secuJ1']+1;
		$securite[] = $helloinfo['secuJ2'];
	}
	else{
		$securite[] = $helloinfo['secuJ1'];
		$securite[] = $helloinfo['secuJ2']+1;
	}
	$hello = $bdd->prepare("UPDATE secuJoueur SET secuJ1=?, secuJ2=? WHERE idtable=?");
	$hello->execute(array($securite[0], $securite[1], $userinfo['idtable']));
}

?>