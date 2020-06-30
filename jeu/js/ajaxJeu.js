
function hiddenFleche(){
	tab = ["20","21","22","23","24","10","11","12","13","14","00","01","02","03","04","30","31","32","33","34"];
	for (var i=0; i<tab.length; i++){
		document.getElementById(tab[i]+"fleche").childNodes[0].hidden = true;
	}
}

function affFleche(table){
	if (typeof table !== 'undefined'){
		for (var i=0; i<table.length; i++){
			document.getElementById(table[i]+"fleche").childNodes[0].hidden = false;
		}
	}
}


function affJoueur(joueur){
	if (joueur == idjoueur){
		document.getElementById("VS").innerHTML="C'est à vous de jouer";
        document.getElementById('VS').className = "btn btn-success";
        //document.getElementById('blabla').innerHTML ="";
        //document.getElementById('blabla').className = "btn btn-warning";
	}
	else{
		document.getElementById("VS").innerHTML="C'est au tour de votre adversaire";
        document.getElementById('VS').className = "btn btn-danger";
        document.getElementById('blabla').innerHTML ="";
	}
}

function cleanPiece(){
	for (var i=0; i<5; i++){
		for (var j=0; j<5; j++){
			document.getElementById(String(i)+String(j)).style.backgroundColor="";
		}
	}
}

function selection(select){
	for (var i=0; i<select.length; i++){
		document.getElementById(select[i]).style.backgroundColor="red";
	}
}

function affPiece(piece){
	if (piece.length > 0){
		document.getElementById(String(piece)).style.backgroundColor="orange";
	}
}

function dernierPiece(derniere){
	if ((typeof derniere !== 'undefined')){// || (derniere != "99")){
		if (derniere != "99"){
			document.getElementById(String(derniere)).style.backgroundColor="blue";
		}
	}
}

function cestfinit(finit){
    if ((typeof finit !== 'undefined')){
        if (finit == idjoueur){
            alert('Vous avez gagné !');
            window.location.replace("./../panel.php");
        }
        else{
            alert('Vous avez perdu !');
            window.location.replace("./../panel.php");
        }
    }
}

function creerPlateau(table){
	sortie="";
	//sortie+="<table id='PGame'> \n"
	sortie+= "<tr>\n<td></td>\n";
	for (var i=0; i<5; i++){
		sortie += "<td id=\"2"+i+"fleche\"><input type=\"button\" onclick=\"onClickArrow('2"+String(i)+"');\" style=\"transform: rotate(180deg); width: 80px; height: 80px; background-image: url(&quot;img/push.png&quot;);\"/></td>";
	}
	sortie += "<td></td></tr>";
	for (var i=0; i<5 ;i++){
		sortie+="<tr> \n";
		sortie += "<td id=\"1"+i+"fleche\"><input type=\"button\" onclick=\"onClickArrow('1"+String(i)+"');\"style=\"transform: rotate(90deg); width: 80px; height: 80px; background-image: url(&quot;img/push.png&quot;);\"/></td>";
		for (var j=0; j<5; j++){
			tmp=String(i)+String(j);
			if (tmp == table[i][j][0]){
				sortie+="<td id=\""+tmp+"\"><input type=\"button\" onclick=\"onClickCell('"+String(i)+String(j)+"');\" name=\"case1\" style=\"width: 70px; height: 70px\"/></td>\n";
			}
			else{
				sortie+="<td id=\""+tmp+"\"><input type=\"button\" onclick=\"onClickCell('"+String(i)+String(j)+"');\" name=\"case1\" style=\"transform: rotate("+(90*table[i][j][1])+"deg); width: 70px; height: 70px; background-image: url(&quot;img/"+table[i][j][0]+".png&quot;);\"/></td>\n";
			}

		}
		sortie += "<td id=\"3"+i+"fleche\"><input type=\"button\" onclick=\"onClickArrow('3"+String(i)+"');\" style=\"transform: rotate(270deg); width: 80px; height: 80px; background-image: url(&quot;img/push.png&quot;);\"/></td>";
		sortie+="</tr> \n"
	}
	sortie += "<tr>\n<td></td>\n";
	for (i=0; i<5; i++){
		sortie += "<td id=\"0"+i+"fleche\"><input type=\"button\" onclick=\"onClickArrow('0"+String(i)+"');\" style=\"transform: rotate(0deg); width: 80px; height: 80px; background-image: url(&quot;img/push.png&quot;);\"/></td>";
	}
	sortie += "<td></td></tr>";
	document.getElementById("PGame").innerHTML = sortie;
}

