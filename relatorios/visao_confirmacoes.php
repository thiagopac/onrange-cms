<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('visao_confirmacoes');

#INPUTS
	$PESQUISA     = addslashes(trim($_REQUEST['pesquisa']));
	$DAT_INICIO   = addslashes($_REQUEST['dat_inicio']);
	$DAT_FIM 	= addslashes($_REQUEST['dat_fim']);
	$DAT_COMPLETA = addslashes($_REQUEST['dat_completa']);
	$ID_CLIENTE	= (int)$_REQUEST['id_cliente'];
	$TIPO_ALUNO	= (int)$_REQUEST['tipo_aluno'];
	$QUEBRA		= addslashes($_REQUEST['quebra']);
	$ID_UNIDADE_ENSINO = $_REQUEST['id_unidade_ensino']; //VEM UM ARRAY AQUI
		
	$menos30dias = time( ) - 86400 * (30 - 1); //(30 - 1) sao 30 dias!
	
	if ($DAT_INICIO == '') 	$DAT_INICIO = date('Y-m-d',$menos30dias);
	if ($DAT_FIM == '') 		$DAT_FIM = date('Y-m-d');
	if ($DAT_COMPLETA == '')	$DAT_COMPLETA = date('d/m/Y',$menos30dias).' - '.date('d/m/Y');
	
	$MSG = '';

