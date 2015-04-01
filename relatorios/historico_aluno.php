<?
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('historico_aluno');

#INPUTS
	$CPF 	     = preg_replace('/\D+/', '', $_REQUEST['pesquisa']);
	$ID_CLIENTE	= (int)$_REQUEST['id_cliente'];

	$MSG = '';

#INICIO LOGICA
	$DB = fnDBConn();
	
	list($ID_CLIENTE,$LISTBOX_CLIENTES) = fnSELECT_CLIENT($DB,$ID_CLIENTE);
	
#CONECTA NO BANCO DO CLIENTE
	if (($ID_CLIENTE > 1) && ($CPF != ''))
		{
		$SQL = "select * from cliente where id = $ID_CLIENTE";
		$RET = fnDB_DO_SELECT($DB,$SQL);
		
		list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
		if ($ERRO == 'ERRO')
			$MSG = $DB_CLI;
		}
		
#PUXA OS DADOS DO ALUNO
	if (($ID_CLIENTE > 1) && ($MSG == '') && ($CPF != ''))
		{
		$SQL = "select id,nome,cpf,senha,ifnull(calouro,0)+1 calouro,DATE_FORMAT(dtcriacao,'%d/%m/%Y %H:%i:%s') din 
			   from ALUNO
			   where cpf = '$CPF'";
				
		$RET_ALUNO = fnDB_DO_SELECT($DB_CLI,$SQL);		
		
		$ID_ALUNO = (int)$RET_ALUNO['ID'];
		
		if ($ID_ALUNO == 0)
			$MSG = "Nenhum aluno encontrado com o CPF: <b>$CPF</b>";
		}
		
#PUXA OS DADOS DO ALUNO
	if ($ID_ALUNO > 0)
		{
		$arINTERFACE_TIPO[1] = 'SMS';
		$arINTERFACE_TIPO[2] = 'APP';
		$arINTERFACE_TIPO[3] = 'WEB';
		
		$SQL = " select ano, mes, idtipo, DATE_FORMAT(dtconfirmacao,'%d/%m/%Y %H:%i:%s') din 
				from LISTA_ALUNO
				where idaluno = $ID_ALUNO
				order by ano desc, mes desc";
				
		$RET_LISTA_ALUNO = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	
		
		$SQL = " select UNIDADE_ENSINO.nome
				from ALUNO_UNICURSO,UNIDADE_CURSO,UNIDADE_ENSINO
				where ALUNO_UNICURSO.idaluno = $ID_ALUNO
				and ALUNO_UNICURSO.idunicurso = UNIDADE_CURSO.id
				and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
				group by ALUNO_UNICURSO.idaluno";
				
		$RET_UNIDADE_ENSINO = fnDB_DO_SELECT($DB_CLI,$SQL);	
		
		$SQL = " select distinct concat('(',substring(numero,3,2),') ',substring(numero,5,20)) numero from TELEFONE
				where idaluno = $ID_ALUNO order by principal desc";
				
		$RET_TELEFONES = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);
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
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 3.1
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
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
<link id="style_color" href="../assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
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
					Histórico de um Aluno <small></small>
					</h3>
					<!--button type="button" class="btn red" style="right: 15px; position: absolute; margin-top: -40px" onClick="parent.location='novo.php'">Novo Cliente</button-->
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE HEADER-->



<!-- ------------------ -->
<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
						</div>
						<div class="portlet-body form">
							<form role="form">
								<div class="form-body">
								<? if ($MSG != '') { ?>
								<div class="alert alert-danger display">
									<button class="close" data-close="alert"></button>
									<?=$MSG?>
								</div>
								<? } ?>
								<div class="row form-group">
													<?=$LISTBOX_CLIENTES?>
													
													<div class="col-md-3">
														<label>CPF</label>
														<input type="text" name="pesquisa" class="form-control" placeholder="Digite o CPF do aluno..." value="<?=$CPF?>">
													</div>
													
												</div>
								</div>
								<div class="form-actions2">
																	<button type="submit" class="btn red">Pesquisar</button>
								</div>
							</form>
						</div>
					</div>
