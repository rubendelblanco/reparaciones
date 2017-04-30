<?php
/*
Plugin Name: fpdf
Description: Post to PDF
*/

global $user_dir;

global $wpdb;
$clientes = get_users(
  array('role' => 'customer','orderby'=> 'last_name','order'=> 'ASC')
);
$tecnico = wp_get_current_user();
$id = $_GET['id'];
$tabla = $wpdb->prefix . "incidencias_listado";
$estados = $wpdb->prefix . "incidencias_estados";
$pivote = $wpdb->prefix . "incidencias_listado_estados";
$query = "SELECT * FROM $tabla WHERE ID = $id";
$result = $wpdb->get_row($query, ARRAY_A);
$ano = substr($result['fecha_entrega'],0,4);
$mes = substr($result['fecha_entrega'],4,2);
$dia = substr($result['fecha_entrega'],6,2);
$preguntas_raw = unserialize($result['preguntas']);
$preguntas = Array();

foreach ($preguntas_raw as $key => $value){

  if ($value == '1') {
    $preguntas[$key] = 'Sí';
  }
  else $preguntas[$key] = 'No';
}

$query2 = "SELECT $pivote.id, $pivote.fecha, $estados.descripcion".
" FROM $pivote, $estados WHERE $pivote.incidencia_id=$id".
" AND $pivote.estado_id = $estados.id ORDER BY $pivote.fecha DESC";
$result2 = $wpdb->get_results($query2, ARRAY_A);
$cliente = get_userdata( $result['user_id'] );

/*
function make_user_dir(){
  global $current_user;
  wp_get_current_user();

  $upload_dir = wp_upload_dir();
  $user_dirname = $upload_dir['basedir'].'/'.$current_user->user_login;
  if ( ! file_exists( $user_dirname ) ) {
      wp_mkdir_p( $user_dirname );
  }
  $user_dir = $user_dirname;
}
add_action('init','make_user_dir');*/

require('fpdf/fpdf.php');
class PDF extends FPDF
{
// Cabecera de página
function Header()
{

    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(30,10,utf8_decode('COLOUR MOBILE AVILA - Resguardo de reparación nº '.$id),1,0,'C');
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
}
}
/*$wp_upload_dir = wp_upload_dir();
$uploadedfile = trailingslashit ( $wp_upload_dir['path'] ) . 'dimserPdf2014.pdf';*/

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,20,'Fecha de entrega:');
$pdf->Cell(40,20,$dia.'/'.$mes.'/'.$ano,1);
$pdf->Ln();
$pdf->Cell(40,25,'Nombre cliente:');
$pdf->Cell(80,25,utf8_decode($cliente->first_name.' '.$cliente->last_name));
$pdf->Cell(10,25,'DNI:');
$pdf->Cell(40,25,utf8_decode($cliente->dni));
$pdf->Ln();
$pdf->Cell(30,25,utf8_decode('Teléfono:'));
$pdf->Cell(40,25,utf8_decode($cliente->billing_phone));
$pdf->Ln();
$pdf->Cell(30,25,utf8_decode('Marca/modelo:'));
$pdf->Cell(40,25,utf8_decode($result->marca));
$pdf->Ln();
$pdf->Cell(30,25,utf8_decode('Descripción de la avería:'));
$pdf->Ln();
$pdf->Cell(190,40,utf8_decode($result->marca),1);
$pdf->Ln();
$pdf->Cell(35,15,utf8_decode('Testeo previo: '.$preguntas['testeo']));
$pdf->Cell(58,15,utf8_decode('Se ha cambiado la ROM: '.$preguntas['rom']));
$pdf->Cell(60,15,utf8_decode('Ha sido previamente reparado o abierto: '.$preguntas['reparadoPrevio']));
$pdf->Ln();
$pdf->Cell(75,15,utf8_decode('Enciende y apaga con normalidad: '.$preguntas['enciendeYApaga']));
$pdf->Cell(68,15,utf8_decode('Funcionan altavoz y micro: '.$preguntas['altavoces']));
$pdf->Ln();
$pdf->Cell(60,15,utf8_decode('Funciona la pantalla táctil: '.$preguntas['pantalla']),1);
$pdf->Cell(60,15,utf8_decode('Carga de forma correcta: '.$preguntas['carga']),1);
$pdf->Cell(60,15,utf8_decode('Funcionan las cámaras: '.$preguntas['camaras']),1);
$pdf->Ln();
$pdf->Cell(120,15,utf8_decode('Existe algún desperfecto en la carcasa o pantalla: '.$preguntas['pantalla']),1);
$pdf->Cell(60,15,utf8_decode('iCloud: '.$preguntas['iCloud']),1);
$pdf->Ln();
$pdf->Cell(45,15,utf8_decode('Funda: '.$preguntas['funda']),1);
$pdf->Cell(45,15,utf8_decode('SIM: '.$preguntas['sim']),1);
$pdf->Cell(45,15,utf8_decode('SD: '.$preguntas['sd']),1);
$pdf->Cell(45,15,utf8_decode('Cargador: '.$preguntas['cargador']),1);
ob_get_clean();
$pdf->Output($uploadedfile, "D");
/*$pdf->Output($uploadedfile, "F");
$attachment = array(
   'guid' => trailingslashit ($wp_upload_dir['url']) . basename( $uploadedfile ),
   'post_mime_type' => 'application/pdf',
   'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
   'post_content' => '',
   'post_status' => 'inherit'
);*/

// Id of attachment if needed
//$attach_id = wp_insert_attachment( $attachment,$uploadedfile);
?>
