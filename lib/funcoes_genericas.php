<?php
function fnFormataNumero($TOTAL,$VALOR,$format = '', $SHOW_PERCENT = TRUE)
	{
	//Retorna quando o valor ta zerado.
	if ($VALOR == 0)
		return($VALOR);
		
	//Deixa o numero bonitinho
	$A = number_format($VALOR, 0, ',', '.');
	
	//Retorna quando o percentual ta zerado.
	if ($TOTAL == 0)
		return($A);
		
	//Colocar o percentual
        if($SHOW_PERCENT)
	$A .= '<br>('.round((($VALOR/$TOTAL)*100),1).'%)'; 
	
	//Colocar um Bold
	if ($format == 'b')
		$A = "<b>$A</b>";
	
	return($A);
	}
function fnMontaFrase($str,$v1='',$v2='',$v3='',$v4='',$v5='',$v6='',$v7='',$v8='')
	{
	$str = str_replace('!VAR1', $v1, $str);
	$str = str_replace('!VAR2', $v2, $str);
	$str = str_replace('!VAR3', $v3, $str);
	$str = str_replace('!VAR4', $v4, $str);
	$str = str_replace('!VAR5', $v5, $str);
	$str = str_replace('!VAR6', $v6, $str);
	$str = str_replace('!VAR7', $v7, $str);
	$str = str_replace('!VAR8', $v8, $str);
	
	return($str);
	}

function fnADDParam($tag,$value)
	{
	$tag = strtoupper($tag);
	return("<{$tag}>{$value}</{$tag}>");
	}
	
function fnGETParam($strNodeName, $strText)
	{
	$intStartPos = strpos( $strText, '<' . $strNodeName . '>' );
	if ($intStartPos === false) return('');
		
	$intStartPos += strlen( '<' . $strNodeName . '>' );
			
	$intLength = strpos( $strText, '</' . $strNodeName . '>');
	if ($intLength === false) return('');	
	
	$intLength -= $intStartPos;
	
	return substr( $strText, $intStartPos, $intLength );
	}
	
function fnLogText($strMessage,$booDie = false)
	{
	global $booDEV;
	
	if ($booDEV) 
		echo $strMessage;
		
	$fp = fopen(FILE_LOG, 'a');
	fwrite($fp, date("Y/m/d H:i:s")." - (IP: {$_SERVER['REMOTE_ADDR']})(USER AGENT: {$_SERVER['HTTP_USER_AGENT']})(REQUEST_URI: {$_SERVER['REQUEST_URI']}) -  ".$strMessage.chr(13).chr(10));
	fclose($fp);
	
	if ($booDie)
		{
		die('FALHA GERAL: '.$strMessage);
		}
	}
	
function fnDBConn()
	{
	global $MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_SENHA, $MYSQL_PORTA, $MYSQL_DATABASE, $MYSQL_TIMEOUT;
	
	$erro = false;	
	$DBtmp = mysqli_connect($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_SENHA, $MYSQL_DATABASE,$MYSQL_PORTA) or $erro = true;
        
	$DBtmp->set_charset("utf8");
	
	if ($erro)
		{
		$erro = false;	
		sleep(3);
		$DBtmp = mysqli_connect($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_SENHA, $MYSQL_DATABASE,$MYSQL_PORTA) or $erro = true;
		}
		
	if ($erro)
		{
		sleep(3);
		$DBtmp = mysqli_connect($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_SENHA, $MYSQL_DATABASE,$MYSQL_PORTA) or fnLogText('(fnDBConn) '.mysqli_connect_error(),true);
		}
		
	fnDB_DO_EXEC($DBtmp,"SET wait_timeout = {$MYSQL_TIMEOUT}");
	
	return($DBtmp);
	}
	
