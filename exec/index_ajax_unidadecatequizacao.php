<?php 
### INPUTS
	$ID = (int)$_REQUEST['id'];
        $BASE = (int)$_REQUEST['base'];
	$UNIDADE = (int)$_REQUEST['unidade'];
	$DB = fnDBConn();	
	
##Codigo
	$SQL = "select * from cliente where id = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
        
##Codigo
        $MES_ANO = date('Y-m');
        
	list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
        
	if ($ERRO == 'ERRO')
		die('<option value="0">Falhou. Tente novamente (2)</option></select>');
	
        $SQL = "    SELECT t.numero
                    FROM TELEFONE as t
                    INNER JOIN ALUNO_UNICURSO as au
                    ON t.idaluno = au.idaluno
                    INNER JOIN UNIDADE_CURSO as uc
                    ON au.idunicurso = uc.id
                    INNER JOIN UNIDADE_ENSINO as ue
                    ON uc.idunidade = ue.id
                    WHERE ue.id = $UNIDADE
                    AND LENGTH(t.numero) > 8
                    GROUP BY 1
                ";
        
        $RET = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	

	echo json_encode($RET);
?>