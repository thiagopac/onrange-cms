<?php

#CONTROLE SESSAO
	fnInicia_Sessao('clientes');
	
### INPUTS
	$ID 		= (int)$_REQUEST['id'];
	$SMS_SALDO = (int)preg_replace('/[^0-9-]|(?<=.)-/', '', substr(addslashes(trim($_REQUEST['sms_saldo'])),0,20));
	
	$URL_BACK = "location: ../clientes/creditos_sms.php?id={$ID}&msg=";

	$DB = fnDBConn();	

//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
	
	if ($SMS_SALDO == 0)
		{
		header($URL_BACK.urlencode('Erro: Valor de Créditos SMS esta vazio.'));
		exit;
		}
		
//Programacao
	fnDB_CLIENTE_ADD_CREDITOS_SMS($DB,$ID,$SMS_SALDO);
		
	header($URL_BACK.urlencode('Alterações realizadas com sucesso!'));
	exit;
?>