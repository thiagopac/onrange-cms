<?php
#CONTROLE SESSAO
	fnInicia_Sessao('locais');
	
### INPUTS
	$IDLOCAL = (int)$_REQUEST['idlocal'];

//Validacao
	if ($IDLOCAL == 0)
		die('Falha Geral: ID invalido'); 
		
//Programacao
	$DB = fnDBConn();
	
	$SQL = "SELECT * FROM LOCAL WHERE ID_LOCAL = $IDLOCAL";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	$NOME = $RET['NOME'];

	$SQL = "UPDATE LOCAL SET DT_EXCLUSAO = NOW() WHERE ID_LOCAL = $IDLOCAL";
	$RET = fnDB_DO_EXEC($DB,$SQL);
	
	$SQL = "UPDATE CHECKINS_CORRENTES SET DT_EXCLUSAO = NOW() WHERE ID_LOCAL = $IDLOCAL";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"Apagou o local <strong>$NOME</strong>");
	
	header('location: ../locais/?msg='.urlencode('Local apagado com sucesso'));
	exit;
?>