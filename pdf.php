<?php
$f;
$l;
if(headers_sent($f,$l))
{
    echo $f,'<br/>',$l,'<br/>';
    die('now detect line');
}

require_once PTPDF_PATH . '/dompdf/autoload.inc.php';

/*
global $wpdb;
$tecnico = wp_get_current_user();
$id = $_GET['id'];
$tabla = $wpdb->prefix . "incidencias_listado";
$estados = $wpdb->prefix . "incidencias_estados";
$pivote = $wpdb->prefix . "incidencias_listado_estados";
$query = "SELECT * FROM $tabla WHERE ID = $id";
$result = $wpdb->get_row($query, ARRAY_A);*/
use Dompdf\Dompdf;

$dompdf = new Dompdf();
ob_start();
$dompdf->set_option('defaultFont', 'Courier');
echo 'Estoy hasta los cojones de esta mierda';
$content = ob_get_clean();
$dompdf->loadHtml($content);
ob_end_flush();

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
 $dompdf->stream();

?>
