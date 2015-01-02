<?php
#CONTROLE SESSAO
	fnInicia_Sessao('disparo_catequizacao');
	
### INPUTS
	$ID = (int)$_REQUEST['id'];

//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
		
//Programacao
	$DB = fnDBConn();
	
	$ID_CLIENTE = (int)$_SESSION['ADMINISTRADOR']['id_cliente'];
	
	$SQL = "select base_enviada from disparo where id = $ID and (id_cliente = $ID_CLIENTE or $ID_CLIENTE = 1)";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	echo str_replace(chr(10),'<br>',$RET['base_enviada']);
?>