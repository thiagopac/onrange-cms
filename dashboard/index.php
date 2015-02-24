<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('dashboard');
	
	$ID_CLIENTE = $_SESSION['ADMINISTRADOR']['ID'];
	$ID_TIPO_ADMIN = $_SESSION['ADMINISTRADOR']['ID_TIPO_ADMIN'];
	
	if(ISSET($_REQUEST['LOCAL_SELECIONADO'])){
		$LOCAL_SELECIONADO = $_REQUEST['LOCAL_SELECIONADO'];
		$_SESSION['LOCAL_SELECIONADO'] =  $_REQUEST['LOCAL_SELECIONADO'];
	}else{
		$LOCAL_SELECIONADO = $_SESSION['LOCAL_SELECIONADO'];
	}
	
#INPUTS
	$MSG = addslashes($_REQUEST['MSG']);

#INICIO LOGICA
	$DB = fnDBConn();
	
	if ($ID_TIPO_ADMIN == 2) {
		$SQL = "SELECT AL.ID_LOCAL, L.NOME FROM ADMINISTRADOR_LOCAL AL
		JOIN LOCAL L ON (AL.ID_LOCAL = L.ID_LOCAL)
		WHERE AL.ID_ADMINISTRADOR = $ID_CLIENTE
		AND L.DT_EXCLUSAO IS NULL
		ORDER BY L.NOME ASC";
		
		$ADMINISTRADOR_LOCAL = fnDB_DO_SELECT_WHILE($DB,$SQL);
		
	}else if($ID_TIPO_ADMIN == 1){
		$SQL = "SELECT L.ID_LOCAL, L.NOME FROM LOCAL L
				WHERE L.DT_EXCLUSAO IS NULL
				ORDER BY L.NOME ASC";
		
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
		
		if($LOCAL_SELECIONADO != 0){
		
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
		else{
			$PENETRACAO = 0;
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
						<a class="more" href="<? echo $_SERVER['PHP_SELF'] ."?indicador=checkin" ?>">
						detalhes <i class="m-icon-swapright m-icon-white"></i>
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
						<a class="more" href="<? echo $_SERVER['PHP_SELF'] ."?indicador=promo" ?>">
						detalhes <i class="m-icon-swapright m-icon-white"></i>
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
								<?	
								 if($LOCAL_SELECIONADO != null && ($LOCAL_SELECIONADO != '0' ||$PENETRACAO != 0)) {
									echo "Tx. de penetração";
								 }
								?>
							</div>
						</div>
						<a class="more" href="<? echo $_SERVER['PHP_SELF'] ."?indicador=penetracao" ?>">
						detalhes <i class="m-icon-swapright m-icon-white"></i>
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
						<a class="more" href="<? echo $_SERVER['PHP_SELF'] ."?indicador=popularidade" ?>">
						detalhes <i class="m-icon-swapright m-icon-white"></i>
						</a>
					</div>
				</div>
			</div>
			<!-- END DASHBOARD STATS -->
			<?php if ($_REQUEST['indicador'] == "checkin"){?>
			  <!-- INICIO GRAFICO DE CHECKINS -->
					<!-- BEGIN PORTLET-->
					<div class="portlet solid grey-cararra bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-bar-chart-o"></i>Checkins <small>- Ultimos 30 dias</small>
							</div>
						</div>
						<div class="portlet-body">
							<div id="site_activities_loading">
								<img src="../assets/admin/layout/img/loading.gif" alt="loading"/>
							</div>
							<div id="grafico_checkins_conteudo" class="display-none">
								<div id="grafico_checkins" style="height: 228px;">
								</div>
							</div>
						</div>
					</div>
					<!-- END PORTLET-->
			  <!-- FIM GRAFICO DE CHECKINS --> 
			<?php } else if($_REQUEST['indicador'] == "promo"){?>
			   Promos
			<?php } else if ($_REQUEST['indicador'] == "penetracao"){?>
			<!-- INICIO GRAFICO DE PENETRA��O -->
					<!-- BEGIN PORTLET-->
					<div class="portlet solid grey-cararra bordered">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-bullhorn"></i>Taxa de penetracao<small> - no momento</small>
							</div>
						</div>
						 <div class="portlet-body">
					<h4></h4>
					<div id="grafico_pizza_penetracao" class="chart">
					</div>
				</div>
					</div>
					<!-- END PORTLET-->
			<!-- FIM GRAFICO DE PENETRA��O -->
			<?php }else if ($_REQUEST['indicador'] == "popularidade"){?>
			   Popularidade
			<?php }?>
			</div>
			
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
<script src="../assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script>
<script src="../assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="../assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
<script src="../assets/global/plugins/fullcalendar/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="../assets/global/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>
	
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
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
});


//IN�CIO GR�FICO LINEAR - CHECKINS

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

if ($('#grafico_checkins').size() != 0) {
    //site activities
    var previousPoint2 = null;
    $('#site_activities_loading').hide();
    $('#grafico_checkins_conteudo').show();

    var data1 = [
        ['JAN 25', 91],
        ['JAN 26', 55],
        ['JAN 27', 67],
        ['JAN 28', 56],
        ['JAN 29', 88],
        ['JAN 31', 76],
        ['FEV 01', 99],
        ['FEV 02', 89],
        ['FEV 03', 56],
        ['FEV 04', 89],
        ['FEV 05', 56],
        ['FEV 06', 90],
        ['FEV 07', 97],
        ['FEV 08', 67],
        ['FEV 09', 67],
        ['FEV 10', 88],
        ['FEV 11', 58],
        ['FEV 12', 99],
        ['FEV 13', 65],
        ['FEV 14', 78],
        ['FEV 15', 35],
        ['FEV 16', 80],
        ['FEV 17', 53],
        ['FEV 18', 21],
        ['FEV 19', 56],
        ['FEV 20', 80],
        ['FEV 21', 105],
        ['FEV 22', 47],
        ['FEV 23', 82],
        ['FEV 24', 140]
        
    ];


    var plot_statistics = $.plot($("#grafico_checkins"),

        [{
            data: data1,
            lines: {
                fill: 0.2,
                lineWidth: 0,
            },
            color: ['#BAD9F5']
        }, {
            data: data1,
            points: {
                show: true,
                fill: true,
                radius: 4,
                fillColor: "#9ACAE6",
                lineWidth: 2
            },
            color: '#9ACAE6',
            shadowSize: 1
        }, {
            data: data1,
            lines: {
                show: true,
                fill: false,
                lineWidth: 3
            },
            color: '#9ACAE6',
            shadowSize: 0
        }],

        {

            xaxis: {
                tickLength: 0,
                tickDecimals: 0,
                mode: "categories",
                min: 0,
                font: {
                    lineHeight: 18,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            yaxis: {
                ticks: 5,
                tickDecimals: 0,
                tickColor: "#eee",
                font: {
                    lineHeight: 14,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            }
        });

    $("#grafico_checkins").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            if (previousPoint2 != item.dataIndex) {
                previousPoint2 = item.dataIndex;
                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                showChartTooltip(item.pageX-5, item.pageY, item.datapoint[0], item.datapoint[1] + ' checkins');
            }
        }
    });

    $('#grafico_checkins').bind("mouseleave", function () {
        $("#tooltip").remove();
    });
}

//FIM GR�FICO LINEAR - CHECKINS

//IN�CIO GR�FICO DE PIZZA - TAXA DE PENETRA��O
var data = [
            {
                data: 56,
                color:"#F7464A",
                label: "Lord Pub"
            },
            {
                data: 24,
                color: "#46BFBD",
                label: "Jack Rock Bar"
            },
            {
                data: 20,
                color: "#FDB45C",
                label: "Circus Rock Bar"
            }
        ];

// DEFAULT
$.plot($("#grafico_pizza_penetracao"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true,
            clickable: true
        }
    });
//IN�CIO GR�FICO DE PIZZA - TAXA DE PENETRA��O

</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>