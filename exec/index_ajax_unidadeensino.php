<select class="bs-select form-control" name="id_unidade_ensino[]" id="sub_category_id" multiple>
<?php 
/*
<select class="bs-select form-control" name="opa" multiple>
																<option value="1">Mustard</option>
																<option value="2">Ketchup</option>
																<option value="3">Relish</option>
															</select>
*/

    $instituicoes = (isset($_REQUEST['instituicoes']) && $_REQUEST['instituicoes'] ? $_REQUEST['instituicoes'] : array());

### INPUTS
	$ID = (int)$_REQUEST['id'];
	$DB = fnDBConn();	

##Validacao
	if ($ID == 0)
		die('<option value="0">Falhou. Tente novamente (1)</option></select>');
	
##Codigo
	$SQL = "select * from cliente where id = $ID";
	$RET = fnDB_DO_SELECT($DB,$SQL);
	
	list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
	
	if ($ERRO == 'ERRO')
		die('<option value="0">Falhou. Tente novamente (2)</option></select>');
			
	$SQL = "select id,nome from UNIDADE_ENSINO where id <> 111";
	
        if(count($instituicoes) === 1)
        {
            if(in_array('Anhanguera', $instituicoes))
                    $SQL .= ' and nome like "%Anhanguera%"';
            else if(in_array('Kroton', $instituicoes))
                    $SQL .= ' and nome not like "%Anhanguera%"';
                
        }
        
        $SQL .= ' order by nome';
        
        $RET = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	

	foreach($RET as $KEY => $ROW)
		{
		?><option value="<?=$ROW['ID']?>"><?=$ROW['NOME']?></option><?
		}
?></select>