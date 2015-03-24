<?php
#CONTROLE SESSAO
	fnInicia_Sessao('administradores');
	
### INPUTS
	$ID_LOCAL = $_REQUEST['LocalPromo'];
	$NOME = $_REQUEST['NomePromo'];
	$DESCRICAO = $_REQUEST['DescricaoPromo'];
	$DT_INICIO = $_REQUEST['dataInicial'];
	$DT_FIM = $_REQUEST['dataFinal'];
	$LOTE = $_REQUEST['quantidadeCodigos'];
	$PROMO_CHECKIN = $_REQUEST['promoCheckin'];
	
	if($PROMO_CHECKIN == "on")
		$PROMO_CHECKIN = 1;
	else
		$PROMO_CHECKIN = 0;

//Validacao
	if ($ID_LOCAL == 0)
		die('Falha Geral: ID de Local invalido'); 
		
//Programacao
	$DB = fnDBConn();
	
	$SQL = "INSERT INTO PROMO(ID_LOCAL, DT_INICIO, DT_FIM, NOME, DESCRICAO, LOTE, PROMO_CHECKIN) VALUES ($ID_LOCAL, '$DT_INICIO', '$DT_FIM', '$NOME', '$DESCRICAO', $LOTE, $PROMO_CHECKIN)";
	$RET = fnDB_DO_EXEC($DB,$SQL);
	
	//echo $RET;
	
	fnDB_LOG_AUDITORIA_ADD($DB,"Adicionou um Promo para o local: <strong>$ID_LOCAL</strong>");
	
	header('location: ../promos/gerarpromos.php?msg='.urlencode('Promo criado com sucesso'));
	exit;
?>