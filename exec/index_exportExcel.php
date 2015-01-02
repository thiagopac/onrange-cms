<?
session_start();
$DOWNLOAD = (int)$_GET['download'];

if ($DOWNLOAD == 1)
	{
	header('Content-Type: application/xls');
	header('Content-Disposition: attachment; filename="relatorio.xls"');

	?><html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Relatório</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table><?
	echo $_SESSION['download'];
	?></table></body></html><?

	//Limpa a sessao
	$_SESSION['download'] = '';
	}
else
	{
	$_SESSION['download'] = $_POST['content'];
	}
?>