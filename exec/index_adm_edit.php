<?php
#CONTROLE SESSAO
	fnInicia_Sessao('administradores');
	
### INPUTS
	$ID 		= (int)$_REQUEST['id'];
	$NOME 	= trim(addslashes($_REQUEST['nome']));
	$LOGIN 	= trim(addslashes($_REQUEST['login']));
	$strPASS = trim(addslashes($_REQUEST['password']));
	$strPASS_MD5 	= fnSenhaMD5($strPASS);

	$URL_BACK = "location: ../administradores/editar.php?id={$ID}&msg=";

	$DB = fnDBConn();	
	
//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
	
	if ($NOME == '')
		{
		header($URL_BACK.urlencode('Nome nao pode ficar em branco.'));
		exit;
		}
		
	if (!filter_var($LOGIN))
		{
		header($URL_BACK.urlencode('Login nao pode ficar em branco.'));
		exit;
		}
		
	if ($strPASS == '')
		{
		header($URL_BACK.urlencode('A senha nao pode ficar em branco'));
		exit;
		}

//Verifica se ja tem uma senha atribuida pra esse login (ou se eh um login novo)
	if ($strPASS == '')
		{
		$SQL = "SELECT * FROM ADMINISTRADOR WHERE ID = $ID";
		$RET = fnDB_DO_SELECT($DB,$SQL);
		
		if ($RET['senha'] == '')
			{
			header($URL_BACK.urlencode('Senha deve ter 5 caracteres no minimo.'));
			exit;
			}
		}
		
//Verifica se alguem ja ta usando esse email
	$SQL = "SELECT ID FROM ADMINISTRADOR WHERE LOGIN = '{$LOGIN}' AND ID <> $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	
	if ($RET['ID'] != 0)
		{
		header($URL_BACK.urlencode("Erro: O email $LOGIN esta cadastrado no sistema."));
		exit;
		}
	
//Programacao
	if ($strPASS != '')
		$SQL_SENHA = ", senha = '$strPASS_MD5'";
		
	$SQL = "UPDATE ADMINISTRADOR SET NOME = '$NOME', LOGIN = '$LOGIN', STATUS = 1 $SQL_SENHA WHERE ID = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	//Adiciona registro na tabela de auditoria
	fnDB_LOG_AUDITORIA_ADD($DB,"O administrador <strong>$LOGIN</strong> foi criado ou atualizado.");
	

	header($URL_BACK.urlencode('Alterações realizadas com sucesso!'));
	exit;
?>