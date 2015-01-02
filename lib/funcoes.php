<?php
function fnValidaChars($texto)
	{
	$arCHARS_VALIDOS = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m',
							 '1','2','3','4','5','6','7','8','9','0',
							 ' ',',','.',':',';','?','!','(',')','=','/','*','$','-','+','%','#','@');
	
	$texto = strtolower($texto);
	
	for($x=0;$x < strlen($texto);$x++)
		{
		if (!in_array($texto[$x],$arCHARS_VALIDOS))
			return($texto[$x]);
		}
		
	return(true);
	}
	
function RemoveAcentos($string="") 
{   
   if($string != "")
   {      
	  $com_acento = "Ã¡ Ã  Ã£ Ã¢ Ã¤ Ã© Ã¨ Ãª Ã« Ã­ Ã¬ Ã® Ã¯ Ã³ Ã² Ã´ Ãµ Ã¶ Ãº Ã¹ Ã» Ã¼ Ã� Ã€ Ãƒ Ã‚ Ã„ Ã‰ Ãˆ ÃŠ Ã‹ Ã� ÃŒ ÃŽ Ã� Ã“ Ã’ Ã• Ã�? Ã– Ãš Ã™ Ã› Ãœ Ã§ Ã‡ Ã± Ã‘";   
      $sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I I O O O O O U U U U c C n N";   
      $c = explode(' ',$com_acento);
      $s = explode(' ',$sem_acento);
   
      $i=0;
      foreach($c as $letra)
      {
          /**
           * @todo Trocar ereg por preg_match
           */
         if(@ereg($letra, $string))
         {
            $pattern[] = $letra;
            $replacement[] = $s[$i];
         }      
         $i=$i+1;      
      }
      
      if(isset($pattern))
      {
         $i=0;
         foreach($pattern as $letra)
         {             
            $string = eregi_replace($letra, $replacement[$i], $string);
            $i=$i+1;      
         }
         return $string; # retorna string alterada
      }   

      return $string; # retorna a mesma string se nada mudou
   }
}

function fnSELECT_CLIENT($DB,$SELECTED,$SHOW_LABEL = true,$TAMANHO = 4)
	{
	//Nao Ã© Adminstrador?
	$ID_CLIENTE = (int)$_SESSION['ADMINISTRADOR']['id_cliente'];
	if ($ID_CLIENTE != 1)
		{
		$ret = '<input type="hidden" name="ID_CLIENTE" id="CLIENTE" value="'.$ID_CLIENTE.'" />';
		return(array($ID_CLIENTE,$ret));
		}
	
	//Se eh adminitrador... Continua listando os clientes...
	$ID_CLIENTE = 0;
				
	$SQL = "SELECT C.ID, A.NOME, A.LOGIN
			FROM CLIENTE C, ADMINISTRADOR A
			WHERE C.STATUS = 1 AND C.ID <> 1
			  AND A.ID_CLIENTE = C.ID
			  AND A.STATUS = 1
			 ORDER BY A.NOME
		   ";
	$RET = fnDB_DO_SELECT_WHILE($DB,$SQL);

	//if ($SHOW_LABEL)
	$ret = '<div class="col-md-'.$TAMANHO.'">';
		
	if ($SHOW_LABEL)
		$ret .= '<label>Cliente</label>';
	
	$ret .= '<select class="form-control" name="id_cliente" id="search_category_id">';
	
	if ((count($RET) > 1) && ($SELECTED == ''))
		$ret .= "<option value=\"0\">Selecione...</option>";
		
	foreach($RET as $KEY => $ROW)
		{
		$sel = '';		
		if (($ROW['ID'] == $SELECTED) || (count($RET) == 1))
			{
			$sel = 'selected';
			$ID_CLIENTE = (int)$ROW['ID'];
			}
			
		$ret .= "<option $sel value=\"{$ROW['ID']}\">{$ROW['NOME']}</option>";
		}
	
	$ret .= '</select>';

	//if ($SHOW_LABEL)
	$ret .= '</div>';
	
	return(array($ID_CLIENTE,$ret));
	}
function pwStrongCheck($pwd)
	{
		$error = false;
		if(strlen($pwd) < 8)//to short
		{
			$error = true;
		}
		/*if(strlen($pwd) > 20)//to long
		{
			$error = true;
		}*/	
		if(!preg_match("#[0-9]+#", $pwd))//at least one number
		{
			$error = true;
		}
		if(!preg_match("#[a-z]+#", $pwd))//at least one letter
		{
			$error = true;
		}
		/*if(!preg_match("#[A-Z]+#", $pwd))//at least one capital letter
		{
			$error = true;
		}*/
		if(!preg_match("#\W+#", $pwd))//at least one symbol
		{
			$error = true;
		}
		return $error;
	}

function fnInicia_Sessao($strGrantPage)
	{
	global $MENU_GRANT,$MENU_ATIVO;
		
	session_start();
	
	if (!fnVerifica_Grant($strGrantPage))
		{
		header("location: ../index.php?erroMsg=".urlencode('Sem acesso para a pÃ¡gina. Tente novamente.')); 
		exit;
		}
		
	$MENU_ATIVO = $strGrantPage;
	}
	
function fnVerifica_Grant($strGrantPage,$grants = 'NULL')
	{
	global $MENU_GRANT;
	
	if ($grants == 'NULL')
		$grants = $_SESSION['ADMINISTRADOR']['GRANTS'];
		
	if (strpos($grants, '|'.$strGrantPage.'|') !== false)
		return(true);
	
	return(false);
	}
	
function fnHeaderLogin()
	{
	global $MENU_GRANT;
	
	foreach($MENU_GRANT as $i => $arItem)
		{
		if (strpos($_SESSION['ADMINISTRADOR']['GRANTS'], '|'.$arItem[0].'|') !== false)
			{
			header("location: ../".$arItem[0].'/'); exit;
			}
		}
	}
	
