<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('consumo_sms');

#INPUTS
	$PESQUISA     = addslashes(trim($_REQUEST['pesquisa']));
	$DAT_INICIO   = addslashes($_REQUEST['dat_inicio']);
	$DAT_FIM 	= addslashes($_REQUEST['dat_fim']);
	$DAT_COMPLETA = addslashes($_REQUEST['dat_completa']);
	$ID_CLIENTE	= (int)$_REQUEST['id_cliente'];
	//$TIPO_ALUNO	= (int)$_REQUEST['tipo_aluno'];
	$QUEBRA		= addslashes($_REQUEST['quebra']);
	$INSTITUICAO		= $_REQUEST['instituicao'][0];
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
	
#MONTA LISTBOX
	//Default
	if (count($ID_UNIDADE_ENSINO) == 0)
		$ID_UNIDADE_ENSINO = array('confirmacao','catequizacao');
		
	unset($RET);
	
	$RET[0]['ID'] = 'confirmacao';
	$RET[0]['NOME'] = 'Confirmação';
	
	$RET[1]['ID'] = 'catequizacao';
	$RET[1]['NOME'] = 'Catequização';
	
	$LISTBOX_UNIDADES = '<select class="bs-select form-control" name="id_unidade_ensino[]" id="sub_category_id" multiple>';
	
	foreach($RET as $KEY => $ROW)
		{
		$sel = '';
		if (in_array($ROW['ID'],$ID_UNIDADE_ENSINO))
			$sel = 'selected';
		
		$LISTBOX_UNIDADES .= '<option '.$sel.' value="'.$ROW['ID'].'">'.$ROW['NOME'].'</option>';
		}

	$LISTBOX_UNIDADES .= '</select>';
		
