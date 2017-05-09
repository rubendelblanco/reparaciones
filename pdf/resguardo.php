<?php defined('TOKEN') or die('nope');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="pdf.css" rel="stylesheet" />
    </head>
    <body>

        <?php
            $incidencias = $table_prefix.'incidencias_listado';
            $users = $table_prefix.'users';
            $meta = $table_prefix.'usermeta';
            $id = intval($_GET['id']);
            $result = $db->row("SELECT $incidencias.*,$users.user_email, $users.display_name FROM $incidencias,$users WHERE $users.id=$incidencias.user_id AND $incidencias.id=$id");
            $cliente = $result['user_id'];
            $meta_query = $db->matrix("SELECT meta_key, meta_value FROM $meta WHERE user_id=$cliente");
            $meta = Array();
            foreach ($meta_query as $key) {
              $meta[$key['meta_key']] = $key['meta_value'];
            }
            $ano = substr($result['fecha_entrega'],0,4);
            $mes = substr($result['fecha_entrega'],4,2);
            $dia = substr($result['fecha_entrega'],6,2);
            $preguntas_raw = unserialize($result['preguntas']);
            $preguntas = Array();

            foreach ($preguntas_raw as $key => $value){

              if ($value == '1') {
                $preguntas[$key] = '<span style="color:green; font-weight:bold">Sí</span>';
              }
              else $preguntas[$key] = '<span style="color:red;font-weight:bold">No</span>';
            }
        ?>


        <h3 class="header">Colour Mobile Ávila - resguardo de reparación nº <?php echo $result['id']?></h3>
        <table width="100%" border="1">
          <tr>
            <th>Fecha de entrega: <br/><?php echo $dia.'/'.$mes.'/'.$ano ?></th>
            <th>Nombre de cliente: <br/><?php echo $result['display_name']?></th>
            <th>DNI: <br/><?php echo $meta['dni'];?></th>
            <th>Tlf: <br/><?php echo $meta['billing_phone']?></th>
            <th>Marca/modelo: <br/> <?php echo $result['marca'] ?></th>
          </tr>
        </table>
        <table width="100%" border="1">
          <tr>
            <td>Descripción de la avería:</td>
          </tr>
          <tr>
            <td><?php echo $result['descripcion']?></td>
          </tr>
        </table>
        <table width="100%" border="1">
          <tr>
            <td style="width:50%">Testeo previo a aceptación del aparato:</td>
            <td style="width:50%"><?php echo $preguntas['testeo']?></td>
          </tr>
          <tr>
            <td style="width:50%">Se ha cambiado la ROM original:</td>
            <td style="width:50%"><?php echo $preguntas['rom']?></td>
          </tr>
          <tr>
            <td style="width:50%">Ha sido previamente reparado o abierto:</td>
            <td style="width:50%"><?php echo $preguntas['reparadoPrevio']?></td>
          </tr>
          <tr>
            <td style="width:50%">Enciende y apaga con normalidad:</td>
            <td style="width:50%"><?php echo $preguntas['enciendeYApaga']?></td>
          </tr>
          <tr>
            <td style="width:50%">Funcionan altavoces y micrófonos:</td>
            <td style="width:50%"><?php echo $preguntas['altavoces']?></td>
          </tr>
          <tr>
            <td style="width:50%">Funciona la pantalla táctil de manera precisa:</td>
            <td style="width:50%"><?php echo $preguntas['pantalla']?></td>
          </tr>
          <tr>
            <td style="width:50%">Carga de forma correcta:</td>
            <td style="width:50%"><?php echo $preguntas['carga']?></td>
          </tr>
          <tr>
            <td style="width:50%">Existe algún desperfecto en la carcasa o pantalla:</td>
            <td style="width:50%"><?php echo $preguntas['carcasa']?></td>
          </tr>
          <tr>
            <td style="width:50%">Funcionan las cámaras:</td>
            <td style="width:50%"><?php echo $preguntas['camaras']?></td>
          </tr>
        </table>
        <table width="100%" border="1">
          <tr>
            <td style="width:20%">Funda: <?php echo $preguntas['funda']?></td>
            <td style="width:20%">SIM: <?php echo $preguntas['sim']?></td>
            <td style="width:20%">SD: <?php echo $preguntas['sd']?></td>
            <td style="width:20%">Cargador: <?php echo $preguntas['cargador']?></td>
            <td style="width:20%">Móvil de sustitución: <?php echo $preguntas['sustitucion']?></td>
          </tr>
        </table>
        <table width="100%" border="1">
          <tr>
            <td>iCLoud: <?php echo $preguntas['iCloud']?></td>
            <td>Contraseña: <?php echo $preguntas['password']?></td>
          </tr>
          <tr>
            <td colspan="2">Presupuesto aproximado (IVA incluído) <?php echo $result['presupuesto']?> &euro;</td>
          </tr>
        </table>
    </body>
</html>
