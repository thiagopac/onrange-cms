<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('listarpromos');

#INICIO LOGICA
	$DB = fnDBConn();
	$SQL = "SELECT P.ID_PROMO, L.NOME AS NOME_LOCAL, P.DT_INICIO, P.DT_FIM, P.NOME AS NOME_PROMO, P.DESCRICAO, P.PROMO_CHECKIN, SUM(CASE WHEN PUC.ID_USUARIO IS NOT NULL THEN 1 ELSE 0 END) AS UTILIZADOS, SUM(CASE WHEN PUC.ID_USUARIO IS NOT NULL THEN 0 ELSE 1 END) AS NAO_UTILIZADOS
			FROM PROMO P
			INNER JOIN LOCAL L ON (P.ID_LOCAL = L.ID_LOCAL)
			INNER JOIN PROMO_CODIGO_USUARIO PUC ON(P.ID_PROMO = PUC.ID_PROMO)
			GROUP BY P.ID_PROMO
			ORDER BY ID_PROMO DESC";
	$RET = fnDB_DO_SELECT_WHILE($DB,$SQL);

#� IE?
	if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/') !== false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'rv:') !== false)))
		$BrowserIE = true;

?>
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
 <style type="text/css" media="print">
#lb1{
	display:none;
}
   
#lb2{
    display: block;
}

</style>
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

			<div id="lb1" class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
					Listar promos <small>Clique no botão <i>ver</i> para abrir os códigos</small>
					</h3>
					<!--button type="button" class="btn red" style="right: 15px; position: absolute; margin-top: -40px" onClick="parent.location='novo.php'">Novo Cliente</button-->
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE HEADER-->
			
					<!-- BEGIN SAMPLE TABLE PORTLET-->
						<div class="portlet-title_sem_titulo">
						</div>
						<div class="portlet-body flip-scroll" id="lb1">
							<table class="table table-bordered table-striped table-condensed flip-content" id="datatable">
							<thead class="flip-content">
							<tr>
								<th width="130px" style="text-align:center;">
									 Local
								</th>
								<th width="160px" style="text-align:center;">
									 Nome
								</th>
								<th style="text-align:center;">
									 Início
								</th>
								<th style="text-align:center;">
									 Fim
								</th>
								<th style="text-align:center;">
									 Descrição
								</th>
								<th style="text-align:center;">
									 Utilizados
								</th>
								<th style="text-align:center;" colspan=2>
									 Disponíveis
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							foreach($RET as $KEY => $ROW)
								{
								?>
								<tr>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['NOME_LOCAL']?>
									</td>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['NOME_PROMO']?>
									</td>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['DT_INICIO']?>
									</td>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['DT_FIM']?>
									</td>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['DESCRICAO']?>
									</td>
									<td align="center" style="vertical-align:middle;">
										 <?=$ROW['UTILIZADOS']?>
									</td>
									<td align="center" style="vertical-align:middle;" width="60px">
										 <?=$ROW['NAO_UTILIZADOS']?>
									</td>
									<td align="center" style="vertical-align:middle;">
									<?php if($ROW['NAO_UTILIZADOS']!=0){?>
									<a class="btn default" data-toggle="modal" href="<?='#'.$ROW['ID_PROMO']?>">Ver</a>
									<?php } ?>
									</td>
								</tr>
								
								<?
								} 
								?>
							</tbody>
							</table>
							
						</div>
						
			<?php
			foreach($RET as $KEY => $ROW){
			?>
				<!-- /.modal --><div id="lb2">
							<div class="modal fade" id="<?=$ROW['ID_PROMO']?>" tabindex="-1" role="basic" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
											<h4 class="modal-title"><?=$ROW['NOME_PROMO']?></h4>
										</div>
										<div class="modal-body">
										<table class="table table-bordered table-striped table-condensed flip-content" id="datatable">
											<thead class="flip-content">
											<tr>
												<th style="text-align:center;">
													 Ordem
												</th>
												<th style="text-align:center;">
													 Código
												</th>
												</tr></thead>
												<tbody>
										<?php
										    $DB = fnDBConn();
											$ID_PROMO = $ROW['ID_PROMO'];
											$SQL = "SELECT PROMO_CODIGO FROM PROMO_CODIGO_USUARIO WHERE ID_PROMO = $ID_PROMO AND ID_USUARIO IS NULL";
											$RET = fnDB_DO_SELECT_WHILE($DB,$SQL);
											$contCodigos = count($RET); //quantidade de códigos do promo
											$quantAgrupadores = (int) ($contCodigos / 10); //isso aqui faz a divisao e pega apenas a parte inteira da divisão, para saber quando criar a linha de agrupador
											while($quantAgrupadores != 0){
												if(($quantAgrupadores*10) %10 == 0) { //checa se o numero é divisível por 10 com resto 0
													$arrImpressores[] = $quantAgrupadores*10; //se for, ele adiciona a um array
													$arrImpressores[] = 0; //adicionamos tb o numero 0, para que tenha agregador desde a 1a impressao
												}
												$quantAgrupadores--; //vamos decrecentando o contador até 0
											}
											
											$contChecaImpressor = 0; //contador que checa se estamos em um número que se deve imprimir a linha do agregador
											
											foreach($RET as $KEY => $ROW){

												$agrupador = $ROW['PROMO_CODIGO'][0]; //pega o agrupador do banco, pelo primeiro caractere da string do codigo de promo												
												$codigo = substr_replace($ROW['PROMO_CODIGO'],'',0,2);//pegando o resto do código sem o agrupador e sem o hífen
												
												$ROWSPAN = "<tr><td rowspan=11 align='center' style='vertical-align:middle;'>$agrupador</td></tr>"; //imprimindo o agrupador com um rowspan de 11, deixando espaço pra coluna de cabeçalho
	
												if (in_array($contChecaImpressor, $arrImpressores, true)) { //se nosso contador que checa se é hora de imprimir o agrupador estiver dentro de um dos números dos array, ele imprime
													echo $ROWSPAN; //imprime a linha do agregador
												}
												
												$contChecaImpressor++; //aumentamos o contador que checa se é hora de imprimir o agregador
												
												echo "<tr><td align='center'>".$codigo."</tr></td>"; //imprime a linha com o código do promo
											}
										?>
										</tbody>
										</table>
										
										</div>
										<div class="modal-footer" id="lb1">
											<button type="button" class="btn default" data-dismiss="modal">Fechar</button>
											<button type="button" class="btn blue"  onClick="window.open('./impressaocodigos.php?promo=<?=$ID_PROMO?>', '_blank')"><i class="fa fa-print"></i> Imprimir</button>
										</div>
									</div>
									<!-- /.modal-content -->
								</div>
								<!-- /.modal-dialog -->
							</div>
							</div>
							<!-- /.modal -->
			<?
			} 
			?>
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
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
        jQuery(document).ready(function()
			{       
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