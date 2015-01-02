<?php 
### INPUTS
	$ID = (int)$_REQUEST['id'];
	$BASE = (int)$_REQUEST['base'];
	$DB = fnDBConn();	
	
##Codigo
	$SQL = "select * from cliente where id = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
        
##Codigo
        $MES_ANO = date('Y-m');
        
	list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
	
	if ($ERRO == 'ERRO')
		die('<option value="0">Falhou. Tente novamente (2)</option></select>');
			
	$SQL = "SELECT `tl`.`numero` 
                FROM `RESPOSTA_ROBO` AS `rp` 
                INNER JOIN `REQUISICAO_ROBO` AS `rq` 
                ON rq.id = rp.idreqrobo 
                INNER JOIN `MENSAGEM` AS `m` 
                ON m.id = rq.idpai 
                INNER JOIN `LISTA_ALUNO` AS `li` 
                ON li.id = m.idlistaaluno 
                INNER JOIN `TELEFONE` AS `tl` 
                ON tl.idaluno = li.idaluno
                WHERE (rp.status = $BASE) 
                AND (rq.idtipo = 1) 
                AND (rq.dtcriacao > '$MES_ANO-01 00:00:00') 
                AND (rq.dtcriacao < NOW())
                GROUP BY 1";
        
        $RET = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	

	echo json_encode($RET);
?>