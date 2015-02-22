<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('dashboard');
	
	$ID_CLIENTE = $_SESSION['ADMINISTRADOR']['ID'];
	$ID_TIPO_ADMIN = $_SESSION['ADMINISTRADOR']['ID_TIPO_ADMIN'];

#INPUTS
	$MSG = addslashes($_REQUEST['MSG']);
	$LOCAL_SELECIONADO = $_REQUEST['LOCAL_SELECIONADO'];

#INICIO LOGICA
	$DB = fnDBConn();
	
	if ($ID_TIPO_ADMIN == 2) {
		$SQL = "SELECT AL.ID_LOCAL, L.NOME FROM ADMINISTRADOR_LOCAL AL
		JOIN LOCAL L ON (AL.ID_LOCAL = L.ID_LOCAL)
		WHERE AL.ID_ADMINISTRADOR = $ID_CLIENTE
		AND L.DT_EXCLUSAO IS NOT NULL";
		
		$ADMINISTRADOR_LOCAL = fnDB_DO_SELECT_WHILE($DB,$SQL);
		
	}else if($ID_TIPO_ADMIN == 1){
		$SQL = "SELECT L.ID_LOCAL, L.NOME FROM LOCAL L
				WHERE L.DT_EXCLUSAO IS NOT NULL";
		
		$ADMINISTRADOR_LOCAL = fnDB_DO_SELECT_WHILE($DB,$SQL);
	}
	
	if (ISSET($LOCAL_SELECIONADO)) {
		
		//***** Quantidade de checkins no local selecionado
		
		$SQL = "SELECT LOCAL.ID_LOCAL, LOCAL.NOME, CHECKINS_CORRENTES.QT_CHECKIN
		FROM LOCAL JOIN CHECKINS_CORRENTES ON LOCAL.ID_LOCAL = CHECKINS_CORRENTES.ID_LOCAL
		WHERE LOCAL.ID_LOCAL = $LOCAL_SELECIONADO
		GROUP BY LOCAL.ID_LOCAL";
		
		$QT_CHECKIN = fnDB_DO_SELECT($DB,$SQL);
		
		if ($QT_CHECKIN['QT_CHECKIN'] == null) {
			$QT_CHECKIN = array("QT_CHECKIN" => "0");
		}
		
		//***** Quantidade de promos no mês atual
		
		$SQL = "SELECT COUNT(1) AS QT_PROMO
		FROM PROMO
		WHERE ID_LOCAL = $LOCAL_SELECIONADO
		  AND NOW() BETWEEN DT_DISPONIBILIZACAO AND DT_FIM";
		
		$QT_PROMO = fnDB_DO_SELECT($DB,$SQL);
		
		if ($QT_PROMO['QT_PROMO'] == null) {
			$QT_PROMO = array("QT_PROMO" => "0");
		}
		
		//***** Percentual de penetração
		
		//Determina a posição e o tipo do local
		
		$SQL = "SELECT ID_TIPO_LOCAL, LATITUDE,LONGITUDE
		FROM LOCAL
		WHERE ID_LOCAL = $LOCAL_SELECIONADO";
		
		$POS_LOCAL = fnDB_DO_SELECT($DB,$SQL);
		
		$LAT_LOCAL = $POS_LOCAL['LATITUDE'];
		$LONG_LOCAL = $POS_LOCAL['LONGITUDE'];
		
		$TIPO_LOCAL = $POS_LOCAL['ID_TIPO_LOCAL'];
		
		//Determina a região onde buscar por outros locais para a comparação. 
		//Por padrão o local estará no centro de um quadrado com o lado = $RAIO*2.
		
		$RAIO = 2;
		
		$MAXLAT = $LAT_LOCAL + rad2deg($RAIO/6371);
		$MINLAT = $LAT_LOCAL - rad2deg($RAIO/6371);
		$MAXLONG = $LONG_LOCAL + rad2deg($RAIO/6371/cos(deg2rad($LAT_LOCAL)));
		$MINLONG = $LONG_LOCAL - rad2deg($RAIO/6371/cos(deg2rad($LAT_LOCAL)));
		
		//Seleciona a quantidade total de checkins correntes do local selecionado
		
		$SQL = "SELECT CHECKINS_CORRENTES.qt_checkin AS TOTAL_CHECKINS_LOCAL
		FROM CHECKINS_CORRENTES
		WHERE
		ID_LOCAL = $LOCAL_SELECIONADO";
		
		$TOTAL_CHECKINS_LOCAL = fnDB_DO_SELECT($DB,$SQL);
		
		//Seleciona a quantidade total de checkins correntes dentro do range, cujo tipo é o mesmo
		
		$SQL = "SELECT SUM(CHECKINS_CORRENTES.qt_checkin) AS TOTAL_CHECKINS_REGIAO
				FROM CHECKINS_CORRENTES JOIN LOCAL USING (ID_LOCAL)
				WHERE
				CHECKINS_CORRENTES.qt_checkin > 0
				AND LOCAL.dt_exclusao IS NULL
				AND LOCAL.id_tipo_local = $TIPO_LOCAL
				AND LOCAL.latitude BETWEEN $MINLAT AND $MAXLAT
				AND LOCAL.longitude BETWEEN $MINLONG AND $MAXLONG";
		
		$TOTAL_CHECKINS_REGIAO = fnDB_DO_SELECT($DB,$SQL);
		
		if ($TOTAL_CHECKINS_LOCAL['TOTAL_CHECKINS_LOCAL'] == null || $TOTAL_CHECKINS_REGIAO['TOTAL_CHECKINS_REGIAO'] == null) {
			$PENETRACAO = 0;
		}
		else{
			$PENETRACAO = $TOTAL_CHECKINS_LOCAL['TOTAL_CHECKINS_LOCAL'] / $TOTAL_CHECKINS_REGIAO['TOTAL_CHECKINS_REGIAO'] * 100;
			$PENETRACAO = NUMBER_FORMAT($PENETRACAO,1);
		}
	}
