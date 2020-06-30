<?php
/**
* Ceci est une page qui ne renvoie que des informations concernant la partie.
* Les méthodes pevent être en get alors, car aucune données sensible.
* La page renvoie : Le joueur qui joue, derniere piece joué, pièce qui est possible de select ou chemin en évidence pour les pièces, felches et si pièce selectionnée.
*/
header('Content-Type: application/json; charset=utf-8');
$bdd = new PDO("sqlite:./../projet.sqlite3");
$voila= array();
$TAB = array("00","01","02","03","04","10","14","20","24","30","34","40","41","42","43","44");
if (isset($_POST['id_table'])){
	$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? ORDER BY id DESC LIMIT 1");
	$requser->execute(array($_POST['id_table']));
	$userinfo = $requser->fetchall();
	$nbreq = count($userinfo);
	if ($nbreq > 0){
		$userinfo = $userinfo[0];
		$json = $userinfo['plateau'];
		$obj = json_decode($json)->plateau;
		$plat = json_encode($obj);
		$qui = quiJoue();
		$selection = array();
		$sortie = "";
		$piece = "";
		if ($userinfo['selectE'] == null){
			if ($userinfo['Joueur1'] == $qui){
				for ($i=50; $i<55; $i++){
					if (PieceJeu(strval($i)) == "99"){
						$selection[] = strval($i);
					}
					else{
						$selection[] = PieceJeu(strval($i));
					}
				}
			}
			else{
				for ($i=60; $i<65; $i++){
					if (PieceJeu(strval($i)) == "99"){
						$selection[] = strval($i);
					}
					else{
						$selection[] = PieceJeu(strval($i));
					}
				}
			}

		}
		else{
			$tmp = json_decode($userinfo['selectE']);
			$piece = PieceJeu($tmp[0]);
			if ($piece == "99"){
				$piece = $tmp[0];
			}
			if ($userinfo['bloque']!= 1 && PieceJeu($tmp[0]) == "99"){
				for ($i=0; $i<16; $i++){
					if (PieceJeu($TAB[$i]) != "99"){
						if ($TAB[$i] == "02" || $TAB[$i] == "42"){
							if ($userinfo["variante"] != 1 || $userinfo['tour'] >=4){
								$selection[] = $TAB[$i];
							}
						}
						else{
							$selection[] = $TAB[$i];
						}
					}
				}
			}
			else{
				if ($userinfo['bloque']!= 1 && peutAvancer()){
					foreach($voila as $value){
						$selection[] = $value;
					}
					//$selection[] = $voila;
				}
			}
			$fleches = fleches($tmp[0]);
			$fleches = json_encode($fleches);
		}
		if ($userinfo['tour'] > 0){
			//SELECT * from PartieEnCours where tour=0 and selectE is not NULL ORDER BY id DESC LIMIT 1
			$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? AND tour=? AND selectE is not NULL ORDER BY id DESC LIMIT 1");
			//$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? AND tour=? ORDER BY id DESC LIMIT 1");
			$requser->execute(array($_POST['id_table'], $userinfo['tour']-1));
			$userinfo = $requser->fetch();
			$derniere = json_decode($userinfo['selectE']);
			$derniere = PieceJeu(strval($derniere[0]));

		}
		$selection = json_encode($selection);
		$sortie='{"plateau": '.$plat.',"joueur":'.$qui.',"selection":'.$selection.',"piece":"'.$piece.'"';
		if (isset($derniere)){
			$sortie = $sortie.',"derniere":"'.$derniere.'"';
		}
		if ((isset($fleches)) && $fleches != ""){
			$sortie = $sortie.',"fleches":'.$fleches;
		}
		$requser = $bdd->prepare("SELECT * FROM PartieEnCours WHERE idtable=? ORDER BY id DESC LIMIT 1");
		$requser->execute(array($_POST['id_table']));
		$userinfo = $requser->fetchall();
		$userinfo = $userinfo[0];
		if ($userinfo['finit'] > 0){
			$sortie = $sortie.',"finit":'.$userinfo['finit'];
		}
		$sortie = $sortie.'}';
		echo $sortie;
	}
	else{
		echo '{"status":"0","error":"C\'est etrange cette erreur :D ","expected":1,"code":740}';
	}

}
else{
	echo '{"status":"0","error":"Mettez au moins une id de table valide ;)","expected":1,"code":750}';
}
/*echo '{"plateau":  [[["00", -1], ["01", -1], ["02", -1], ["03", -1], ["04", -1]],[["10", -1], ["11", -1], ["12", -1], ["13", -1], ["14", -1]],[["20", -1], ["21", -1], ["22", -1], ["23", -1], ["24", -1]],[["30", -1],["31", -1], ["32", -1], ["33", -1], ["34", -1]],[["40", -1], ["41", -1], ["42", -1], ["43", -1], ["44", -1]],[["50", -1], ["51", -1], ["52", -1], ["53", -1], ["54", -1]],[["60", -1], ["61", -1], ["62", -1], ["63", -1], ["64", -1]]],"joueur":"12"}';
*/

