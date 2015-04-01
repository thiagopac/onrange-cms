<?
// #INCLUDES
require_once ('../lib/config.php');

// CONTROLE SESSAO
fnInicia_Sessao ( 'sanitizacaolocal' );

#PEGA O RESULTADO DA FUNÇÃO
if($_POST[LocalDoador]){
	$DB = fnDBConn();
	$MSG = sanitizaLocal($DB,$_POST[LocalDoador],$_POST[LocalRecebedor]);
}

#INICIO LOGICA
$DB = fnDBConn();
$SQL = "SELECT ID_LOCAL, NOME FROM LOCAL WHERE DT_EXCLUSAO IS NULL ORDER BY NOME ASC";
$RET = fnDB_DO_SELECT_WHILE($DB,$SQL);
?>
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
<meta charset="utf-8" />
<title><?=$TITULO?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta content="" name="description" />
<meta content="" name="author" />
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link
	href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all"
	rel="stylesheet" type="text/css" />
<link
	href="../assets/global/plugins/font-awesome/css/font-awesome.min.css"
	rel="stylesheet" type="text/css" />
<link
	href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css"
	rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css"
	rel="stylesheet" type="text/css" />
<link href="../assets/global/plugins/uniform/css/uniform.default.css"
	rel="stylesheet" type="text/css" />
<link
	href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"
	rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/select2/select2.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/jquery-tags-input/jquery.tagsinput.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css"
	href="../assets/global/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
	
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-select/bootstrap-select.min.css"/>
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="../assets/global/plugins/jquery-multi-select/css/multi-select.css"/>
	
	
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->

<link href="../assets/global/css/components.css" rel="stylesheet"
	type="text/css" />
<link href="../assets/global/css/plugins.css" rel="stylesheet"
	type="text/css" />
<link href="../assets/admin/layout/css/layout.css" rel="stylesheet"
	type="text/css" />
<link id="style_color"
	href="../assets/admin/layout/css/themes/darkblue.css" rel="stylesheet"
	type="text/css" />