# RETORNO DADOS DO ADMINISTRADOR NO LOGIN
function fnDB_ADMINISTRADOR_INFO($DB,$strLogin,$strPass)
	{
	global $DATABASE_NAME;
	$error = false;


	$strSQL = "SELECT ID, NOME, LOGIN, GRANTS, ID_TIPO_ADMIN
				FROM $DATABASE_NAME.ADMINISTRADOR
				WHERE LOGIN = '$strLogin'
				AND SENHA = '$strPass'
				AND STATUS=1";
					

	$qy = mysqli_query($DB, $strSQL) or $error = true;

	if ($error)
	fnLogText('(fnDB_ADMINISTRADOR_INFO) MySQL Error: '.mysqli_error($DB).' (SQL: '.$strSQL.')',true);

	$linha = mysqli_fetch_array($qy);

	return($linha);
	}
		
# GERA HASH DA SENHA DO ADMINISTRADOR
function fnSenhaMD5($string)
	{
	global $SENHA_MD5;
	
		return md5($string);
	}

	
# GERA LOG DE AUDITORIA DE AÃ‡Ã•ES DO SISTEMA
function fnDB_LOG_AUDITORIA_ADD($DB,$descricao,$loga_request = true){
	global $DATABASE_NAME;
	
	$error = false;
				
	$ip 	  = addslashes($_SERVER['REMOTE_ADDR']);
	$descricao= addslashes($descricao);
			
	//Pra nao logar as senhas
	if ($loga_request)
		$request= addslashes(print_r($_REQUEST,true));
	
	//SQL 1
	$strSQL = "INSERT INTO AUDITORIA 
				(IP, ID_USER, ACAO_DESC, REQUEST, DIN_REF, DIN)
				VALUES
				('$ip', {$_SESSION['ADMINISTRADOR']['ID']}, '$descricao', '$request', CURDATE(), NOW())";
	$qy = mysqli_query($DB, $strSQL) or $error = true;

	if ($error)
		fnLogText('(fnDB_LOG_AUDITORIA_ADD) MySQL Error: '.mysqli_error($DB).' (SQL: '.$strSQL.')',true);
	
	if($DB->affected_rows == 0){
		return(false);
	} 
	  
	$Id = (int)mysqli_insert_id($DB);  	
		
	if ($Id == 0)
		return(false);
			
	return(true);
}

function fnRemoveAcentos($str, $enc = "UTF-8")
	{

	$str = str_replace('"',' ',$str);
	$str = str_replace('Â´',' ',$str);
	$str = str_replace("'",' ',$str);
	$str = str_replace("Â¨",' ',$str);
	$str = str_replace("~",' ',$str);
	$str = str_replace("^",' ',$str);
	  
	$acentos = array(
	'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
	'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
	'C' => '/&Ccedil;/',
	'c' => '/&ccedil;/',
	'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
	'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
	'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
	'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
	'N' => '/&Ntilde;/',
	'n' => '/&ntilde;/',
	'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
	'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
	'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
	'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
	'Y' => '/&Yacute;/',
	'y' => '/&yacute;|&yuml;/',
	'a.' => '/&ordf;/',
	'o.' => '/&ordm;/');
	
	 return preg_replace($acentos,
						   array_keys($acentos),
						   htmlentities($str,ENT_NOQUOTES, $enc));
	}
	
function sanitizaLocal($DB, $localDoador, $localRecebedor){
	
    $result = mysqli_query($DB,"SELECT qt_checkin FROM CHECKINS_CORRENTES WHERE id_local = '" . $localDoador . "';");

    $qt_checkin_doador = mysqli_fetch_array($result);

    $result = mysqli_query($DB,"SELECT qt_checkin FROM CHECKINS_CORRENTES WHERE id_local = '" . $localRecebedor . "';");

    $qt_checkin_recebedor = mysqli_fetch_array($result);

    $qt_checkin = $qt_checkin_doador[0] + $qt_checkin_recebedor[0];

    $result1 = mysqli_query($DB,"UPDATE CHECKINS_CORRENTES SET qt_checkin = '" . $qt_checkin . "' WHERE id_local = '" . $localRecebedor . "';");

    $result2 = mysqli_query($DB,"UPDATE CHECKINS_CORRENTES SET qt_checkin = 0 WHERE id_local = '" . $localDoador . "';");

    $result3 = mysqli_query($DB,"UPDATE CHECKIN SET id_local = '" . $localRecebedor . "' WHERE id_local = '" . $localDoador . "';");

    $result4 = mysqli_query($DB,"UPDATE LOCAL SET dt_exclusao = NOW() WHERE id_local = '" . $localDoador . "';");

    if($result1 && $result2 && $result3 && $result4){
    	
    	//Adiciona registro na tabela de auditoria
    	fnDB_LOG_AUDITORIA_ADD($DB,'Efetuou trasferência de checkins.',false);
    	
            return $MSG = "Checkins transferidos com sucesso";
    }else{
            return $MSG = "Erro na transferencia de checkins";
    }	

}

function alteraTempo($DB, $txt_t_local, $txt_t_checkin){
	$result = mysqli_query($DB,"UPDATE CONFIGURACAO SET T_CHECKIN = '" . $txt_t_checkin . "', T_LOCAL = '" . $txt_t_local . "';");
	if($result){
		
		//Adiciona registro na tabela de auditoria
		fnDB_LOG_AUDITORIA_ADD($DB,'Alterou configuração de tempo.',false);
		
		return $MSG = "Tempo alterado com sucesso";
	}else{
		return $MSG = "Erro ao alterar tempo";
	}
}

?>