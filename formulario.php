<style>
label.error{
  color: red;
}
.mensajes-error{
  display:none;
  border: 1px solid red;
  background-color: #ffe5e5;
  padding:5px;
}
</style>
<?php
  $clientes = get_users(
    array('role' => 'customer','orderby'=> 'last_name','order'=> 'ASC')
  );
  $tecnico = wp_get_current_user();
 ?>
<div class="wrap">
  <h1 class="wp-heading-inline">Resguardo de reparación</h1>
  <form id="altaForm" action="<?php echo esc_url( admin_url('admin.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="reparaciones">
    <input type="hidden" name="tecnico" value="<?php echo $tecnico->ID ?>">
    <div class="postbox" id="boxid">
      <div class="inside">
        <h3 class="hndle"><span> Resguardo de reparación</span></h3>
      </div>
      <div class="inside">
        <h4> Fecha de entrega* </h4>
        <div class="form-field form-required term-name-wrap" style="display:inline-block; width:60px">
          <label for="dia">Día</label> <input id = "dia" type="text" name="dia" data-msg-required="Introduzca un dia de mes<br/>" required>
        </div>
        <div class="form-field form-required term-name-wrap" style="display:inline-block; width:60px" data-msg-required="Introduzca un mes (formato: 1-12)<br/>" required>
          <label for="mes">Mes</label> <input id="mes" type="text" name="mes">
        </div>
        <div class="form-field form-required term-name-wrap"  style="display:inline-block; width:60px" >
          <label for="ano">Año</label> <input id = "ano" type="text" name="ano" minlength="4" maxlength="4" data-msg-required="Introduzca un año (cuatro cifras. Ejemplo: 2015)<br/>" required>
        </div>
      </div>
      <div class="inside">
        <select name="cliente">
          <?php foreach($clientes as $cliente){
            echo '<option value="'.$cliente->id.'">'.$cliente->last_name.', '.$cliente->first_name.' ('.$cliente->user_email.')</option>';
          }
          ?>
        </select>
      </div>
      <div class="inside">
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <label for="marca">Marca y/o modelo*</label> <input class="short" type="text" name="marca" required data-msg-required="Introduzca una marca o modelo de movil<br/>">
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
            <label for="rom">Contraseña iCloud</label>
            <input type="text" name="contrasena"/>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <label for="rom">Pin SIM</label>
          <input type="text" name="pinsim"/>
        </div>
        <div style="width:50%" class="form-field form-required term-name-wrap">
          <label for="descripcion">Descripción de avería*</label>
          <textarea rows="10" columns="40" class="short" type="text" name="descripcion" data-msg-required="La descripción de la avería es obligatoria<br/>" required></textarea>
        </div>
      </div>
      <div class="inside">
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="testeo" value="0" />
          <input type="checkbox" name="testeo" value="1" required/>
          <label for="testeo">Testeo previo a aceptación del aparato*</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="rom" value="0" />
          <input type="checkbox" name="rom" value="1" />
          <label for="rom">¿Se ha cambiado la ROM original?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="reparadoPrevio" value="0" />
          <input type="checkbox" name="reparadoPrevio" value="1" />
          <label for="rom">¿Ha sido previamente abierto o reparado?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="enciendeYApaga" value="0" />
          <input type="checkbox" name="enciendeYApaga" value="1" />
          <label for="rom">¿Enciende y apaga con normalidad?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="altavoces" value="0" />
          <input type="checkbox" name="altavoces" value="1" />
          <label for="rom">¿Funcionan altavoces y micrófono?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="pantalla" value="0" />
          <input type="checkbox" name="pantalla" value="1" />
          <label for="rom">¿Funciona la pantalla táctil de manera precisa?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="carga" value="0" />
          <input type="checkbox" name="carga" value="1" />
          <label for="rom">¿Carga de forma correcta?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="carcasa" value="0" />
          <input type="checkbox" name="carcasa" value="1" />
          <label for="rom">¿Existe algún desperfecto en la carcasa o pantalla?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="camaras" value="0" />
          <input type="checkbox" name="camaras" value="1" />
          <label for="rom">¿Funcionan las cámaras?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="iCloud" value="0" />
          <input type="checkbox" name="iCloud" value="1" />
          <label for="rom">¿iCloud?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="funda" value="0" />
          <input type="checkbox" name="funda" value="1" />
          <label for="rom">¿Tiene Funda?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="sim" value="0" />
          <input type="checkbox" name="sim" value="1" />
          <label for="rom">¿Tiene tarjeta SIM?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="sd" value="0" />
          <input type="checkbox" name="sd" value="1" />
          <label for="rom">¿Tiene tarjeta SD?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="cargador" value="0" />
          <input type="checkbox" name="cargador" value="1" />
          <label for="rom">¿Tiene cargador?</label>
        </div>
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <input type="hidden" name="sustitucion" value="0" />
          <input type="checkbox" name="sustitucion" value="1" />
          <label for="rom">¿Se entrega móvil de sustitución al cliente?</label>
        </div>
      </div>
      <div class="inside">
        <div style="display:inline-block;width:25%" class="form-field form-required term-name-wrap">
          <label for="presupuesto">Presupuesto aproximado de la reparación (IVA incluído)</label>
          <input type="text" name="presupuesto" min="0" value="0" required number="true"> &euro;
        </div>
      </div>
      <div class="inside">
        <input type="submit" value="Dar de alta" class="button button-primary">
      </div>
      <div class="inside">
        <div class="mensajes-error"><h4>Se han encontrado los siguientes errores:</h4></div>
      </div>
    </div>
  </form>
</div>
<script>
  jQuery(document).ready(function(){
    var now = new Date();
    var mes = now.getMonth();
    var ano = now.getFullYear();
    var dia = now.getDate();
    jQuery('#ano').val(ano);
    jQuery('#mes').val(mes+1);
    jQuery('#dia').val(dia);
  })
</script>
<script>
jQuery("#altaForm").validate({
  errorLabelContainer: jQuery(".mensajes-error"),
  messages: {
      ano:{
        minlength: "El año debe tener cuatro cifras<br/>",
        maxlength: "El año debe tener cuatro cifras<br/>"
      },
      testeo:{
        required: "Se debe aceptar el testeo previo del aparato<br/>"
      },
      presupuesto:{
        required: "Es obligatorio poner un presupuesto<br/>",
        number: "El valor del presupuesto debe ser un numero. Poner decimales con puntos. Ej: 45.10<br/>",
        min: "El presupuesto no puede ser negativo<br/>"
      }
		}
});
jQuery("#altaForm").validate();
</script>