<link href="../assets/admin/layout/css/custom.css" rel="stylesheet"
	type="text/css" />
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico" />
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
				<a href="../dashboard/"> <img
					src="../assets/admin/layout/img/logo.png" alt="logo"
					class="logo-default" />
				</a>
				<div class="menu-toggler sidebar-toggler hide">
					<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
				</div>
			</div>
			<!-- END LOGO -->
			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<a href="javascript:;" class="menu-toggler responsive-toggler"
				data-toggle="collapse" data-target=".navbar-collapse"> </a>
			<!-- END RESPONSIVE MENU TOGGLER -->
		<? include('../_top.php'); ?>
	</div>
		<!-- END HEADER INNER -->
	</div>
	<!-- END HEADER -->
	<div class="clearfix"></div>
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
							Sanitização de local <small></small>
						</h3>
						<!--button type="button" class="btn red" style="right: 15px; position: absolute; margin-top: -40px" onClick="parent.location='novo.php'">Novo Cliente</button-->
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER-->
				
				<? if ($MSG != '') { ?>
								<div class="alert alert-danger display">
									<button class="close" data-close="alert"></button>
									<i class="fa-lg fa fa-warning"></i>
									<?=$MSG?>
								</div>
								<? } ?>
				

				<div class="tab-pane" id="tab_7">
					<div class="portlet box blue ">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Transferência de checkins
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse"> </a>
							</div>
						</div>
						<div class="portlet-body form">
							
                                                            <!-- BEGIN FORM-->
                                                            <form action="index.php" class="form-horizontal form-bordered form-label-stripped" method="POST">
                                                                    <div class="form-body">
                                                                            <div class="form-group">
                                                                                    <label class="control-label col-md-3">Local doador</label>
                                                                                    
                                                                                    <div class="col-md-4">
																						<select class="form-control input-xlarge select2me" data-placeholder="[ID] - Nome do local" id="LocalDoador" name="LocalDoador">
																							<option value=""></option>
																								<?
																								foreach($RET as $KEY => $ROW)
																										{
																									?>
																							<option value="<?=$ROW['ID_LOCAL']?>">[<?=$ROW['ID_LOCAL']?>] - <?=$ROW['NOME']?></option>
																									<?
																										}
																									?>
																						</select>
																						<span class="help-block">Este é o local que possui os checkins que serão transferidos</span>
																					</div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                    <label class="control-label col-md-3">Local recebedor</label>
                                                                                   
                                                                                     <div class="col-md-4">
																						<select class="form-control input-xlarge select2me" data-placeholder="[ID] - Nome do local" id="LocalRecebedor" name="LocalRecebedor">
																							<option value=""></option>
																								<?
																								foreach($RET as $KEY => $ROW)
																										{
																									?>
																							<option value="<?=$ROW['ID_LOCAL']?>">[<?=$ROW['ID_LOCAL']?>] - <?=$ROW['NOME']?></option>
																									<?
																										}
																									?>
																						</select>
																						<span class="help-block"> Este é o local que receberá os checkins de outro local</span>
																					</div>
                                                                                    
                                                                            </div>
                                                                    </div>
                                                                    <div class="form-actions fluid">
                                                                            <div class="row">
                                                                                    <div class="col-md-12">
                                                                                            <div class="col-md-offset-3 col-md-9">
                                                                                                    <button type="submit" class="btn green">
                                                                                                            <i class="fa fa-check"></i> Transferir
                                                                                                    </button>
                                                                                                    <button type="button" class="btn default">Cancelar</button>
                                                                                            </div>
                                                                                    </div>
                                                                            </div>
                                                                    </div>
                                                            </form>
                                                            <!-- END FORM-->
						</div>
					</div>
				</div>



				<!-- END CONTENT -->
			</div>
			<!-- END CONTAINER -->
			<!-- BEGIN FOOTER -->
			<div class="page-footer">
				<div class="page-footer-inner">
		 <?=date("Y"); ?> &copy; <?=$TITULO?>
	</div>
				<div class="page-footer-tools">
					<span class="go-top"> <i class="fa fa-angle-up"></i>
					</span>
				</div>
			</div>
			</div></div>
			<!-- END FOOTER -->
			<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
			<!-- BEGIN CORE PLUGINS -->
			<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
			<script src="../assets/global/plugins/jquery.min.js"
				type="text/javascript"></script>
			<script src="../assets/global/plugins/jquery-migrate.min.js"
				type="text/javascript"></script>
			<!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
			<script
				src="../assets/global/plugins/jquery-ui/jquery-ui.min.js"
				type="text/javascript"></script>
			<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js"
				type="text/javascript"></script>
			<script
				src="../assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"
				type="text/javascript"></script>
			<script
				src="../assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
				type="text/javascript"></script>
			<script src="../assets/global/plugins/jquery.blockui.min.js"
				type="text/javascript"></script>
			<script src="../assets/global/plugins/jquery.cokie.min.js"
				type="text/javascript"></script>
			<script src="../assets/global/plugins/uniform/jquery.uniform.min.js"
				type="text/javascript"></script>
			<script
				src="../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
				type="text/javascript"></script>
			<!-- END CORE PLUGINS -->
			<!-- BEGIN PAGE LEVEL PLUGINS -->
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/clockface/js/clockface.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
			<script type="text/javascript"
				src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
			<!-- END PAGE LEVEL PLUGINS -->
			<!-- BEGIN PAGE LEVEL SCRIPTS -->
			<script src="../assets/global/scripts/metronic.js"
				type="text/javascript"></script>
			<script src="../assets/admin/layout/scripts/layout.js"
				type="text/javascript"></script>
			<script src="../assets/admin/layout/scripts/quick-sidebar.js"
				type="text/javascript"></script>
			<script src="../assets/admin/pages/scripts/components-pickers.js"></script>
			
				<script type="text/javascript" src="../assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
				<script type="text/javascript" src="../assets/global/plugins/select2/select2.min.js"></script>
				<script type="text/javascript" src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
			<!-- END PAGE LEVEL SCRIPTS -->
			<script>
        jQuery(document).ready(function() {       
			// initiate layout and plugins
			Metronic.init(); // init metronic core components
			Layout.init(); // init current layout
			QuickSidebar.init() // init quick sidebar
			ComponentsPickers.init();
			
		});   
		
    </script>
			<!-- END JAVASCRIPTS -->

</body>
<!-- END BODY -->
</html>