<?php
##INCLUDES
	require_once('../lib/config.php');
	
#CONTROLE SESSAO
	fnInicia_Sessao('listarpromos');
	
	$ID_PROMO = $_REQUEST['promo'];
	
#INICIO LOGICA
	$DB = fnDBConn();
	$SQL = "SELECT P.ID_PROMO, L.NOME AS NOME_LOCAL, P.DT_INICIO, P.DT_FIM, P.NOME AS NOME_PROMO, P.DESCRICAO, P.PROMO_CHECKIN, SUM(CASE WHEN PUC.ID_USUARIO IS NOT NULL THEN 1 ELSE 0 END) AS UTILIZADOS, SUM(CASE WHEN PUC.ID_USUARIO IS NOT NULL THEN 0 ELSE 1 END) AS NAO_UTILIZADOS
			FROM PROMO P
			INNER JOIN LOCAL L ON (P.ID_LOCAL = L.ID_LOCAL)
			INNER JOIN PROMO_CODIGO_USUARIO PUC ON(P.ID_PROMO = PUC.ID_PROMO)
			WHERE P.ID_PROMO = $ID_PROMO
			GROUP BY P.ID_PROMO
			ORDER BY ID_PROMO DESC";
	$RET = fnDB_DO_SELECT($DB,$SQL);
?>
<html>
	<head>
		<title><?=$TITULO?></title>
		 <style>
table, th, td, tr {
    border: solid black 1px;
}
 </style>
<style type="text/css" media="print">
@page 
{
	size: auto;   /* auto is the initial value */
	margin: 0mm;  /* this affects the margin in the printer settings */
}
   
@media print
{
	html {overflow-x: visible;}
	table { page-break-after:auto }
	tr    { page-break-inside:avoid; page-break-after:auto }
	td    { page-break-inside:avoid; page-break-after:auto }
	thead { display:table-header-group }
	tfoot { display:table-footer-group }
	body {-webkit-print-color-adjust:exact;}
}

</style>
	</head>
	<body onload="window.print()">
	<div align="center" style="font-size:18px;"><strong><?=$RET['NOME_LOCAL']?> - <?=$RET['NOME_PROMO']?></strong></div>
	<br />

<table align="center" border=1 style="border-collapse:collapse;width:60%;horizontal-align:center" cellpadding=3 cellspacing=5>
			<thead>
			<tr style="background-color: #5d5d5d;color:#FFFFFF">
				<th style="text-align:center;">
					 Ordem
				</th>
				<th style="text-align:center;">
					 C&oacute;digo
				</th>
				</tr>
				</thead>
				<tbody>
		<?php
		    $DB = fnDBConn();
			$ID_PROMO = $_REQUEST['promo'];
			$SQL = "SELECT PROMO_CODIGO FROM PROMO_CODIGO_USUARIO WHERE ID_PROMO = $ID_PROMO AND ID_USUARIO IS NULL ORDER BY PROMO_CODIGO ASC";
			$RET = fnDB_DO_SELECT_WHILE($DB,$SQL);
			$contCodigos = count($RET); //quantidade de códigos do promo
			$quantAgrupadores = (int) ($contCodigos / 10); //isso aqui faz a divisao e pega apenas a parte inteira da divisão, para saber quando criar a linha de agrupador
			while($quantAgrupadores != 0){
				if(($quantAgrupadores*10) %10 == 0) { //checa se o numero é divisível por 10 com resto 0
					$arrImpressores[] = $quantAgrupadores*10; //se for, ele adiciona a um array
				}
				$quantAgrupadores--; //vamos decrecentando o contador até 0
			}
			$arrImpressores[] = 0; //adicionamos tb o numero 0, para que tenha agregador desde a 1a impressao
			
			$contChecaImpressor = 0; //contador que checa se estamos em um número que se deve imprimir a linha do agregador
			
			foreach($RET as $KEY => $ROW){

				$agrupador = $ROW['PROMO_CODIGO'][0]; //pega o agrupador do banco, pelo primeiro caractere da string do codigo de promo												
				$codigo = substr_replace($ROW['PROMO_CODIGO'],'',0,2);//pegando o resto do código sem o agrupador e sem o hífen
				
				$ROWSPAN = "<tr><td rowspan=11 align='center' style='vertical-align:middle;font-size:22px;border:solid black 1px;'>$agrupador</td></tr>"; //imprimindo o agrupador com um rowspan de 11, deixando espaço pra coluna de cabeçalho

				if (in_array($contChecaImpressor, $arrImpressores, true)) { //se nosso contador que checa se é hora de imprimir o agrupador estiver dentro de um dos números dos array, ele imprime
					echo $ROWSPAN; //imprime a linha do agregador
				}
				
				$contChecaImpressor++; //aumentamos o contador que checa se é hora de imprimir o agregador
				
				if($contChecaImpressor %2 != 0)
					echo "<tr><td align='center' style='font-size:16px;'>".$codigo."</td></tr>"; //imprime a linha com o código do promo
				else
					echo "<tr><td align='center' style='background-color:#f0f0f0;font-size:16px;'>".$codigo."</td></tr>"; //imprime a linha com o código do promo
			}
		?>
		</tbody>
		</table>
										
		<p>
			&nbsp;</p>
	</body>
</html>
