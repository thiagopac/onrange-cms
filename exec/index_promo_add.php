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
	
//Validacao
	if ($ID_LOCAL == 0)
		die('Falha Geral: ID de Local inválido'); 
		
//Programacao
	$DB = fnDBConn();
	
	//Cria Promo
	
	if($PROMO_CHECKIN == "on")
		$PROMO_CHECKIN = 1;
	else
		$PROMO_CHECKIN = 0;
	
	$DT_INICIO = str_replace('/','-',$DT_INICIO);
	$DT_INICIO .= ' 00:00:00';
	$DT_INICIO = date('Y-m-d H:i:s',strtotime($DT_INICIO));
	
	$DT_FIM = str_replace('/','-',$DT_FIM);
	$DT_FIM .= ' 23:59:59';
	$DT_FIM = date('Y-m-d H:i:s',strtotime($DT_FIM));
	
	
	$SQL = "INSERT INTO PROMO(ID_LOCAL, DT_INICIO, DT_FIM, NOME, DESCRICAO, LOTE, PROMO_CHECKIN) VALUES ($ID_LOCAL, '$DT_INICIO', '$DT_FIM', '$NOME', '$DESCRICAO', $LOTE, $PROMO_CHECKIN)";
	$RET = fnDB_DO_EXEC($DB,$SQL);
	
	//print_r($RET);
	
	$LOTE = (int)$LOTE;
	
	//Cria codigos
	for($i=0;$i<$LOTE;$i++){
		
		$CODIGOS[$i] = floor($i/10)+1 . "-";
		
		$CODIGOS[$i] .= substr(str_shuffle("BCDFGHJKLMNPQRSTVWXZBCDFGHJKLMNPQRSTVWXZBCDFGHJKLMNPQRSTVWXZBCDFGHJKLMNPQRSTVWXZBCDFGHJKLMNPQRSTVWXZ"), 0, 5);
	
		$CODIGOS = array_unique($CODIGOS);
	
		$i = count($CODIGOS)-1;
	}
	
	sort($CODIGOS);
	
	//Gera SQL de insert
	
	$SQL = "INSERT INTO PROMO_CODIGO_USUARIO(ID_PROMO, PROMO_CODIGO) VALUES";
		
	for($i=0;$i<$LOTE;$i++){
		if($i<$LOTE-1)
			$SQL .= "($RET[1],'$CODIGOS[$i]'),";
		else
			$SQL .= "($RET[1],'$CODIGOS[$i]')";
	}
	
	
	$RET = fnDB_DO_EXEC($DB,$SQL);
		
	
	fnDB_LOG_AUDITORIA_ADD($DB,"Adicionou um Promo para o local: <strong>$ID_LOCAL</strong> com <strong>$LOTE</strong> códigos.");
	
	header('location: ../promos/gerarpromos.php?msg='.urlencode('Promo criado com sucesso!'));
	exit;

?>