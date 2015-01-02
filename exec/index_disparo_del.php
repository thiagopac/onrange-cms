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
	
	$SQL = "select * from disparo where id = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	$ID_CLIENTE = (int)$RET['id_cliente'];
	$MENSAGEM = $RET['mensagem'];
	$TIPO_DISPARO = $RET['tipo_disparo'];
	$QTDE_BASE = (int)$RET['qtde_base'];
	

	$SQL = "SELECT NOME FROM ADMINISTRADOR WHERE ID_CLIENTE = $ID_CLIENTE";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	$NOME = $RET['nome'];
	
	$SQL = "update disparo set status = 0 where status = 1 and id = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"Cancelou disparo do cliente $NOME. Mensagem cancelada: $MENSAGEM");
	
	fnDB_CLIENTE_ADD_CREDITOS_SMS($DB,$ID_CLIENTE,$QTDE_BASE);
	
	if ($TIPO_DISPARO == 'confirmacao')
		{
		header('location: ../disparo_confirmacao/?msg='.urlencode('Disparo cancelado com sucesso'));
		exit;
		}
		
	header('location: ../disparo_catequizacao/?msg='.urlencode('Disparo cancelado com sucesso'));
	exit;
?>