#PUXA OS DADOS DO BANCO DO CLIENTE
	if (($ID_CLIENTE > 1) && ($MSG == ''))
		{
		if ($QUEBRA == '') $QUEBRA = 'dia';
		
		if ($QUEBRA == 'dia') 		$QUEBRA_NOME = 'Dia';
		if ($QUEBRA == 'mes')			$QUEBRA_NOME = 'Mês';
		if ($QUEBRA == 'tipo_disparo')	$QUEBRA_NOME = 'Tipo Disparo';
			
		if ($QUEBRA == 'dia') 		{ $SQL_QUEBRA1 = "date(din_sched) orderr, DATE_FORMAT(din_sched,'%d/%m') 1a_coluna,"; 		$SQL_QUEBRA2 = "date(MENSAGEM.dtenvio) orderr, DATE_FORMAT(MENSAGEM.dtenvio,'%d/%m') 1a_coluna,";		$SQL_QUEBRA3 = "date(dtenvio) orderr, DATE_FORMAT(dtenvio,'%d/%m') 1a_coluna,"; }
		if ($QUEBRA == 'mes')			{ $SQL_QUEBRA1 = "left(din_sched,7) orderr, DATE_FORMAT(din_sched,'%m/%y') 1a_coluna,"; 		$SQL_QUEBRA2 = "left(MENSAGEM.dtenvio,7) orderr, DATE_FORMAT(MENSAGEM.dtenvio,'%m/%y') 1a_coluna,";		$SQL_QUEBRA3 = "left(dtenvio,7) orderr, DATE_FORMAT(dtenvio,'%m/%y') 1a_coluna,"; }
		if ($QUEBRA == 'tipo_disparo')	{ $SQL_QUEBRA1 = "1 orderr, 'Catequização' 1a_coluna,";						$SQL_QUEBRA2 = "2 orderr, 'Confirmação' 1a_coluna,";								$SQL_QUEBRA3 = "2 orderr, 'Confirmação' 1a_coluna,"; }
		
		/*
		if ($QUEBRA == 'dia') 		{ $SQL_QUEBRA1 = "date(din_sched) orderr, DATE_FORMAT(din_sched,'%d/%m') 1a_coluna,"; 		$SQL_QUEBRA2 = "date(DISPARO.date_to_send) orderr, DATE_FORMAT(DISPARO.date_to_send,'%d/%m') 1a_coluna,";		$SQL_QUEBRA3 = "date(dtcriacao) orderr, DATE_FORMAT(dtcriacao,'%d/%m') 1a_coluna,"; }
		if ($QUEBRA == 'mes')			{ $SQL_QUEBRA1 = "left(din_sched,7) orderr, DATE_FORMAT(din_sched,'%m/%y') 1a_coluna,"; 		$SQL_QUEBRA2 = "left(DISPARO.date_to_send,7) orderr, DATE_FORMAT(DISPARO.date_to_send,'%m/%y') 1a_coluna,";		$SQL_QUEBRA3 = "left(dtcriacao,7) orderr, DATE_FORMAT(dtcriacao,'%m/%y') 1a_coluna,"; }
		if ($QUEBRA == 'tipo_disparo')	{ $SQL_QUEBRA1 = "1 orderr, 'Catequização' 1a_coluna,";						$SQL_QUEBRA2 = "2 orderr, 'Confirmação' 1a_coluna,";								$SQL_QUEBRA3 = "2 orderr, 'Confirmação' 1a_coluna,"; }
		*/
		
                
                
		if (in_array('catequizacao',$ID_UNIDADE_ENSINO))
		{
                        $QTDE_BASE_SELETED = 'qtde_base'; // qtde_base (todos)
                    
                        if($INSTITUICAO == 1) // qtde_base_1 (kroton)
                        {
                            $QTDE_BASE_SELETED = 'qtde_base_1';
                        }
                        if($INSTITUICAO == 2) // qtde_base (anhanguera)
                        {
                            $QTDE_BASE_SELETED = 'qtde_base_2';
                        }
                        
			$SQL = "
			select $SQL_QUEBRA1
				   sum($QTDE_BASE_SELETED) cnt_mensagens,
				   sum($QTDE_BASE_SELETED) mensagens_enviadas,
				   0 mensagens_na_fila,
				   0 mensagens_nao_entregues
			from disparo
			where din_sched between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59' and tipo_disparo = 'catequizacao' and status <> 0 and (id_cliente = $ID_CLIENTE)
			group by 2";
                        
			$RET1 = fnDB_DO_SELECT_WHILE($DB,$SQL);		
			}
			
		if (in_array('confirmacao',$ID_UNIDADE_ENSINO))
			{
                    
                        $SQL_MENSAGEM = ($ID_CLIENTE === 2 ? 'and MENSAGEM.id > 966008' : '');
                    
                        if($INSTITUICAO)
                        {
                            $SQL_INSTITUICAO_1 = ", INSTITUICAO_LISTA_ALUNO";
                            $SQL_INSTITUICAO_2 = "and MENSAGEM.idlistaaluno = INSTITUICAO_LISTA_ALUNO.idlistaaluno
                                                    and INSTITUICAO_LISTA_ALUNO.idinstituicao = $INSTITUICAO";
                            
                            $SQL_INSTITUICAO_RECEBIDO_1 = ", INSTITUICAO_NUMERO";
                            $SQL_INSTITUICAO_RECEBIDO_2 = "and SMS_RECEBIDO.remetente = INSTITUICAO_NUMERO.numero
                                                            and INSTITUICAO_NUMERO.idinstituicao = $INSTITUICAO";
                        }
//                            die(var_dump($INSTITUICAO));
                        
			$SQL = "
                        select $SQL_QUEBRA2
				count(MENSAGEM.id) cnt_mensagens,
				sum(case when (MENSAGEM.status in (2) and ifnull(statusope,0) <> 130) then 1 else 0 end) mensagens_enviadas,
				sum(case when MENSAGEM.status in (0,1,3) and MENSAGEM.tentativas <  5 and MENSAGEM.idtipo <> 2  then 1 else 0 end) mensagens_na_fila,
				sum(case when (MENSAGEM.status in (0,1,3) and (MENSAGEM.tentativas >= 5 or MENSAGEM.idtipo = 2)) or (MENSAGEM.statusope = 130) then 1 else 0 end) mensagens_nao_entregues
			from DISPARO, MENSAGEM $SQL_INSTITUICAO_1
			where MENSAGEM.dtenvio between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59'
			  and DISPARO.status > 0
			  and MENSAGEM.iddisparo = DISPARO.id
			  $SQL_MENSAGEM #A partir desse ID que lancamos o CMS... Antes disso o MENSAGEM.iddisparo nao ta preenchido mesmo, dá na mesma.					  
			  and MENSAGEM.status in (0,1,2,3)
                          $SQL_INSTITUICAO_2
			group by 2";

			$RET2 = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);

			$SQL = "
			select $SQL_QUEBRA3
				count(distinct idbroaker) sms_recebidos
			from SMS_RECEBIDO $SQL_INSTITUICAO_RECEBIDO_1
			where dtenvio between '$DAT_INICIO 00:00:00' and '$DAT_FIM 23:59:59'
                        $SQL_INSTITUICAO_RECEBIDO_2
			group by 2";
			
			$RET3 = fnDB_DO_SELECT_WHILE($DB_CLI,$SQL);
			}
		
		unset($RET_TOTAL);
		
		foreach($RET1 as $VALUE)
			{
			$TMP = 'cnt_mensagens'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_enviadas'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_na_fila'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_nao_entregues'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			
			$RET_TOTAL[ $VALUE['orderr'] ]['1a_coluna'] = $VALUE['1a_coluna'];
			}
			
		foreach($RET2 as $VALUE)
			{
			$TMP = 'cnt_mensagens'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_enviadas'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_na_fila'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			$TMP = 'mensagens_nao_entregues'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			
			$RET_TOTAL[ $VALUE['orderr'] ]['1a_coluna'] = $VALUE['1a_coluna'];
			}
			
		foreach($RET3 as $VALUE)
			{
			$TMP = 'sms_recebidos'; $RET_TOTAL[ $VALUE['orderr'] ][$TMP] = (int)$RET_TOTAL[ $VALUE['orderr'] ][$TMP] + (int)$VALUE[$TMP];
			
			$RET_TOTAL[ $VALUE['orderr'] ]['1a_coluna'] = $VALUE['1a_coluna'];
			}
			
			
		//Ordena o array
		krsort($RET_TOTAL);
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
					Consumo de SMS <small></small>
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
														<label>Período</label>
														<div id="reportrange" class="form-control">
															<i class="fa fa-calendar"></i>
															&nbsp; <span>June 1, 2014 - June 30, 2014</span>
															<b class="fa fa-angle-down"></b>
														</div>
													</div>
												</div>
								<div class="row form-group">
													<div class="col-md-4" id="show_sub_categories">
														<label>Tipo de Disparo</label>
															<div>
															<?=$LISTBOX_UNIDADES;?>
															</div>
													</div>
													<!--div class="col-md-4">
														<label>Tipo de Aluno</label>
														<select class="form-control" name="tipo_aluno">
														<option value="0"></option>
														<option <? if ($TIPO_ALUNO == 1) echo 'selected'; ?> value="1">Calouro</option>
														<option <? if ($TIPO_ALUNO == 2) echo 'selected'; ?> value="2">Veterano</option>
														</select>
													</div-->
                                                                                                        <?php
                                                                                                        if($ID_CLIENTE <= 2) // Referente ao ID da tabela CLIENTE, 2 refere-se a conexão com Kroton
                                                                                                        {
                                                                                                     ?>
                                                                                                                    <div class="col-md-4" id="show_sub_categories_1">
                                                                                                                            <label>Instituição</label>
                                                                                                                            <select class="bs-select form-control" name="instituicao[]" id="instituicao">
                                                                                                                            <?php

                                                                                                                            $insts = array(
                                                                                                                                0 => 'Todos',
                                                                                                                                2 => 'Anhanguera',
                                                                                                                                1 => 'Kroton'
                                                                                                                            );
                                                                                                                            
                                                                                                                            foreach($insts as $i => $instituicao)
                                                                                                                                    {
                                                                                                                                            $sel = ($i == $INSTITUICAO ? 'selected' : '');
                                                                                                                                        echo "<option $sel value=\"$i\">$instituicao</option>";
                                                                                                                                    }
                                                                                                                            ?>
                                                                                                                            </select>
                                                                                                                    </div>
                                                                                                    <?php 
                                                                                                        } 
                                                                                                    ?>                                                                
												</div>	
								<div class="row form-group">
													<div class="col-md-4">
														<label>Quebrar Relatório por</label>
														<select class="form-control" name="quebra">
														<option <? if ($QUEBRA == 'dia') echo 'selected'; ?> value="dia">Dia</option>
														<option <? if ($QUEBRA == 'mes') echo 'selected'; ?> value="mes">Mes</option>
														<option <? if ($QUEBRA == 'tipo_disparo') echo 'selected'; ?> value="tipo_disparo">Tipo de Disparo</option>
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
									 Mensagens Enviadas
								</th>
								<th class="numeric">
									 Não entregues
								</th>
								<th class="numeric">
									 Entregues
								</th>
								<th class="numeric">
									 Recebidas
								</th>
							</tr>
							</thead>
							<tbody>
							<?php
							unset($TOTAIS);
							
							foreach($RET_TOTAL as $KEY => $ROW)
								{
								$ROW['cnt_mensagens'] 		 = (int)$ROW['cnt_mensagens'];
								$ROW['mensagens_enviadas'] 	 = (int)$ROW['mensagens_enviadas'];
								$ROW['mensagens_nao_entregues'] = (int)$ROW['mensagens_nao_entregues'];
								$ROW['sms_recebidos'] 		 = (int)$ROW['sms_recebidos'];
								
								//Gera Totalizadores
								foreach($ROW as $KEY2 => $ROW2)
									{
									if ($KEY2 != '1a_coluna')
										$TOTAIS[$KEY2] = (int)$TOTAIS[$KEY2] + (int)$ROW2;
									}
									
								//Gera Relatorio3
								$N++;
								$RELATORIO3_BARRAS .= "[$N,'{$ROW['1a_coluna']}'],";
								$RELATORIO3_cnt_mensagens .= "[$N,'{$ROW['cnt_mensagens']}'],";
								$RELATORIO3_enviadas .= "[$N,'{$ROW['mensagens_enviadas']}'],";
								$RELATORIO3_nao_entregues .= "[$N,'{$ROW['mensagens_nao_entregues']}'],";
								$RELATORIO3_sms_recebidos .= "[$N,'{$ROW['sms_recebidos']}'],";
								?>
								<tr>
									<td>
										 <?=$ROW['1a_coluna']?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['cnt_mensagens'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['mensagens_nao_entregues'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['mensagens_enviadas'])?>
									</td>
									<td>
										 <?=fnFormataNumero(0,$ROW['sms_recebidos'])?>
									</td>
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
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['cnt_mensagens'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['mensagens_nao_entregues'])?>
									</td>
									<td>
										 <?=fnFormataNumero($ROW['cnt_mensagens'],$ROW['mensagens_enviadas'])?>
									</td>
									<td>
										 <?=fnFormataNumero(0,$ROW['sms_recebidos'])?>
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
	
    function GenerateSeries(added){
        var data = [];
        var start = 100 + added;
        var end = 500 + added;
 
        for(i=1;i<=20;i++){        
            var d = Math.floor(Math.random() * (end - start + 1) + start);        
            data.push([i, d]);
            start++;
            end++;
        }
 
        return data;
    }
	
	<? if ($QUEBRA == 'tipo_disparo') { ?>
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
	<? } else { ?>
var options = {
	
	
            series:{
                stack:false,
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
	<? } ?>
 
     var data3 = [
	   {label: 'Enviadas', data: [<?=trim($RELATORIO3_cnt_mensagens,',')?>]},
	   {label: 'Entregues', data: [<?=trim($RELATORIO3_enviadas,',')?>]},
	   {label: 'Não entregues', data: [<?=trim($RELATORIO3_nao_entregues,',')?>]},
	   {label: 'Recebidas', data: [<?=trim($RELATORIO3_sms_recebidos,',')?>]}
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
		
                  var id;
                        
                  if (!$('#search_category_id').val())
			id = <?=$ID_CLIENTE?>;
                  else
                        id = $('#search_category_id').val();

                  if(id > 2)
                      $('#show_sub_categories_1').hide();
                  else
                      $('#show_sub_categories_1').show();                
                
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