function fnDBConn_CLIENTE($JSON)
	{
	global $MYSQL_TIMEOUT;
	
	$TMP = json_decode($JSON,true);
	
	$CLI_HOST = $TMP['mysql']['host'];
	$CLI_PORTA = $TMP['mysql']['porta'];
	$CLI_LOGIN = $TMP['mysql']['login'];
	$CLI_SENHA = $TMP['mysql']['senha'];
	$CLI_DATABASE = $TMP['mysql']['database'];
	
	if (($CLI_HOST == '') || ((int)$CLI_PORTA <= 0) || ($CLI_LOGIN == '') || ($CLI_DATABASE == ''))
		return(array('ERRO','Parametros do MySQL estão inválidos.'));
	
		
	$erro = false;		
	$DBtmp = mysqli_connect($CLI_HOST, $CLI_LOGIN, $CLI_SENHA, $CLI_DATABASE,$CLI_PORTA) or $erro = true;
	
	$DBtmp->set_charset("utf8");
	
	if ($erro)
		return(array('ERRO','Erro ao conectar no MySQL: '.mysqli_connect_error()));
		
	fnDB_DO_EXEC($DBtmp,"SET wait_timeout = {$MYSQL_TIMEOUT}");
		
	return(array('OK',$DBtmp));
	}
	
function fnDB_DO_EXEC($DB, $strSQL)
	{
	global $DATABASE_NAME,$SQL_DUMP;
	
	$error = false;
	
	$SQL_DUMP .= "\n********************************************************************************\n";
	$SQL_DUMP .= $strSQL;
	
	$qy = mysqli_query($DB,$strSQL) or $error = true;
	
	if ($error)
		fnLogText('(fnDB_DO_EXEC) MySQL Error: '.mysqli_error($DB).' (SQL: '.$strSQL.')',true);

	return(array((int)$DB->affected_rows, (int)mysqli_insert_id($DB)) );
}

function fnDB_DO_SELECT_WHILE($DB, $strSQL)
{
	global $DATABASE_NAME,$SQL_DUMP;
	$error = false;

	$SQL_DUMP .= "\n********************************************************************************\n";
	$SQL_DUMP .= $strSQL;

	$qy = mysqli_query($DB,$strSQL) or $error = true;

	if($error) fnLogText('(fnDB_DO_SELECT_WHILE) MySQL Error: '.mysqli_error($DB).' (SQL: '.$strSQL.')',true);

	$arItem = array();
	$i=0;

	while($linha = mysqli_fetch_assoc($qy))
	{		
		$arItem[$i] = $linha;
		$i++;
	}

	return($arItem);
}

function fnDB_DO_SELECT($DB, $strSQL)
	{
	global $DATABASE_NAME,$SQL_DUMP;
	
	$SQL_DUMP .= "\n********************************************************************************\n";
	$SQL_DUMP .= $strSQL;

	$error = false;
	
	$qy = mysqli_query($DB,$strSQL) or $error = true;
	
	if ($error)
		fnLogText('(fnDB_DO_SELECT) MySQL Error: '.mysqli_error($DB).' (SQL: '.$strSQL.')',true);

	$linha = mysqli_fetch_assoc($qy);
	
	return($linha);
}

function fnCALL_URL($url,$post_fields)
	{
	$curl = curl_init();
		
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
	
	curl_setopt($curl, CURLOPT_TIMEOUT, 180); //se for mexer aqui, mexer no set_time_limit tbm (que deve ser maior)
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 180);
	$result = trim(curl_exec($curl));
	
	curl_close($curl);
	return($result);
	}
	
function fnMT_RANDOM($ARRAY,$FRASE)
	{
	unset($A);
	
	if ($ARRAY[$FRASE] != '')
		$A[] = $ARRAY[$FRASE];
		
	for($i=1;$i<=10;$i++)
		{
		if ($ARRAY[$FRASE.$i] != '')
			$A[] = $ARRAY[$FRASE.$i];
		else
			break;
		}
		
	if (count($A) == 0)
		return('');
		
	$RAND = rand(0,count($A)-1);
	
	return($A[$RAND]);
	}
?>