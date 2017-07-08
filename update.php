<?php
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
  $preguntas = unserialize($result['preguntas']);
  $query2 = "SELECT $pivote.id, $pivote.fecha, $estados.descripcion, estado_id".
  " FROM $pivote, $estados WHERE $pivote.incidencia_id=$id".
  " AND $pivote.estado_id = $estados.id ORDER BY $pivote.fecha DESC";
  $result2 = $wpdb->get_results($query2, ARRAY_A);
  $query3 = "SELECT * from $estados";
  $result_estados = $wpdb->get_results($query3, ARRAY_A);
 ?>

<div class="wrap">
  <h1 class="wp-heading-inline">Ficha de reparación</h1>
  <h2 class="nav-tab-wrapper">
     <a href="#tab-1" class="nav-tab nav-tab-active">Estado</a>
     <a href="#tab-2" class="nav-tab">Datos</a>
  </h2>
  <form action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="reparaciones_update">
    <input type="hidden" name="id" value="<?php echo intval($_GET['id'])?>">
    <input type="hidden" name="tecnico" value="<?php echo $tecnico->ID ?>">
    <div id="tab-1">
      <div class="postbox" id="boxid-1">
        <div class="inside">
          <h3 class="hndle"><span>Estado</span></h3>
        </div>
        <div class="inside">
          <h4 class="hndle"><span>Estado de la reparación</span></h4>
          <div class="form-field form-required term-name-wrap" style="display:inline-block; width:150px">
          <select name="estados">
            <?php foreach($result_estados as $estado){
              if ($result2[0]['estado_id']!=$estado['id'])
                echo '<option value="'.$estado['id'].'">'.$estado['descripcion'].'</option>';
              else
                echo '<option value="'.$estado['id'].'" selected>'.$estado['descripcion'].'</option>';
            }
            ?>
          </select>
          </div>
        </div>
        <div class="inside">
          <div class="form-field form-required term-name-wrap" style="display:inline-block">
            Resultado de la reparación:
            <input type="radio" name="resultado_reparacion" value="2" <?php if ($result['resultado_reparacion']==2) echo 'checked'?>> Favorable
            <input type="radio" name="resultado_reparacion" value="1" <?php if ($result['resultado_reparacion']==1) echo 'checked'?>> Desfavorable
          </div>
        </div>
        <div class="inside">
          <h4 class="hndle"><span>Historial de estados</span></h4>
          <ul>
            <?php foreach ($result2 as $r):
              $time = strtotime($r['fecha']);
              echo '<li> <b>'.$r['descripcion'].'</b> registrado el día '.date("d/m/Y", $time).' a las '.date("H:i:s", $time).'</li>';
              endforeach;
            ?>
          </ul>
        </div>
        <div class="inside">
          <input type="submit" value="Actualizar" class="button button-primary">
        </div>
      </div>
    </div>
    <div id="tab-2" style="display:none">
      <div class="postbox" id="boxid-2">
        <div class="inside">
          <h3 class="hndle"><span>Datos</span></h3>
        </div>
        <div class="inside">
          <h4> Fecha de entrega </h4>
          <div class="form-field form-required term-name-wrap" style="display:inline-block; width:60px">
            <label for="dia">Día</label> <input id = "dia" type="text" name="dia" value="<?php echo $dia; ?>">
          </div>
          <div class="form-field form-required term-name-wrap" style="display:inline-block; width:60px">
            <label for="mes">Mes</label> <input id="mes" type="text" name="mes" value="<?php echo $mes; ?>">
          </div>
          <div class="form-field form-required term-name-wrap"  style="display:inline-block; width:60px" >
            <label for="ano">Año</label> <input id = "ano" type="text" name="ano" value="<?php echo $ano; ?>">
          </div>
        </div>
        <div class="inside">
          <select name="cliente">
            <?php foreach($clientes as $cliente){
              if ($cliente->id !== $result[id])
                echo '<option value="'.$cliente->id.'" selected>'.$cliente->last_name.', '.$cliente->first_name.' ('.$cliente->user_email.')</option>';
              else
                echo '<option value="'.$cliente->id.'">'.$cliente->last_name.', '.$cliente->first_name.' ('.$cliente->user_email.')</option>';
            }

            ?>
          </select>
        </div>
        <div class="inside">
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <label for="marca">Marca y/o modelo</label> <input class="short" type="text" value="<?php echo $result['marca']?>" name="marca">
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
              <label for="rom">Contraseña iCloud</label>
              <input type="text" name="contrasena" value="<?php echo $result['password'];?>"/>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <label for="rom">Pin SIM</label>
            <input type="text" name="pinsim" value="<?php echo $result['pinsim'];?>"/>
          </div>
          <div style="width:50%" class="form-field form-required term-name-wrap">
            <label for="descripcion">Descripción de avería</label>
            <textarea rows="10" columns="40" class="short" type="text" name="descripcion"><?php echo $result['descripcion'];?></textarea>
          </div>
        </div>
        <div class="inside">
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="testeo" value="0" />
            <input type="checkbox" name="testeo" value="1" <?php if ($preguntas['testeo']==1) echo 'checked' ?>/>
            <label for="testeo">Testeo previo a aceptación del aparato*</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="rom" value="0" />
            <input type="checkbox" name="rom" value="1" <?php if ($preguntas['testeo']==1) echo 'rom' ?>/>
            <label for="rom">¿Se ha cambiado la ROM original?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="reparadoPrevio" value="0" />
            <input type="checkbox" name="reparadoPrevio" value="1" <?php if ($preguntas['reparadoPrevio']==1) echo 'checked' ?>/>
            <label for="rom">¿Ha sido previamente abierto o reparado?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="enciendeYApaga" value="0" />
            <input type="checkbox" name="enciendeYApaga" value="1" <?php if ($preguntas['enciendeYApaga']==1) echo 'checked' ?>/>
            <label for="rom">¿Enciende y apaga con normalidad?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="altavoces" value="0" />
            <input type="checkbox" name="altavoces" value="1" <?php if ($preguntas['altavoces']==1) echo 'checked' ?>/>
            <label for="rom">¿Funcionan altavoces y micrófono?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="pantalla" value="0" />
            <input type="checkbox" name="pantalla" value="1" <?php if ($preguntas['pantalla']==1) echo 'checked' ?>/>
            <label for="rom">¿Funciona la pantalla táctil de manera precisa?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="carga" value="0" />
            <input type="checkbox" name="carga" value="1" <?php if ($preguntas['carga']==1) echo 'checked' ?>/>
            <label for="rom">¿Carga de forma correcta?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="carcasa" value="0" />
            <input type="checkbox" name="carcasa" value="1" <?php if ($preguntas['carcasa']==1) echo 'checked' ?>/>
            <label for="rom">¿Existe algún desperfecto en la carcasa o pantalla?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="camaras" value="0" />
            <input type="checkbox" name="camaras" value="1" <?php if ($preguntas['camaras']==1) echo 'checked' ?>/>
            <label for="rom">¿Funcionan las cámaras?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="iCloud" value="0" />
            <input type="checkbox" name="iCloud" value="1" <?php if ($preguntas['iCloud']==1) echo 'checked' ?>/>
            <label for="rom">¿iCloud?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="funda" value="0" />
            <input type="checkbox" name="funda" value="1" <?php if ($preguntas['funda']==1) echo 'checked' ?>/>
            <label for="rom">¿Tiene Funda?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="sim" value="0" />
            <input type="checkbox" name="sim" value="1" <?php if ($preguntas['sim']==1) echo 'checked' ?>/>
            <label for="rom">¿Tiene tarjeta SIM?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="sd" value="0" />
            <input type="checkbox" name="sd" value="1" <?php if ($preguntas['sd']==1) echo 'checked' ?>/>
            <label for="rom">¿Tiene tarjeta SD?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="cargador" value="0" />
            <input type="checkbox" name="cargador" value="1" <?php if ($preguntas['cargador']==1) echo 'checked' ?>/>
            <label for="rom">¿Tiene cargador?</label>
          </div>
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <input type="hidden" name="sustitucion" value="0" />
            <input type="checkbox" name="sustitucion" value="1" <?php if ($preguntas['sustitucion']==1) echo 'checked' ?>/>
            <label for="rom">¿Se entrega móvil de sustitución al cliente?</label>
          </div>
        </div>
        <div class="inside">
          <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <label for="presupuesto">Presupuesto aproximado de la reparación (IVA incluído)</label>
            <input type="text" value="<?php echo $result['presupuesto'];?>" name="presupuesto">
          </div>
        </div>
        <div class="inside">
          <input type="submit" value="Actualizar" class="button button-primary">
        </div>
      </div>
    </div> <!-- end tab -->
  </form>
</div>

<script>

jQuery(document).ready( function() {
  var id = null;
  function displayTabs(){
    jQuery('a.nav-tab').each(function(){
      id = jQuery(this).attr('href');
      if (!jQuery(this).hasClass('nav-tab-active')){
        jQuery(id).css('display','none');
      }
      else{
        jQuery(id).css('display','block');
      }
    });
  }

  displayTabs();

  jQuery('a.nav-tab').click(function(e){
    if (!jQuery(this).hasClass('nav-tab-active')){
      e.preventDefault();
      jQuery('a.nav-tab').removeClass('nav-tab-active');
      jQuery(this).addClass('nav-tab-active');
      displayTabs();
    }
  });
});
</script>
