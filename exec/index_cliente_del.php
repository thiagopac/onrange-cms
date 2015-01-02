<?php
#CONTROLE SESSAO
	fnInicia_Sessao('clientes');
	
### INPUTS
	$ID = (int)$_REQUEST['id'];

//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
		
//Programacao
	$DB = fnDBConn();
	
	$SQL = "SELECT * FROM ADMINISTRADOR WHERE ID = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	$NOME = $RET['NOME'];

	$SQL = "UPDATE ADMINISTRADOR SET STATUS = 0 WHERE STATUS = 1 AND ID = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	$SQL = "UPDATE ADMINISTRADOR SET LOGIN = CONCAT(login,'_OFF'), STATUS = 0 WHERE STATUS = 1 AND ID = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"Apagou a conta do Cliente: <strong>$NOME</strong>");
	
	header('location: ../clientes/?msg='.urlencode('Cliente apagado com sucesso'));
	exit;
?>