<?php
if ( ! empty( $_POST ) ) {
  global $wpdb;
  $table = $wpdb->prefix.'incidencias_listado';
  $dia = $_POST['dia'];
  $mes = $_POST['mes'];
  $ano = $_POST['ano'];
  $id = intval($_POST['id']);
  if (strlen($dia)==1) $dia = '0'.$dia;
  if (strlen($mes)==1) $mes = '0'.$mes;

  $preguntas = array(
    'testeo' => $_POST['testeo'],
    'rom' => $_POST['rom'],
    'reparadoPrevio' => $_POST['reparadoPrevio'],
    'enciendeYApaga' => $_POST['enciendeYApaga'],
    'altavoces' => $_POST['altavoces'],
    'pantalla' => $_POST['pantalla'],
    'carga' => $_POST['carga'],
    'carcasa' => $_POST['carcasa'],
    'camaras' => $_POST['camaras'],
    'iCloud' => $_POST['iCloud'],
    'funda' => $_POST['funda'],
    'sim' => $_POST['sim'],
    'sd' => $_POST['sd'],
    'cargador' => $_POST['cargador'],
    'sustitucion' => $_POST['sustitucion']
  );

  $sql4 = "UPDATE `wp_incidencias_listado` SET `user_id` = ".$_POST['cliente'].",`tecnico_id` = ".$_POST['tecnico'].",
  `marca` = '".$_POST['marca']."',`descripcion` = '".$_POST['descripcion']."' ,`password` = '".$_POST['contrasena']."',`pinsim` = '".$_POST['pinsim']."',
`preguntas` = '".serialize($preguntas)."',
`presupuesto` = '".$_POST['presupuesto']."',
`fecha_entrega` = '".$ano.$mes.$dia."',
`resultado_reparacion` = '".$_POST['resultado_reparacion']."'
WHERE `id` = $id";
$success = $wpdb->query($sql4);

  if($success){
    $table = $wpdb->prefix.'incidencias_listado_estados';
    $data = array(
      'estado_id' => $_POST['estados'],
      'incidencia_id' => $id
    );
    $format = array(
        '%s',
        '%s'
    );
    $wpdb->insert( $table, $data, $format );
    $admin_url = get_admin_url();
    wp_redirect( $admin_url.'admin.php?page=reparaciones' );
  }
  else{ echo $success; echo 'error';}
}
 ?>
