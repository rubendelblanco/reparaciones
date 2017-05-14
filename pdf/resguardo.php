<?php defined('TOKEN') or die('nope');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="pdf.css" rel="stylesheet" />
    </head>
    <body style="margin-top:-20px">

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

        <table width="100%" border="0">
          <tr>
            <td><b>Fecha de entrega:</b> <?php echo $dia.'/'.$mes.'/'.$ano ?></td>
            <td colspan="2"><b>Marca/modelo: </b><?php echo $result['marca'] ?></td>
          </tr>
          <tr>
            <td><b>Nombre de cliente:</b> <?php echo $result['display_name']?></td>
            <td><b>DNI:</b> <?php echo $meta['dni'];?></td>
            <td><b>Tlf:</b> <?php echo $meta['billing_phone']?></td>
          </tr>
        </table>
        <table width="100%" style="margin: 10px 0 10px 0">
          <tr style="border-top:1px solid; border-left:1px solid; border-right:1px solid">
            <td><b>Descripción de la avería:</b></td>
          </tr>
          <tr style="border:0px 1px 1px 1px solid">
            <td><?php echo $result['descripcion']?></td>
          </tr>
        </table>
        <table width="100%" border="1" style="margin:5px 0 5px 0">
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
        <table width="100%" border="1" style="margin:5px 0 5px 0">
          <tr>
            <td style="width:20%">Funda: <?php echo $preguntas['funda']?></td>
            <td style="width:20%">SIM: <?php echo $preguntas['sim']?></td>
            <td style="width:20%">SD: <?php echo $preguntas['sd']?></td>
            <td style="width:20%">Cargador: <?php echo $preguntas['cargador']?></td>
            <td style="width:20%">Móvil de sustitución: <?php echo $preguntas['sustitucion']?></td>
          </tr>
        </table>
        <table width="100%" border="1" style="margin:5px 0 5px 0">
          <tr>
            <td>iCLoud: <?php echo $preguntas['iCloud']?></td>
            <td>Contraseña: <?php echo $preguntas['password']?></td>
          </tr>
          <tr>
            <td colspan="2">Presupuesto aproximado (IVA incluído): <b><?php echo $result['presupuesto']?> &euro;</b></td>
          </tr>
        </table>
        <p style="margin:10px 0 5px 0;">Código de desbloqueo (poner numeros en el orden del patrón)</p>
        <div style="float:left">
          <table width="231px" border="1" style="margin:5px 0 5px 0; table-layout:fixed">
            <tr>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
            </tr>
            <tr>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
            </tr>
            <tr>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
              <td style="height:35px;"></td>
            </tr>
          </table>
        </div>
        <div style="float:right; margin-right:10%">FIRMA DE LA TIENDA/CLIENTE:</div>
        <p style="clear:both">RESULTADO DE LA REPARACION:
          <?php if ($result['resultado_reparacion']==1) echo '<span style="color:red">DESFAVORABLE</span>';
          else if ($result['resultado_reparacion']==2) echo '<span style="color:green">FAVORABLE</span>';?>
        </p>
    </body>
</html>