<!-- ------------------ -->
			
			
					<!-- BEGIN SAMPLE TABLE PORTLET-->
					<div style="margin-bottom: 20px" class="portlet box red">
						<div class="portlet-title_sem_titulo">
						</div>
						<div class="portlet-body flip-scroll">
							<table style="margin-bottom: 0px" class="table table-bordered table-striped table-condensed flip-content" id="datatable">
							<thead class="flip-content">
							<tr>
								<th width="15%">
									 CPF
								</th>
								<td>
									 <?=$RET_ALUNO['cpf']?>
								</td>
								<th width="15%">
									 Telefone Principal
								</th>
								<td>
									 <?=$RET_TELEFONES[0]['numero']?>
								</td>
							</tr>
							<tr>
								<th>
									 Nome
								</th>
								<td>
									 <?=$RET_ALUNO['nome']?>
								</td>
								<th>
									 Telefone 2
								</th>
								<td>
									 <?=$RET_TELEFONES[1]['numero']?>
								</td>
							</tr>
							<tr>
								<th>
									 Senha
								</th>
								<td>
									 <?=base64_decode($RET_ALUNO['senha'])?>
								</td>
								<th>
									 Telefone 3
								</th>
								<td>
									 <?=$RET_TELEFONES[2]['numero']?>
								</td>
							</tr>
							<tr>
								<th>
									 Calouro?
								</th>
								<td>
									 <? if ((int)$RET_ALUNO['calouro'] == '1') echo 'Não'; ?>
									 <? if ((int)$RET_ALUNO['calouro'] == '2') echo 'Sim'; ?>
									 
								</td>
								<th>
									 Telefone 4
								</th>
								<td>
									 <?=$RET_TELEFONES[3]['numero']?>
								</td>
							</tr>
							<tr>
								<th>
									 Unidade de Ensino
								</th>
								<td>
									 <?=$RET_UNIDADE_ENSINO['nome']?>
								</td>
								<th>
									 Telefone 5
								</th>
								<td>
									 <?=$RET_TELEFONES[4]['numero']?>
								</td>
							</tr>
							<tr>
								<th>
									 Data de Cadastro
								</th>
								<td>
									 <?=$RET_ALUNO['din']?>
								</td>
								<th>
									 Telefone 6
								</th>
								<td>
									 <?=$RET_TELEFONES[5]['numero']?>
								</td>
							</tr>

							</thead>
							</table>
						</div>
					</div>
					<!-- END SAMPLE TABLE PORTLET-->
					
					<!-- BEGIN SAMPLE TABLE PORTLET-->
					<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
						</div>
						<div class="portlet-body flip-scroll">
							<table style="margin-bottom: 0px" class="table table-bordered table-striped table-condensed flip-content" id="datatable">
							<thead class="flip-content">
							<tr>
								<th width="20%">
									 Ano
								</th>
								<th>
									 Mes
								</th>
								<th class="numeric">
									 Confirmou Presença?
								</th>
								<th class="numeric">
									 Via Interface 
								</th>
								<th class="numeric">
									 Data da Confirmação
								</th>
							</tr>
							</thead>
							<tbody>
							<?
							foreach($RET_LISTA_ALUNO as $KEY => $ROW)
								{
								?>
								<tr>
									<td>
										 <?=$ROW['ano']?>
									</td>
									<td>
										 <?=$ROW['mes']?>
									</td>
									<td>
										<? if ((int)$ROW['idtipo'] > 0) echo 'Sim'; else echo 'Não'; ?>
									</td>
									<td>
										<?=$arINTERFACE_TIPO[(int)$ROW['idtipo']]?>
									</td>
									<td>
										 <?=$ROW['din']?>
									</td>
								</tr>
								<?
								}
							?>
							</tbody>
							</table>
						</div>
					</div>
					<!-- END SAMPLE TABLE PORTLET-->
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
<script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script type="text/javascript" src="../assets/outros/excellentexport.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
        jQuery(document).ready(function() {       
			// initiate layout and plugins
			Metronic.init(); // init metronic core components
			Layout.init(); // init current layout
			QuickSidebar.init() // init quick sidebar			
		});   
		
    </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>