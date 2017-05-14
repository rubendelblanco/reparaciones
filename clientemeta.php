<?php
  /*Meta informacion de los clientes*/
 add_action ('show_user_profile', 'show_clientemeta');
 add_action ('edit_user_profile', 'show_clientemeta');
 add_action('personal_options_update', 'save_clientemeta');
 add_action('edit_user_profile', 'save_clientemeta');

function show_clientemeta($user){?>
  <h3>Datos adicionales de cliente</h3>
  <table class="form-table">
    <th><label for="dni">DNI</label></th>
    <td>
      <input type="text" name="dni" value="<?php echo esc_attr(get_user_meta($user->id,'dni',true));?>">
    </td>
  </table>
<?php }
 function save_clientemeta($user){
   if (!current_user_can('edit_user', $user)) return false;
   return update_user_meta($user->ID,'dni', $_POST['dni']);
 }
?>
