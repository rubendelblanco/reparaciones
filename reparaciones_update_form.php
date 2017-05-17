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

if($success!==false){
    $table = $wpdb->prefix.'incidencias_listado_estados';
    $sql = "SELECT * FROM $table WHERE incidencia_id=$id ORDER BY fecha DESC";
    $query = $wpdb->get_row($sql,ARRAY_A);
    
    //se manda el email si el estado de reparacion ha cambiado
    if ($query['estado_id']!=$_POST['estados']){
      $data = array(
        'estado_id' => $_POST['estados'],
        'incidencia_id' => $id
      );
      $format = array(
          '%s',
          '%s'
      );
      $wpdb->insert( $table, $data, $format );
      //mandar_mail
      envio_mail($_POST['cliente'],$id);
    }

    $admin_url = get_admin_url();
    wp_redirect( $admin_url.'admin.php?page=reparaciones' );
  }
  else{echo 'error';}
}

function envio_mail($id_user,$id){
  global $wpdb;
  $sql = "SELECT ".$wpdb->prefix."incidencias_listado.id, ".$wpdb->prefix."incidencias_estados.descripcion, fecha, marca FROM ".$wpdb->prefix."incidencias_listado_estados, ".
  $wpdb->prefix."incidencias_listado, ".$wpdb->prefix."incidencias_estados".
  " WHERE ".$wpdb->prefix."incidencias_listado_estados.estado_id=".$wpdb->prefix."incidencias_estados.id AND ".$wpdb->prefix."incidencias_listado.user_id=$id_user".
  " AND ".$wpdb->prefix."incidencias_listado.id = $id AND ".$wpdb->prefix."incidencias_listado_estados.incidencia_id = $id ORDER BY fecha DESC";
  $result_estados = $wpdb->get_results($sql, ARRAY_A);
  $lista_estados='';

  foreach($result_estados as $v){
    $time = strtotime($r['fecha']);
    $lista_estados .= '<li>'.$v['descripcion'].' el día '.date("d/m/Y", $time).' a las '.date("H:i:s", $time).'</li>';
  }

  $direccion = get_userdata( $id_user )->user_email;
  $subject = 'Actualización de su reparación';
  $headers = array('Content-Type: text/html; charset=UTF-8');
  $body = '<p>Su reparación del movil modelo '.$result_estados[0]['marca'].' con referencia nº '.$result_estados[0]['id'].' ha sido actualizada.</p>';
  $body .= '<p>El historial de estado de su móvil es el siguiente:</p> ';
  $body .= '<ul>'.$lista_estados.'</ul>';
  wp_mail( $direccion, $subject, $body, $headers );
}
 ?>
