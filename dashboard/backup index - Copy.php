<?
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('dashboard');

#INPUTS
	$ID_CLIENTE	= (int)$_REQUEST['id_cliente'];
	/*$PESQUISA     = addslashes(trim($_REQUEST['pesquisa']));
	$DAT_INICIO   = addslashes($_REQUEST['dat_inicio']);
	$DAT_FIM 	= addslashes($_REQUEST['dat_fim']);
	$DAT_COMPLETA = addslashes($_REQUEST['dat_completa']);
	$TIPO_ALUNO	= (int)$_REQUEST['tipo_aluno'];
	$QUEBRA		= addslashes($_REQUEST['quebra']);
	$ID_UNIDADE_ENSINO = $_REQUEST['id_unidade_ensino']; //VEM UM ARRAY AQUI*/
	
	$MSG = '';

#É IE?
	if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/') !== false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'rv:') !== false)))
		$BrowserIE = true;
	
#INICIO LOGICA
	$DB = fnDBConn();
	
	list($ID_CLIENTE,$LISTBOX_CLIENTES) = fnSELECT_CLIENT($DB,$ID_CLIENTE,false);
	
#CONECTA NO BANCO DO CLIENTE
	if ($ID_CLIENTE > 1)
		{
		$SQL = "select * from cliente where id = $ID_CLIENTE";
		$RET = fnDB_DO_SELECT($DB,$SQL);
		
		list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
		if ($ERRO == 'ERRO')
			$MSG = $DB_CLI;
		}
	