?>
<!DOCTYPE html>
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
	href="../assets/global/plugins/bootstrap-select/bootstrap-select.min.css" />
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
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../assets/global/css/components.css" rel="stylesheet"
	type="text/css" />
<link href="../assets/global/css/plugins.css" rel="stylesheet"
	type="text/css" />
<link href="../assets/admin/layout/css/layout.css" rel="stylesheet"
	type="text/css" />
<link id="style_color"
	href="../assets/admin/layout/css/themes/default.css" rel="stylesheet"
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
					<div class="col-md-9">
						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<h3 class="page-title">
							Resumo <small>para <?=$_SESSION['ADMINISTRADOR']['NOME']?></small>
						</h3>
					</div>
			
					<div class="form-body">
					 	<div class="col-md-3">
					 		<form action="index.php" method="POST">
								<select onchange="this.form.submit()" class="bs-select form-control" name="LOCAL_SELECIONADO" id="LOCAL_SELECIONADO">
									<option id="0" value="0">Selecione um local</option>
									<?php 
									foreach($ADMINISTRADOR_LOCAL as $KEY => $ROW)
										{
										?>
										<option <? if ($LOCAL_SELECIONADO == $ROW['ID_LOCAL']) echo 'selected'; ?> id="<?=$ROW['ID_LOCAL']?>" value="<?=$ROW['ID_LOCAL']?>"><?=$ROW['NOME']?></option>
										<?
										}
									?>
								</select>
							</form>
						 </div>
					</div>
				</div>
				<!-- BEGIN DASHBOARD STATS -->
			<div class="row">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat blue-madison">
						<div class="visual">
							<i class="fa fa-check-circle-o"></i>
						</div>
						<div class="details">
							<div class="number">
							<? if ($LOCAL_SELECIONADO != null && ($LOCAL_SELECIONADO != '0' || $QT_CHECKIN['QT_CHECKIN'] != 0)) {
									echo $QT_CHECKIN['QT_CHECKIN'];
								}ELSE{
									echo "N/A";
								}
							?>
							</div>
							<div class="desc">
							<? if ($LOCAL_SELECIONADO != null && ($LOCAL_SELECIONADO != '0' || $QT_CHECKIN['QT_CHECKIN'] != 0)) {
									if($QT_CHECKIN['QT_CHECKIN'] == 1){
										echo 'Checkin agora';
									}else{
										echo 'Checkins agora';
									}
								}
							?>
								 
							</div>
						</div>
						<a class="more" href="#">
						O que é isso? <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat red-intense">
						<div class="visual">
							<i class="fa fa-tags"></i>
						</div>
						<div class="details">
							<div class="number">
								 <? 
								 	if ($LOCAL_SELECIONADO != null){ 
										if($LOCAL_SELECIONADO != '0'){ 
								 			if($QT_PROMO['QT_PROMO'] != 0) {
												echo $QT_PROMO['QT_PROMO'];
											}
											else{
												echo "0";
											}
										}else{
											echo "N/A";		
										}
								 	}
									else{
										echo "N/A";
									}

								?>
							</div>
							<div class="desc">
								<? 
								 	if ($LOCAL_SELECIONADO != null && $LOCAL_SELECIONADO != '0'){
										if($QT_PROMO['QT_PROMO'] == 1){
											echo "Promo ativo";
										}else{
											echo "Promos ativos";
										 }
								 	}	
								?>
								 
							</div>
						</div>
						<a class="more" href="#">
						O que é isso? <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat green-haze">
						<div class="visual">
							<i class="fa fa-bullhorn"></i>
						</div>
						<div class="details">
							<div class="number">
								 <? 
								 	if ($LOCAL_SELECIONADO != null){ 
										if($LOCAL_SELECIONADO != '0'){ 
								 			if($PENETRACAO != 0) {
												echo $PENETRACAO . '%';
											}
											else{
												echo "0%";
											}
										}else{
											echo "N/A";		
										}
								 	}
									else{
										echo "N/A";
									}

								?>
							</div>
							<div class="desc">
								 Tx. de penetração
							</div>
						</div>
						<a class="more" href="#">
						O que é isso? <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
					<div class="dashboard-stat purple-plum">
						<div class="visual">
							<i class="fa fa-globe"></i>
						</div>
						<div class="details">
							<div class="number">
								 N/A%
							</div>
							<div class="desc">
								 Popularidade
							</div>
						</div>
						<a class="more" href="#">
						O que é isso? <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
			</div>
			<!-- END DASHBOARD STATS -->
			</div>`
			
		</div>
	</div>
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2015 &copy; <?=$TITULO?>
	</div>
	<div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
	</div>
</div>
<!-- END FOOTER -->
	<!-- END FOOTER -->
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script> 
<![endif]-->
	<script src="../assets/global/plugins/jquery-1.11.0.min.js"
		type="text/javascript"></script>
	<script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js"
		type="text/javascript"></script>
	<!-- END CORE PLUGINS -->

	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script src="../assets/global/plugins/flot/jquery.flot.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="../assets/global/scripts/metronic.js"
		type="text/javascript"></script>
	<script src="../assets/admin/layout/scripts/layout.js"
		type="text/javascript"></script>
		<script src="../assets/admin/pages/scripts/components-pickers.js"></script>
		<script src="../assets/admin/pages/scripts/components-dropdowns.js"></script>
	<!-- END PAGE LEVEL SCRIPTS -->


	<script>
jQuery(document).ready(function()
	{       
	// initiate layout and plugins
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	ComponentsPickers.init();
	ComponentsDropdowns.init();
}
</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>