function affichageJoueur1(table){//5
	sortie="<tr> \n";
	for (var i=0; i<5 ;i++){
		tmp=String(5)+String(i);
		if (tmp == table[5][i][0]){
			sortie+="<td id=\""+table[5][i][0]+"\"><input type=\"button\" onclick=\"onClickPiece('"+String(5)+String(i)+"');\" name=\"case1\" style=\"transform: rotate("+(90*table[5][i][1])+"deg); width: 80px; height: 80px; background-image: url(&quot;img/"+tmp+".png&quot;);\"/></td>\n";
		}
		else{
			sortie+="<td></td>\n";
		}
	}
	sortie+="</tr> \n"
	document.getElementById("Zpiece").innerHTML= sortie;
}

function affichageJoueur2(table){//6
	sortie="<tr> \n";
	for (var i=0; i<5 ;i++){
		tmp=String(6)+String(i);
		if (tmp == table[6][i][0]){
			sortie+="<td id=\""+table[6][i][0]+"\"><input type=\"button\" onclick=\"onClickPiece('"+String(6)+String(i)+"');\" name=\"case1\" style=\"transform: rotate("+(90*table[6][i][1])+"deg); width: 80px; height: 80px; background-image: url(&quot;img/"+tmp+".png&quot;);\"/></td>\n";
		}
		else{
			sortie+="<td></td>\n";
		}
	}
	sortie+="</tr> \n"
	document.getElementById("Zpiece2").innerHTML= sortie;
}

function securite(nombre){
    if (typeof nombre !== 'undefined'){
        secu+=1;
    }
}

$(document).ready(function(){
    $("#rotateUp").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onFaceUp",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Rotation effecutée";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);         	
            },
            'json'
         );
        MAJ();
    });
    $("#rotateDown").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onFaceDown",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Rotation effecutée";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });
    $("#rotateLeft").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onFaceLeft",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Rotation effecutée";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });
    $("#rotateRight").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onFaceRight",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Rotation effecutée";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });
    $("#push").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onPush",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Pousser effecutée.";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });
    $("#end").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "onSkip",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Fin de tour effecutée.";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });

    $("#recup").click(function(e){
        e.preventDefault();
        $.post(
            'actionJeu.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
                id_joueur : idjoueur,
                script : "recup",
                element : "00",
                securite : secu
            },
            function(data){
            	var truc = data.error;
                var secur = data.data;
            	if (truc != null){
            		document.getElementById("blabla").innerHTML = truc;
                    document.getElementById('blabla').className = "btn btn-warning";
            	}
            	else{
            		document.getElementById("blabla").innerHTML = "Votre pièce est dans votre jeu.";
                    document.getElementById('blabla').className = "btn btn-success";
            	}
                securite(secur);          	
            },
            'json'
         );
        MAJ();
    });



    $.post(
            'jeuInfo.php', // Un script PHP que l'on va créer juste après
            {
                id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
            },
            function(data){
            	var truc = data.plateau;
            	var selct = data.selection;
            	var piece = data.piece;
            	var derniere = data.derniere;
            	var joueur = data.joueur;
            	var fleches = data.fleches;
            	creerPlateau(truc);
            	affichageJoueur1(truc);
            	affichageJoueur2(truc);
            	cleanPiece(); 
            	selection(selct);
            	affPiece(piece);
            	dernierPiece(derniere);
            	affJoueur(joueur);
            	hiddenFleche();
            	affFleche(fleches);
            },
            'json'
         );




});

function envoie(script, element){
    $.post(
    'actionJeu.php', // Un script PHP que l'on va créer juste après
    {
        id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
        id_joueur : idjoueur,
        script : script,
        element : element,
        securite : secu
    },
    function(data){
    	var erreur = data.error;
        var secur = data.data;
    	if (typeof erreur !== 'undefined'){
    		document.getElementById("blabla").innerHTML = erreur;
            document.getElementById('blabla').className = "btn btn-warning";
    	}
        securite(secur); 
    	MAJ();
    },
    'json'
 );
}

function MAJ(){
	    $.post(
        'jeuInfo.php', // Un script PHP que l'on va créer juste après
        {
            id_table : idtable,  // Nous récupérons la valeur de nos input que l'on fait passer à connexion.php
        },
        function(data){
        	var truc = data.plateau;
        	var selct = data.selection;
        	var piece = data.piece;
        	var derniere = data.derniere;
        	var joueur = data.joueur;
        	var fleches = data.fleches;
            var finit = data.finit;
            cestfinit(finit);
        	creerPlateau(truc);
        	affichageJoueur1(truc);
        	affichageJoueur2(truc);
        	cleanPiece() 
        	selection(selct);
        	affPiece(piece); 
        	dernierPiece(derniere);
        	affJoueur(joueur);
        	hiddenFleche();
        	affFleche(fleches);
        },
        'json'
     );
}


function onClickPiece(element){
	envoie("onClickPiece", element);
}

function onClickCell(element){
	envoie("onClickCell", element);
}

function onClickArrow(element){
	envoie("onClickArrow", element);
}

function charger(){
	setTimeout(function(){
		MAJ();
		charger();
	}, 1000);
}

charger();