function quiJoue(){
	global $userinfo;
	if ($userinfo['tour']%2==0){
		return $userinfo['Joueur1'];
	}
	else{
		return $userinfo['Joueur2'];
	}
}

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

function peutAvancer(){
	global $userinfo;
	global $voila;
	$tmp = json_decode($userinfo['selectE']);
	$orientation = $tmp[1];
	$tmp = PieceJeu($tmp[0]);
	//nord
	if ($tmp[0]>0 && PieceJeu(strval(intval($tmp[0])-1).$tmp[1])!= "99"){
		$voila[] = strval(intval($tmp[0])-1).$tmp[1];
	}
	//est
	if ($tmp[1]<4 && PieceJeu($tmp[0].strval(intval($tmp[1])+1)) != "99"){
		$voila[] = $tmp[0].strval(intval($tmp[1])+1);
	}
	//sud
	if ($tmp[0]<4 && PieceJeu(strval(intval($tmp[0])+1).$tmp[1]) != "99"){
		$voila[] = strval(intval($tmp[0])+1).$tmp[1];	
	}
	//ouest
	if ($tmp[1]>0 && PieceJeu($tmp[0].strval(intval($tmp[1])-1)) != "99"){
		$voila[] = $tmp[0].strval(intval($tmp[1])-1);
	}

	if ($userinfo["variante"] == 1 && $userinfo['tour'] <4){
		//echo "je fais quelque chose";
		if (($key = array_search("02", $voila)) !== false) {
		    unset($voila[$key]);
		}
		if (($key = array_search("42", $voila)) !== false) {
		    unset($voila[$key]);
		}
	}

	return (count($voila)>0);
	//return (in_array($element, $table));
	/*switch ($orientation) {
		case 0:
			$voila=strval(intval($tmp[0])-1).$tmp[1];
			return (PieceJeu($voila) != "99");
			break;
		case 1:
			$voila=$tmp[0].strval(intval($tmp[1])+1);
			return (PieceJeu($voila) != "99");
			break;
		case 2:
			$voila=strval(intval($tmp[0])+1).$tmp[1];
			return (PieceJeu($voila) != "99");
			break;
		default:
			$voila=$tmp[0].strval(intval($tmp[1])-1);
			return (PieceJeu($voila) != "99");
			break;
	}*/
}

function fleches($element){//envoie la piece selectionnée.
	$FLECHES = array("20","21","22","23","24","10","11","12","13","14","00","01","02","03","04","30","31","32","33","34");
	$retour = array();
	if (PieceJeu($element) == "99"){//si la piece n'est pas dans le plateau on verifie alors 
		for ($i=0;$i<20;$i++){
			$direction = $FLECHES[$i][0];
			if (peutPush($FLECHES[$i], $direction, true)){
				$retour[]=$FLECHES[$i];
			}

		}
	}
	return $retour;

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

?>