#RELATORIO: CONFIRMACOES e ALUNOS CONFIRMADOS
	if (($ID_CLIENTE > 1) && ($MSG == ''))
		{
		$ID_UNIDADE_ENSINO_FINAL = "0";
		$QUEBRA_NOME = 'Dia em que foi confirmado';
		$SQL_QUEBRA = "case when LISTA_ALUNO.dtconfirmacao is null then 'Não confirmados' else DATE_FORMAT(LISTA_ALUNO.dtconfirmacao,'%d/%m') end as 1a_coluna";
		
		$menos30dias = time( ) - 86400 * 10; //(30 - 1) sao 30 dias!
		
		$DAT_INICIO = date('Y-m-d',$menos30dias);
		$DAT_FIM = date('Y-m-d');
		
		//ESSE SQL É ***QUASE*** IGUAL AO DO VISAO_ALUNOS_CONFIRMADOS.PHP
		$SQL = "
		select $SQL_QUEBRA,
			count(distinct LISTA_ALUNO.idaluno) TOTAL,
			#count(distinct case when ifnull(LISTA_ALUNO.idtipo,0) in (0) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS_SEM_CONFIRMAR,
			count(distinct case when LISTA_ALUNO.idtipo in (1)  then LISTA_ALUNO.idaluno else null end) TOTAL_SMS,
			count(distinct case when LISTA_ALUNO.idtipo in (2)  then LISTA_ALUNO.idaluno else null end) TOTAL_APP,
			count(distinct case when LISTA_ALUNO.idtipo in (3)  then LISTA_ALUNO.idaluno else null end) TOTAL_WEB,
			count(distinct case when LISTA_ALUNO.idtipo in (1,2,3) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS,
					
			count(distinct LISTA_ALUNO.id) CONFIRMACOES_TOTAL,
			#count(distinct case when ifnull(LISTA_ALUNO.idtipo,0) in (0) then LISTA_ALUNO.id else null end) TOTAL_CONFIRMACOES_SEM_CONFIRMAR,
			count(distinct (case when LISTA_ALUNO.idtipo = 1 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_SMS,
			count(distinct (case when LISTA_ALUNO.idtipo = 2 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_APP,
			count(distinct (case when LISTA_ALUNO.idtipo = 3 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_WEB
			
		from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO, ALUNO, UNIDADE_ENSINO
		where (LISTA_ALUNO.idtipo in (0,1,2,3) or LISTA_ALUNO.idtipo is null)
		  and LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
		  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
		  and LISTA_ALUNO.idaluno = ALUNO.id
		  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
		  and LISTA_ALUNO.idaluno NOT IN ($USUARIOS_TESTES)
		  and ('$ID_UNIDADE_ENSINO_FINAL' = '0' or UNIDADE_CURSO.idunidade in ($ID_UNIDADE_ENSINO_FINAL)) #Cada cliente tera seus IDs de UNIDADE DE ENSINO
		  and (/*LISTA_ALUNO.dtconfirmacao is null or*/ LISTA_ALUNO.dtconfirmacao between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59')
		group by 1
		order by 1 desc";
		
		$ALUNOS = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	

		$SQL = "
				select 			
					count(distinct LISTA_ALUNO.idaluno) TOTAL,
					count(distinct case when ifnull(LISTA_ALUNO.idtipo,0) in (0) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS_SEM_CONFIRMAR,
					count(distinct case when LISTA_ALUNO.idtipo in (1)  then LISTA_ALUNO.idaluno else null end) TOTAL_SMS,
					count(distinct case when LISTA_ALUNO.idtipo in (2)  then LISTA_ALUNO.idaluno else null end) TOTAL_APP,
					count(distinct case when LISTA_ALUNO.idtipo in (3)  then LISTA_ALUNO.idaluno else null end) TOTAL_WEB,
					count(distinct case when LISTA_ALUNO.idtipo in (1,2,3) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS,
					
					count(distinct LISTA_ALUNO.id) CONFIRMACOES_TOTAL,
					count(distinct case when ifnull(LISTA_ALUNO.idtipo,0) in (0) then LISTA_ALUNO.id else null end) TOTAL_CONFIRMACOES_SEM_CONFIRMAR,
					count(distinct (case when LISTA_ALUNO.idtipo = 1 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_SMS,
					count(distinct (case when LISTA_ALUNO.idtipo = 2 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_APP,
					count(distinct (case when LISTA_ALUNO.idtipo = 3 then LISTA_ALUNO.id else null end)) CONFIRMACOES_TOTAL_WEB
				 
				from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO, ALUNO, UNIDADE_ENSINO
				where (LISTA_ALUNO.idtipo in (0,1,2,3) or LISTA_ALUNO.idtipo is null)
				  and LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
				  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
				  and LISTA_ALUNO.idaluno = ALUNO.id
				  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
				  and LISTA_ALUNO.idaluno NOT IN (40083, 40474, 50023, 40478, 41661, 63424, 63425, 63426, 63427, 63428)
		  ";
		 
		 $ALUNOS_TOTAIS = fnDB_DO_SELECT($DB_CLI,$SQL);	
		
		}
?>
<!--
CONSULTAS SQL:
<?
if ((int)$_SESSION['ADMINISTRADOR']['id_cliente'] == 1) //Admin
	echo $SQL_DUMP;
?>
-->
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?=$TITULO?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-select/bootstrap-select.min.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../assets/global/css/components.css" rel="stylesheet" type="text/css"/>
<link href="../assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="../assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
<link href="../assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="../dashboard/">
			<img src="../assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<? include('../_top.php'); ?>
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<? include('../_menu.php'); ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->

			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Resumo <small>dos ultimos 10 dias</small>
					</h3>
					
					<!--button type="button" class="btn red" style="right: 15px; position: absolute; margin-top: -40px" onClick="parent.location='novo.php'">Novo Cliente</button-->
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
				<?=$LISTBOX_CLIENTES?>
			</div>
			<? if (strlen($MSG) > 0 ) { ?>
								<div class="alert alert-danger display">
										<button class="close" data-close="alert"></button>
										<?=$MSG?>
									</div>
								<? } ?>
			<!-- END PAGE HEADER-->



<!-- ------------------ -->
<div class="col-md-12"><h3 class="page-title"></h3></div>
<?
/*$REPORT1_TOTAL = 0;

foreach($ALUNOS as $KEY => $ROW)
	{
	if ($REPORT1_TOTAL < (int)$ROW['CONFIRMACOES_TOTAL'])
		$REPORT1_TOTAL = (int)$ROW['CONFIRMACOES_TOTAL'];
	}*/
	
foreach($ALUNOS as $KEY => $ROW)
	{
	//Gera Totalizadores
	foreach($ROW as $KEY2 => $ROW2)
		{
		if ($KEY2 != '1a_coluna')
			$TOTAIS[$KEY2] = (int)$TOTAIS[$KEY2] + (int)$ROW2;
		}
		
	//Gera Relatorio1
	$N++;
	$RELATORIO1_BARRAS .= "[$N,'{$ROW['1a_coluna']}'],";
	$RELATORIO1_SEM_CONFIRMACAO .= "[$N,'{$ROW['TOTAL_CONFIRMACOES_SEM_CONFIRMAR']}'],";
	$RELATORIO1_TOTAL_SMS .= "[$N,'{$ROW['CONFIRMACOES_TOTAL_SMS']}'],";
	$RELATORIO1_TOTAL_APP .= "[$N,'{$ROW['CONFIRMACOES_TOTAL_APP']}'],";
	$RELATORIO1_TOTAL_WEB .= "[$N,'{$ROW['CONFIRMACOES_TOTAL_WEB']}'],";

	//$RELATORIO1_TOTAL_SMS .= "[$N,'".round(100*($ROW['CONFIRMACOES_TOTAL_SMS'] / $REPORT1_TOTAL))."'],";
	//$RELATORIO1_TOTAL_APP .= "[$N,'".round(100*($ROW['CONFIRMACOES_TOTAL_APP'] / $REPORT1_TOTAL))."'],";
	//$RELATORIO1_TOTAL_WEB .= "[$N,'".round(100*($ROW['CONFIRMACOES_TOTAL_WEB'] / $REPORT1_TOTAL))."'],";
	
	//Gera Relatorio3
	$N++;
	$RELATORIO3_BARRAS .= "[$N,'{$ROW['1a_coluna']}'],";
	$RELATORIO3_SEM_CONFIRMACAO .= "[$N,'{$ROW['TOTAL_ALUNOS_SEM_CONFIRMAR']}'],";
	$RELATORIO3_TOTAL_SMS .= "[$N,'{$ROW['TOTAL_SMS']}'],";
	$RELATORIO3_TOTAL_APP .= "[$N,'{$ROW['TOTAL_APP']}'],";
	$RELATORIO3_TOTAL_WEB .= "[$N,'{$ROW['TOTAL_WEB']}'],";
	}
?>
<!-- ------------------ -->
			
			<div class="row">
				<div class="col-md-6">
					<div class="portlet solid grey-cararra bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-bar-chart-o"></i>Confirmações
							</div>
							<div class="tools">
								<!--div class="btn-group pull-right">
									<a href="" class="btn grey-steel btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
									Filter <span class="fa fa-angle-down">
									</span>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="javascript:;">
											Q1 2014 <span class="label label-sm label-default">
											past </span>
											</a>
										</li>
										<li>
											<a href="javascript:;">
											Q2 2014 <span class="label label-sm label-default">
											past </span>
											</a>
										</li>
										<li class="active">
											<a href="javascript:;">
											Q3 2014 <span class="label label-sm label-success">
											current </span>
											</a>
										</li>
										<li>
											<a href="javascript:;">
											Q4 2014 <span class="label label-sm label-warning">
											upcoming </span>
											</a>
										</li>
									</ul>
								</div-->
							</div>
						</div>
						<div class="portlet-body">
							<div id="site_activities_content">
								<div id="grafico1" style="height: 220px;">
								</div>
							</div>
							<div style="margin: 20px 0 10px 30px">
								<div class="row">
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-warning">
										Total</span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via WEB: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_WEB'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via APP: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_APP'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via SMS: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_SMS'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-danger">
										Faltam: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['TOTAL_CONFIRMACOES_SEM_CONFIRMAR'])?></h5>
										</center>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				<div class="col-md-6">
					<div class="portlet solid grey-cararra bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-bar-chart-o"></i>Alunos confirmados
							</div>
							<div class="tools">
								<!--div class="btn-group pull-right">
									<a href="" class="btn grey-steel btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
									Filter <span class="fa fa-angle-down">
									</span>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="javascript:;">
											Q1 2014 <span class="label label-sm label-default">
											past </span>
											</a>
										</li>
										<li>
											<a href="javascript:;">
											Q2 2014 <span class="label label-sm label-default">
											past </span>
											</a>
										</li>
										<li class="active">
											<a href="javascript:;">
											Q3 2014 <span class="label label-sm label-success">
											current </span>
											</a>
										</li>
										<li>
											<a href="javascript:;">
											Q4 2014 <span class="label label-sm label-warning">
											upcoming </span>
											</a>
										</li>
									</ul>
								</div-->
							</div>
						</div>
						<div class="portlet-body">
							<div id="site_activities_content">
								<div id="grafico3" style="height: 220px;">
								</div>
							</div>
							<div style="margin: 20px 0 10px 30px">
								<div class="row">
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-warning">
										Total </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['TOTAL'],$ALUNOS_TOTAIS['TOTAL'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via WEB: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['TOTAL'],$ALUNOS_TOTAIS['TOTAL_WEB'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via APP: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['TOTAL'],$ALUNOS_TOTAIS['TOTAL_APP'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via SMS: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['TOTAL'],$ALUNOS_TOTAIS['TOTAL_SMS'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-danger">
										Faltam: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['TOTAL'],$ALUNOS_TOTAIS['TOTAL_ALUNOS_SEM_CONFIRMAR'])?></h5>
										</center>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div></div>

				<!--------------RELATORIO DE VOLUMETRIA-------------------> 

				<div class="row">
				<div class="col-md-12">
					<div class="portlet solid grey-cararra bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-bar-chart-o"></i>Disparos SMS
							</div>
							<div class="tools">
							</div>
						</div>
						<div class="portlet-body">
							<div id="site_activities_content">
								<div id="grafico4" style="height: 220px;">
								</div>
							</div>
							<div style="margin: 20px 0 10px 30px">
								<div class="row">
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-warning">
										Total</span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via WEB: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_WEB'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via APP: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_APP'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-success">
										via SMS: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['CONFIRMACOES_TOTAL_SMS'])?></h5>
										</center>
									</div>
									<div class="col-md-2 col-sm-3 col-xs-6 text-stat">
										<center><span class="label label-sm label-danger">
										Faltam: </span>
										<h5><?=fnFormataNumero($ALUNOS_TOTAIS['CONFIRMACOES_TOTAL'],$ALUNOS_TOTAIS['TOTAL_CONFIRMACOES_SEM_CONFIRMAR'])?></h5>
										</center>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>



			


        <br/>
        <br/>
					
		</div>
	</div>
	<!-- END CONTENT -->
</div>

<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2014 &copy; <?=$TITULO?>
	</div>
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>


<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../assets/global/plugins/flot/jquery.flot.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.stack.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.crosshair.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="../assets/admin/pages/scripts/components-pickers.js"></script>
<script type="text/javascript" src="../assets/outros/excellentexport.js"></script>
<script src="../assets/admin/pages/scripts/components-dropdowns.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script type="text/javascript">
$(function () { 

     var previousPoint2 = null;
     var previousPoint = null;
	 
	function showChartTooltip(x, y, xValue, yValue) {
		$('<div id="tooltip" class="chart-tooltip">' + yValue + '<\/div>').css({
			position: 'absolute',
			display: 'none',
			top: y - 40,
			left: x - 40,
			border: '0px solid #ccc',
			padding: '2px 6px',
			'background-color': '#fff'
		}).appendTo("body").fadeIn(200);
	}
	
	//
	//RELATORIO 1
	//
    var options = {
	
	
            series:{
                stack:true,
                bars: {
                                    show: true,
                                    barWidth: 0.5,
                                    lineWidth: 0, // in pixels
                                    shadowSize: 0,
                                    align: 'center',
									
                                }
            },
			
            grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        },
			xaxis: {
				ticks: [<?=trim($RELATORIO1_BARRAS,',')?>]
			}
			
					
			
			
							
    };
 
     var data1 = [
	   /*{label: 'Alunos sem confirmação', data: [<?=trim($RELATORIO1_SEM_CONFIRMACAO,',')?>]},*/
	   {label: 'Confirmações via SMS', data: [<?=trim($RELATORIO1_TOTAL_SMS,',')?>]},
	   {label: 'Confirmações via APP', data: [<?=trim($RELATORIO1_TOTAL_APP,',')?>]},
	   {label: 'Confirmações via WEB', data: [<?=trim($RELATORIO1_TOTAL_WEB,',')?>]}
    ];

	
    $.plot($("#grafico1"), data1, options);  




	
	$("#grafico1").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;

				$("#tooltip").remove();
				var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);

				showChartTooltip(item.pageX, item.pageY, item.datapoint[0], (item.datapoint[1]-item.datapoint[2]) + ' '+ item.series.label);
				console.log(item.series.label+': '+item.datapoint);
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});


	$('#grafico1').bind("mouseleave", function () {
		$("#tooltip").remove();
	});
	

	//
	//RELATORIO 3
	//
    var options = {
	
	
            series:{
                stack:true,
                bars: {
                                    show: true,
                                    barWidth: 0.5,
                                    lineWidth: 0, // in pixels
                                    shadowSize: 0,
                                    align: 'center',
									
                                }
            },
			
            grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        },
			xaxis: {
				ticks: [<?=trim($RELATORIO3_BARRAS,',')?>]
			}
			
					
			
			
							
    };
 
     var data3 = [
	   /*{label: 'Alunos sem confirmação', data: [<?=trim($RELATORIO3_SEM_CONFIRMACAO,',')?>]},*/
	   {label: 'Alunos confirmados via SMS', data: [<?=trim($RELATORIO3_TOTAL_SMS,',')?>]},
	   {label: 'Alunos confirmados via APP', data: [<?=trim($RELATORIO3_TOTAL_APP,',')?>]},
	   {label: 'Alunos confirmados via WEB', data: [<?=trim($RELATORIO3_TOTAL_WEB,',')?>]}
    ];

	
    $.plot($("#grafico3"), data3, options);  




	
	$("#grafico3").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;

				$("#tooltip").remove();
				var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);

				showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' '+ item.series.label);
				///alert('data_point:' + item.datapoint);
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});


	$('#grafico3').bind("mouseleave", function () {
		$("#tooltip").remove();
	});
	
	//
	//RELATORIO 4
	//
    var options = {
	
	
            series:{
                stack:true,
			  lines: { show: true },
			  points: {
                radius: 3,
                show: true,
                fill: true
						},
                /*bars: {
                                    show: true,
                                    barWidth: 0.5,
                                    lineWidth: 0, // in pixels
                                    shadowSize: 0,
                                    align: 'center',
									
                                }*/
            },
			
            grid: {
                            hoverable: true,
                            clickable: true,
                            tickColor: "#eee",
                            borderColor: "#eee",
                            borderWidth: 1
                        },
			xaxis: {
				ticks: [<?=trim($RELATORIO3_BARRAS,',')?>]
			}
			
					
			
			
							
    };
 
     var data4 = [
	   /*{label: 'Alunos sem confirmação', data: [<?=trim($RELATORIO3_SEM_CONFIRMACAO,',')?>]},*/
	   {label: 'Alunos confirmados via SMS', data: [<?=trim($RELATORIO3_TOTAL_SMS,',')?>]},
	   {label: 'Alunos confirmados via APP', data: [<?=trim($RELATORIO3_TOTAL_APP,',')?>]},
	   {label: 'Alunos confirmados via WEB', data: [<?=trim($RELATORIO3_TOTAL_WEB,',')?>]}
    ];

	
    $.plot($("#grafico4"), data4, options);  




	
	$("#grafico4").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;

				$("#tooltip").remove();
				var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);

				showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' '+ item.series.label);
				///alert('data_point:' + item.datapoint);
			}
		} else {
			$("#tooltip").remove();
			previousPoint = null;
		}
	});


	$('#grafico4').bind("mouseleave", function () {
		$("#tooltip").remove();
	});
	
});
</script>
<script>
jQuery(document).ready(function()
	{       
	// initiate layout and plugins
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	QuickSidebar.init() // init quick sidebar
	ComponentsPickers.init();
	ComponentsDropdowns.init();
	 //Charts.init();
	 //Charts.initCharts();
	 //Charts.initPieCharts();
	 //Charts.initBarCharts();
	
	
	<? if ($LISTBOX_UNIDADES != '') { ?>
	$('#ajax_unidade_ensino').hide();
	<? } ?>
	
	$('#reportrange span').html('<?=$DAT_COMPLETA?>');
	$('#dat_inicio').val('<?=$DAT_INICIO?>');
	$('#dat_fim').val('<?=$DAT_FIM?>');
	$('#dat_completa').val('<?=$DAT_COMPLETA?>');
	
	//AJAX DO UNIDADE DE ENSINO (ativado quando o cliente muda)
	$('#search_category_id').change(function(){
		CarregaUnidade();
		return false;
	});
	//AJAX END
	});   


function CarregaUnidade(){

	  if ($('#search_category_id').val() == 0)
		return false;
		
	  $('#ajax_unidade_ensino').show();
	  $('#input_unidade_ensino').attr("value", 'Carregando...');
	  $('#ajax_unidade_ensino_final').html('');
	  
	  $.post("../exec/?e=ajax_unidadeensino", {
		id: $('#search_category_id').val(),
		}, function(response){
			$('#ajax_unidade_ensino_final').html(response);
			$('#ajax_unidade_ensino').hide();
		});
	} 		

	$('#search_category_id').change(function(){ parent.location='?id_cliente='+$('#search_category_id').val() });	
	
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>