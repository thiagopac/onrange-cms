<?php
#CONTROLE SESSAO
	fnInicia_Sessao('administradores');
	
### INPUTS
	$ID = (int)$_REQUEST['ID'];

//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
		
//Programacao
	$DB = fnDBConn();
	
	$SQL = "SELECT * FROM ADMINISTRADOR WHERE ID = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	$LOGIN = $RET['login'];

	$SQL = "UPDATE ADMINISTRADOR SET LOGIN = CONCAT(login,'_OFF'), STATUS = 0 WHERE STATUS = 1 AND ID = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"Apagou a conta do Administrador: <strong>$LOGIN</strong>");
	
	header('location: ../administradores/?msg='.urlencode('Administrador apagado com sucesso'));
	exit;
?>