<?
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('locais');

#INPUTS
	$MSG = addslashes($_REQUEST['msg']);
	$IDLOCAL  = (int)$_REQUEST['idlocal'];

#INICIO LOGICA
	$DB = fnDBConn();
	
	if ($IDLOCAL == -1)
		{
		$SQL = "INSERT INTO LOCAL (NOME, LATITUDE, LONGITUDE, DT_LOCAL, ID_USUARIO, ID_TIPO_LOCAL, DESTAQUE)
				VALUES ('temp', '0.0', '0.0', NOW(), 1, 1, 0)";
		$RET = fnDB_DO_EXEC($DB,$SQL);
		$IDLOCAL = (int)$RET[1];
		if ($IDLOCAL == 0)
			die('Falha geral. ID nao foi criado');
		}
		
	$SQL = "SELECT * FROM LOCAL WHERE ID_LOCAL = $IDLOCAL";
	$RET = fnDB_DO_SELECT($DB,$SQL);
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
<meta charset="utf-8"/>
<title><?=$TITULO?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
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
					Locais<small></small>
					</h3>
					
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE HEADER-->
			
									<? if ($MSG != '') { ?>
									<div class="alert alert-danger display">
										<button class="close" data-close="alert"></button>
										<?=$MSG?>
									</div>
									<? } ?>
								<div class="row">
				<div class="col-md-6">
					<!-- INICIO PORTLET FORMULARIO CRIAR LOCAL-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Criar ou alterar local
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
							</div>
						</div>
						<div class="portlet-body form">
										<!-- BEGIN FORM-->
										<form method="post" action="../exec/" id="form_sample_2" class="horizontal-form" novalidate="novalidate">
											<input type="hidden" name="e" id="e" value="local_edit" />
											<input type="hidden" name="idlocal" id="idlocal" value="<?=$IDLOCAL?>" />
											<div class="form-body">
												<h3 class="form-section">Dados do local</h3>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">Nome</label>
															<input type="text" id="nome" name="nome" class="form-control" placeholder="Nome do local" value="<?=$RET['NOME']?>">
														</div>
													</div>
													<!--/span-->

													<!--/span-->
												</div>
												<!--/row-->
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">Tipo de local</label>
															<select class="form-control"  id="idtipolocal" name="idtipolocal">
															<? if($RET['ID_TIPO_LOCAL']==1){
																   $um = "Selected";
																}else if($RET['ID_TIPO_LOCAL']==2){
																   $dois = "Selected";
																}else if($RET['ID_TIPO_LOCAL']==3){
																   $tres = "Selected";
																}else{
																   $quatro = "Selected";
																}
																?>
																<option value="1" <?=$um?> >Boate ou pub</option>
																<option value="2" <?=$dois?>>Bar ou restaurante</option>
																<option value="3" <?=$tres?>>Festa ou show</option>
																<option value="4" <?=$quatro?>>Local p&uacute;blico</option>
															</select>
															<span class="help-block">
															Altere o tipo de local </span>
														</div>
													</div>
												</div>
												<!--/row-->
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">Latitude</label>
															<input type="text" id="latitude" name="latitude" class="form-control" placeholder="Latitude" value="<?=$RET['LATITUDE']?>">
															<span class="help-block">
															Exemplo: -19.000000</span>
														</div>	
													</div>
												</div>
												<!--/row-->
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">Longitude</label>
															<input type="text" id="longitude" name="longitude" class="form-control" placeholder="Longitude" value="<?=$RET['LONGITUDE']?>">
															<span class="help-block">
															Exemplo: -43.000000</span>
														</div>	
													</div>
												</div>
												<!--/row-->
											</div>
											<div class="form-actions fluid">
												<div class="col-md-offset-7 col-md-12">
													<button type="submit" class="btn blue"><i class="fa fa-check"></i> Salvar</button>
													<button type="button" class="btn default" onClick="parent.location='index.php';"><i class="fa fa-chevron-left"></i> Voltar</button>
												</div>
											</div>
										</form>
										<!-- END FORM-->
									</div>
					</div>
					<!-- FIM PORTLET FORMULARIO CRIAR LOCAL -->
				</div>
				<div class="col-md-6">
				<!-- BEGIN MARKERS PORTLET-->
					<div class="portlet solid green">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Localização
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<div id="gmap_marker" class="gmaps">
							</div>
						</div>
					</div>
					<!-- END MARKERS PORTLET-->
				</div>
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
<script type="text/javascript" src="../assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script type="text/javascript" src="../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="../assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../assets/global/plugins/ckeditor/ckeditor.js"></script>`
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="../assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="../assets/admin/pages/scripts/components-form-tools.js"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {       
	// initiate layout and plugins
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	QuickSidebar.init() // init quick sidebar
	ComponentsFormTools.init();
	MapsGoogle.init();
});

var MapsGoogle = function () {

  var mapMarker = function () {
        var map = new GMaps({
           div: '#gmap_marker',
           lat: <?=$RET['LATITUDE']?>,
           lng:  <?=$RET['LONGITUDE']?>,
        });
        map.addMarker({
            lat: <?=$RET['LATITUDE']?>,
            lng:  <?=$RET['LONGITUDE']?>,
            title: 'Mapa local',
            infoWindow: {
                content: '<span style="color:#000"><?=$RET['NOME']?></span>'
            }
        });
        map.setZoom(16);
    }
    
    return {
        //main function to initiate map samples
        init: function () {
            mapMarker();
        }

    };

}();

</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>