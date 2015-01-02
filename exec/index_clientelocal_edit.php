<?php
#CONTROLE SESSAO
	fnInicia_Sessao('clienteslocais');
	
### INPUTS
	$ID 		= (int)$_REQUEST['id'];

	$URL_BACK = "location: ../clienteslocais/editar.php?id={$ID}&msg=";

	$DB = fnDBConn();	

//Validacao
	if ($ID == 0)
		die('Falha Geral: ID invalido'); 
	
		
//Programacao
		
	$SQL = "DELETE FROM ADMINISTRADOR_LOCAL WHERE ID_ADMINISTRADOR = $ID";
	$RET = fnDB_DO_EXEC($DB,$SQL);
        
        foreach ($_REQUEST['my_multi_select1'] as $locais) {

            $SQL_INSERT .= "('$ID','$locais'),";

        }
        
        $SQL_INSERT = rtrim($SQL_INSERT,',');

        $SQL = "INSERT INTO ADMINISTRADOR_LOCAL (ID_ADMINISTRADOR, ID_LOCAL) VALUES $SQL_INSERT";
        $RET = fnDB_DO_EXEC($DB,$SQL);

	fnDB_LOG_AUDITORIA_ADD($DB,"Os locais do cliente <strong>$ID</strong> foram atualizados.");		
		
	header($URL_BACK.urlencode('Locais alterados com sucesso!'));
	exit;
?>