#É IE?
	if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || ((strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/') !== false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'rv:') !== false)))
		$BrowserIE = true;
	
#INICIO LOGICA
	$DB = fnDBConn();
	
	list($ID_CLIENTE,$LISTBOX_CLIENTES) = fnSELECT_CLIENT($DB,$ID_CLIENTE);
	
#CONECTA NO BANCO DO CLIENTE
	if ($ID_CLIENTE > 1)
		{
		$SQL = "select * from cliente where id = $ID_CLIENTE";
		$RET = fnDB_DO_SELECT($DB,$SQL);
		
		list($ERRO,$DB_CLI) = fnDBConn_CLIENTE($RET['params']);
		if ($ERRO == 'ERRO')
			$MSG = $DB_CLI;
		}
	
#PUXA OS DADOS DO BANCO DO CLIENTE
	if (($ID_CLIENTE > 1) && ($MSG == ''))
		{
		$ID_UNIDADE_ENSINO_FINAL = "0";
		
		$SQL = "select id,nome from UNIDADE_ENSINO WHERE id <> 111 AND nome NOT LIKE '%emotion%' order by nome";
		$RET = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);	

		$LISTBOX_UNIDADES = '<select class="bs-select form-control" name="id_unidade_ensino[]" id="sub_category_id" multiple>';
		
		foreach($RET as $KEY => $ROW)
			{
			$sel = '';
			if (in_array((int)$ROW['ID'],$ID_UNIDADE_ENSINO))
				{
				$sel = 'selected';
				$ID_UNIDADE_ENSINO_FINAL .= ','.(int)$ROW['ID'];
				}
			
			$LISTBOX_UNIDADES .= '<option '.$sel.' value="'.$ROW['ID'].'">'.$ROW['NOME'].'</option>';
			}

		$LISTBOX_UNIDADES .= '</select>';
		}
		
#PUXA OS DADOS DO BANCO DO CLIENTE
	if (($ID_CLIENTE > 1) && ($MSG == ''))
		{
		if ($TIPO_ALUNO == 1) $SQL_CALOURO = "and (ALUNO.calouro = 1)";
		if ($TIPO_ALUNO == 2) $SQL_CALOURO = "and (ALUNO.calouro = 0)";
		
		if ($QUEBRA == '') $QUEBRA = 'dia_confirmacao';
		
		//if ($QUEBRA == 'ano_mes') 			$QUEBRA_NOME = 'Mês/Ano';
		if ($QUEBRA == 'tipo_aluno')		$QUEBRA_NOME = 'Tipo do Aluno';
		if ($QUEBRA == 'unidade_ensino')	$QUEBRA_NOME = 'Unidade de Ensino';
		if ($QUEBRA == 'dia_confirmacao')	$QUEBRA_NOME = 'Dia em que foi confirmado';
		
			
		//if ($QUEBRA == 'ano_mes') 			$SQL_QUEBRA = "concat(LISTA_ALUNO.ano,'-',LISTA_ALUNO.mes) orderr, concat(LISTA_ALUNO.mes,'/',LISTA_ALUNO.ano) as 1a_coluna";
		if ($QUEBRA == 'tipo_aluno')		$SQL_QUEBRA = "case when ALUNO.calouro = 1 then 'Calouro' else 'Veterano' end as 1a_coluna";
		if ($QUEBRA == 'unidade_ensino')	$SQL_QUEBRA = "UNIDADE_ENSINO.nome as 1a_coluna";
		if ($QUEBRA == 'dia_confirmacao')	$SQL_QUEBRA = "DATE_FORMAT(date(LISTA_ALUNO.dtconfirmacao),'%d/%m') as 1a_coluna";
		
		/*$SQL = "
		select $SQL_QUEBRA,
			count(distinct LISTA_ALUNO.idaluno) TOTAL,
			count(distinct case when ifnull(LISTA_ALUNO.idtipo,0) in (0) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS_SEM_CONFIRMAR,
			count(distinct case when LISTA_ALUNO.idtipo in (1)  then LISTA_ALUNO.idaluno else null end) TOTAL_SMS,
			count(distinct case when LISTA_ALUNO.idtipo in (2)  then LISTA_ALUNO.idaluno else null end) TOTAL_APP,
			count(distinct case when LISTA_ALUNO.idtipo in (3)  then LISTA_ALUNO.idaluno else null end) TOTAL_WEB,
			count(distinct case when LISTA_ALUNO.idtipo in (1,2,3) then LISTA_ALUNO.idaluno else null end) TOTAL_ALUNOS
		from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO, ALUNO, UNIDADE_ENSINO
		where (LISTA_ALUNO.idtipo in (0,1,2,3) or LISTA_ALUNO.idtipo is null)
		  and LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
		  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
		  and LISTA_ALUNO.idaluno = ALUNO.id
		  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
		  and LISTA_ALUNO.idaluno NOT IN ($USUARIOS_TESTES)
		  and ('$ID_UNIDADE_ENSINO_FINAL' = '0' or UNIDADE_CURSO.idunidade in ($ID_UNIDADE_ENSINO_FINAL)) #Cada cliente tera seus IDs de UNIDADE DE ENSINO
		  and (LISTA_ALUNO.dtconfirmacao is null or LISTA_ALUNO.dtconfirmacao between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59')
		  $SQL_CALOURO
		group by 1
		order by 1 desc";*/
		
		$SQL = "select $SQL_QUEBRA,
				 count(distinct LISTA_ALUNO.id) TOTAL_CONFIRMACOES,
				 count(distinct (case when LISTA_ALUNO.idtipo = 1 then LISTA_ALUNO.id else null end)) TOTAL_SMS,
				 count(distinct (case when LISTA_ALUNO.idtipo = 2 then LISTA_ALUNO.id else null end)) TOTAL_APP,
				 count(distinct (case when LISTA_ALUNO.idtipo = 3 then LISTA_ALUNO.id else null end)) TOTAL_WEB,
				 case when LISTA_ALUNO.dtconfirmacao >= '2014-06-01 00:00:00' then 'SIM' else 'NAO' end corte
				from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO, ALUNO, UNIDADE_ENSINO
				where LISTA_ALUNO.idtipo is not null and LISTA_ALUNO.idtipo in (1,2,3)
				  and LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
				  and LISTA_ALUNO.idaluno = ALUNO.id
				  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
				  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
				  and LISTA_ALUNO.idaluno NOT IN ($USUARIOS_TESTES)
				  and ('$ID_UNIDADE_ENSINO_FINAL' = '0' or UNIDADE_CURSO.idunidade in ($ID_UNIDADE_ENSINO_FINAL)) #Cada cliente tera seus IDs de UNIDADE DE ENSINO
				  and (LISTA_ALUNO.dtconfirmacao is null or LISTA_ALUNO.dtconfirmacao between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59')
				  $SQL_CALOURO
				group by 1
				order by LISTA_ALUNO.dtconfirmacao desc";
		
		$RET = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);		
		
		//--------------------------------------------
		if ($QUEBRA == 'dia_confirmacao')
			{
			/*$SQL = "
			select min(LISTA_ALUNO.dtconfirmacao) into @DATA_MINIMA
			from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO
			where LISTA_ALUNO.idtipo is not null and LISTA_ALUNO.idtipo in (1,2,3)
				  and LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
				  and LISTA_ALUNO.idaluno = ALUNO.id
				  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
				  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
				  and LISTA_ALUNO.idaluno NOT IN ($USUARIOS_TESTES)
				  and ('$ID_UNIDADE_ENSINO_FINAL' = '0' or UNIDADE_CURSO.idunidade in ($ID_UNIDADE_ENSINO_FINAL)) #Cada cliente tera seus IDs de UNIDADE DE ENSINO
				  and (LISTA_ALUNO.dtconfirmacao is null or LISTA_ALUNO.dtconfirmacao between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59')
				  $SQL_CALOURO
			";
			fnDB_DO_EXEC($DB_CLI, $SQL);	*/
			
			//--------------------------------------------
			$SQL = "
			select count(distinct LISTA_ALUNO.id) confirmacoes_pendentes_desde_essa_epoca
			from LISTA_ALUNO, ALUNO_UNICURSO, UNIDADE_CURSO, ALUNO, UNIDADE_ENSINO
			where LISTA_ALUNO.idaluno = ALUNO_UNICURSO.idaluno
				  and LISTA_ALUNO.idaluno = ALUNO.id
				  and UNIDADE_CURSO.idunidade = UNIDADE_ENSINO.id
				  and UNIDADE_CURSO.id = ALUNO_UNICURSO.idunicurso
				  and LISTA_ALUNO.idaluno NOT IN ($USUARIOS_TESTES)
				  and ('$ID_UNIDADE_ENSINO_FINAL' = '0' or UNIDADE_CURSO.idunidade in ($ID_UNIDADE_ENSINO_FINAL)) #Cada cliente tera seus IDs de UNIDADE DE ENSINO
				  $SQL_CALOURO
				  and (idtipo is null or idtipo = 0 or (idtipo > 0 and dtconfirmacao >= '$DAT_INICIO 00:00:00'))
			";
			$TEMP = fnDB_DO_SELECT_WHILE($DB_CLI, $SQL);		
			$PENDENTES = (int)$TEMP[0]['confirmacoes_pendentes_desde_essa_epoca'];
			
			for ($x = (count($RET)-1); $x >= 0; $x--)
				{
				$CONFIRMADOS_NESSE_DIA = (int)$RET[$x]['TOTAL_CONFIRMACOES'];
				$PENDENTES = $PENDENTES - $CONFIRMADOS_NESSE_DIA;
				$RET[$x]['FALTAM_CONFIRMAR'] = $PENDENTES;
				}
				
			for ($x = (count($RET)-1); $x >= 0; $x--)
				{
				if ($RET[$x]['corte'] == 'NAO')
					unset($RET[$x]);
				
				unset($RET[$x]['corte']);
				}
			}
		//-------------------------------
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
					Visão das Confirmações <small></small>
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
							<input type="hidden" name="dat_inicio" id="dat_inicio" value="" />
							<input type="hidden" name="dat_fim" id="dat_fim" value="" />
							<input type="hidden" name="dat_completa" id="dat_completa" value="" />
								<div class="form-body">
								<? if ($MSG != '') { ?>
								<div class="alert alert-danger display">
									<button class="close" data-close="alert"></button>
									<?=$MSG?>
								</div>
								<? } ?>
								<div class="row form-group">
													<?=$LISTBOX_CLIENTES?>
													
													<div class="col-md-4">
														<label>Período em que os usuários confirmaram</label>
														<div id="reportrange" class="form-control">
															<i class="fa fa-calendar"></i>
															&nbsp; <span>June 1, 2014 - June 30, 2014</span>
															<b class="fa fa-angle-down"></b>
														</div>
													</div>
												</div>
								<div class="row form-group">
													<div class="col-md-4" id="show_sub_categories">
														<label>Unidade de Ensino</label>
															<div id="ajax_unidade_ensino">
																 <input type="text" name="aaaaa" class="form-control" id="input_unidade_ensino" value="Selecione...">
															</div>
															<div id="ajax_unidade_ensino_final">
															<?=$LISTBOX_UNIDADES;?>
															</div>
													</div>
													<div class="col-md-4">
														<label>Tipo de Aluno</label>
														<select class="form-control" name="tipo_aluno">
														<option value="0"></option>
														<option <? if ($TIPO_ALUNO == 1) echo 'selected'; ?> value="1">Calouro</option>
														<option <? if ($TIPO_ALUNO == 2) echo 'selected'; ?> value="2">Veterano</option>
														</select>
													</div>
												</div>	
								<div class="row form-group">
													<div class="col-md-4">
														<label>Quebrar Relatório por</label>
														<select class="form-control" name="quebra">
														<!--option <? if ($QUEBRA == 'ano_mes') echo 'selected'; ?> value="ano_mes">Ano e Mes de Referência</option-->
														<option <? if ($QUEBRA == 'tipo_aluno') echo 'selected'; ?> value="tipo_aluno">Tipo de Aluno</option>
														<option <? if ($QUEBRA == 'unidade_ensino') echo 'selected'; ?> value="unidade_ensino">Unidade de Ensino</option>
														<option <? if ($QUEBRA == 'dia_confirmacao') echo 'selected'; ?> value="dia_confirmacao">Dia em que foi confirmado</option>
														
														</select>
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
					<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
						</div>
						<div class="portlet-body flip-scroll">
							<table class="table table-bordered table-striped table-condensed flip-content" id="datatable">
							<thead class="flip-content">
							<tr>
								<th>
									 <?=$QUEBRA_NOME?>
								</th>
								<th>
									 Quantidade de Confirmações
								</th>
								<th class="numeric">
									 Confirmações via SMS
								</th>
								<th class="numeric">
									 Confirmações via APP
								</th>
								<th class="numeric">
									 Confirmações via WEB
								</th>
								<? if ($QUEBRA == 'dia_confirmacao') { ?>
								<th class="numeric">
									 Faltam Confirmar
								</th>
								<? } ?>
							</tr>
							</thead>
							<tbody>
							<?
							unset($TOTAIS);
							
							foreach($RET as $KEY => $ROW)
								{
								//Gera Totalizadores
								foreach($ROW as $KEY2 => $ROW2)
									{
									if ($KEY2 != '1a_coluna')
										$TOTAIS[$KEY2] = (int)$TOTAIS[$KEY2] + (int)$ROW2;
									}
									
								//Gera Relatorio2
								//$RELATORIO2 .= "{label: \"{$ROW['1a_coluna']}\", data:{$ROW['TOTAL_ALUNOS']}},"
								
								//Gera Relatorio3
								$N++;
								$RELATORIO3_BARRAS .= "[$N,'{$ROW['1a_coluna']}'],";
								$RELATORIO3_SEM_CONFIRMACAO .= "[$N,'{$ROW['TOTAL_ALUNOS_SEM_CONFIRMAR']}'],";
								$RELATORIO3_TOTAL_SMS .= "[$N,'{$ROW['TOTAL_SMS']}'],";
								$RELATORIO3_TOTAL_APP .= "[$N,'{$ROW['TOTAL_APP']}'],";
								$RELATORIO3_TOTAL_WEB .= "[$N,'{$ROW['TOTAL_WEB']}'],";
								?>
								<tr>
									<td>
										 <?=$ROW['1a_coluna']?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_CONFIRMACOES'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_SMS'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_APP'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_WEB'])?>
									</td>
									<? if ($QUEBRA == 'dia_confirmacao') { ?>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['FALTAM_CONFIRMAR'])?>
									</td>
									<? } ?>
								</tr>
								<?
								}

								unset($ROW);
								$ROW = $TOTAIS;
								?>
								<tr>
									<td><b>
										Totais 
									</b></td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_CONFIRMACOES'],'b')?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_SMS'],'b')?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_APP'],'b')?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['TOTAL'],$ROW['TOTAL_WEB'],'b')?>
									</td>
									<td>

									</td>
								</tr>
							</tbody>
							</table>
							
							<p align="right"><a href="#" class="btn red" id="exportExcel">Exportar Excel</a></p>
						
						</div>
						
				
					
					</div>
					<!-- END SAMPLE TABLE PORTLET-->
					
					
					
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
							<div class="caption">
							</div>
							<div class="tools">
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="javascript:;" class="reload">
								</a>
							</div>
						</div>
						<div class="portlet-body">
						<div id="grafico3" style="height: 300px"></div>
						</div>
					</div>
				</div>
			</div>
			<!-- END BASIC CHART PORTLET-->
			
			<!-- BEGIN PIE CHART PORTLET-->
			<div class="row">
				<div class="col-md-6">
					<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
							<div class="caption">
							</div>
							<div class="tools">
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
								<a href="javascript:;" class="reload">
								</a>
							</div>
						</div>
						<div class="portlet-body">
						<div id="grafico1" style="height: 300px"></div>
						</div>
					</div>
				</div>
				<!--div class="col-md-6">
					<div class="portlet box red">
						<div class="portlet-title_sem_titulo">
							<div class="caption">
							</div>
						</div>
						<div class="portlet-body">
							<div id="grafico3" style="height: 300px"></div>
						</div>
					</div>
				</div-->
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
	

	
    var data1 = [
		
      /*{label: "Alunos sem confirmação", data:<?=$ROW['TOTAL_ALUNOS_SEM_CONFIRMAR']?>},*/
	  {label: "Confirmações via SMS", data:<?=$ROW['TOTAL_SMS']?>},
      {label: "Confirmações via APP", data:<?=$ROW['TOTAL_APP']?>},
      {label: "Confirmações via WEB", data:<?=$ROW['TOTAL_WEB']?>}
       
    ];
	
    /*var data2 = [
	  <?=trim($RELATORIO2,',')?>
    ];*/
 
    var options = {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 3 / 4,
                                formatter: function (label, series) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + /*label + '<br/>' +*/ Math.round(series.percent) + '%</div>';
                                },
                                background: {
                                    opacity: 0.5,
                                    color: '#000'
                                }
                            }
                        }
                    }
         };
		 
    $.plot($("#grafico1"), data1, options);  


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
	   {label: 'Confirmações via SMS', data: [<?=trim($RELATORIO3_TOTAL_SMS,',')?>]},
	   {label: 'Confirmações via APP', data: [<?=trim($RELATORIO3_TOTAL_APP,',')?>]},
	   {label: 'Confirmações via WEB', data: [<?=trim($RELATORIO3_TOTAL_WEB,',')?>]}
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
				
				<? if ($QUEBRA == 'tipo_disparo') { ?>
					showChartTooltip(item.pageX, item.pageY, item.datapoint[0], (item.datapoint[1]-item.datapoint[2]) + ' '+ item.series.label);
				<? } else { ?>
					showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' '+ item.series.label);
				<? } ?>
				
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

		    $('#exportExcel').click(function() {
			  $.post("../exec/?e=exportExcel", {
				content: $('#datatable').html(),
				}, function(response){
					$("body").append("<iframe src='../exec/?e=exportExcel&download=1' style='display: none;' ></iframe>"); 
				});	
			
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
				$('.bs-select').selectpicker('refresh');
			});
			
			


		} 			
    </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>