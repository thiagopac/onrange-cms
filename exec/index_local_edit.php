<?php
#CONTROLE SESSAO
	fnInicia_Sessao('locais');
	
### INPUTS
	$IDLOCAL = (int)$_REQUEST['idlocal'];
	$NOME = trim(addslashes($_REQUEST['nome']));
	$IDTIPOLOCAL = trim(addslashes($_REQUEST['idtipolocal']));
	$LATITUDE = trim(addslashes($_REQUEST['latitude']));
	$LONGITUDE = trim(addslashes($_REQUEST['longitude']));
	
	$URL_BACK = "location: ../locais/editar.php?idlocal={$IDLOCAL}&msg=";

	$DB = fnDBConn();	

//Validacao
	if ($IDLOCAL == 0)
		die('Falha Geral: ID invalido'); 
	
	if ($NOME == '')
		{
		header($URL_BACK.urlencode('Nome nao pode ficar em branco.'));
		exit;
		}
		
	if (($LATITUDE == '') || ($LONGITUDE == ''))
		{
		header($URL_BACK.urlencode('Latitude e longitude precisam ser preenchidos.'));
		exit;
		}
		
//Programacao

		
	$SQL = "UPDATE LOCAL SET NOME = '$NOME', ID_TIPO_LOCAL = '$IDTIPOLOCAL', LATITUDE = '$LATITUDE', LONGITUDE = '$LONGITUDE'  WHERE ID_LOCAL = $IDLOCAL";
	$RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"O Local <strong>$NOME</strong> foi criado ou atualizado.");		
		
	header($URL_BACK.urlencode('Alterações realizadas com sucesso!'));
	exit;
?>