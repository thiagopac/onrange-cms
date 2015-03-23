<?php
#SETA TIMEOUT
	$PHP_TIMEOUT = 300;
	$MYSQL_TIMEOUT = $PHP_TIMEOUT + 10;
        
//definindo hora padrão da aplicacao
date_default_timezone_set('America/Sao_Paulo');        
	
#DEFAULT
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);	
	
	if ($_SERVER['SERVER_ADMIN'] == 'admin@roomnants.com')
		define('FILE_LOG', '/var/log/www/'.date('Y-m-d').'-cms.roomnants.com_PHP_error.log');
	else
		define('FILE_LOG', 'C:/Server/Apache2.2/logs/'.date('Y-m-d')."-cms.roomnants.com_PHP_error.log");

	set_time_limit($PHP_TIMEOUT);
	
#ACESSO MYSQL
	$MYSQL_HOST  = "mysql.hostinger.com.br";
	$MYSQL_LOGIN = "u138894269_onrng";
	$MYSQL_SENHA = 'onrange8375';
	$MYSQL_PORTA = 3306;
	$MYSQL_DATABASE = 'u138894269_onrng';
//	var_dump($_SERVER['SERVER_NAME']);die;
	if ($_SERVER['SERVER_NAME'] == 'localhost')
		{
		$MYSQL_HOST  = "localhost";
		$MYSQL_LOGIN = "root";
		$MYSQL_SENHA = "";
		$MYSQL_PORTA = 3306;
		$MYSQL_DATABASE = 'cms_homo';
		}

#MENU
	$MENU_GRANT[]  = array('dashboard');
	$MENU_GRANT[]  = array('administradores');
	$MENU_GRANT[]  = array('configuracoes');
	$MENU_GRANT[]  = array('auditoria');
	$MENU_GRANT[]  = array('clientes');
	$MENU_GRANT[]  = array('locais');
	$MENU_GRANT[]  = array('clienteslocais');
	$MENU_GRANT[]  = array('gerarpromos');
	$MENU_GRANT[]  = array('listarpromos');
	$MENU_GRANT[]  = array('logs_no_sistema');
	
	

//VARIAVEIS
	$TITULO = "Onrange CMS";
	
//INCLUDES
	include('funcoes.php');
	include('funcoes_genericas.php');
?>