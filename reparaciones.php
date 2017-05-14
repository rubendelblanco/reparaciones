<?php
/*
Plugin Name: Incidencias y reparaciones
Description: Control de reparaciones para Colour Mobile
Author: Hazmeweb
Version: 1.0
*/


global $custom_table_example_db_version;
$custom_table_example_db_version = '1.1'; // version changed from 1.0 to 1.1

function custom_table_example_install()
{
  global $wpdb;

  $table_name = $wpdb->prefix . "incidencias_listado";
  $estados = $wpdb->prefix . "incidencias_estados";
  $pivote = $wpdb->prefix . "incidencias_listado_estados";

  $sql1 = "CREATE TABLE $estados (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    descripcion VARCHAR(256),
    PRIMARY KEY  (id)
  ) $charset_collate;
  ";

  $sql2 = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) UNSIGNED NOT NULL,
    tecnico_id bigint(20) UNSIGNED NOT NULL,
    fecha_entrega VARCHAR(8) NOT NULL,
    marca tinytext NOT NULL,
    descripcion mediumtext,
    preguntas VARCHAR(512),
    password VARCHAR(128),
    pinsim VARCHAR(9),
    codigo_bloqueo VARCHAR(24) NOT NULL,
    presupuesto VARCHAR(24),
    resultado_reparacion BOOLEAN NULL,
    fecha_registro datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY  (id),
    FOREIGN KEY (user_id) REFERENCES wp_users(id),
    FOREIGN KEY (tecnico_id) REFERENCES wp_users(id)
  ) $charset_collate;";

  $sql3 = "INSERT INTO $estados (id, descripcion) VALUES (1,'Móvil recibido en tienda'),(2,'Esperando piezas de reparación'),(3,'Reparando móvil'),(4,'Móvil reparado'),(5,'Cerrado'),(6,'Anulado')";

  $sql4 = "CREATE TABLE $pivote (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    estado_id mediumint(9) NOT NULL,
    incidencia_id mediumint(9) NOT NULL,
    fecha datetime DEFAULT current_timestamp NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (estado_id) REFERENCES wp_incidencias_estados(id),
    FOREIGN KEY (incidencia_id) REFERENCES wp_incidencias_listado(id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql1 );
  dbDelta( $sql2 );
  dbDelta( $sql3 );
  dbDelta( $sql4 );

  //anadir rol de tecnico
  global $wp_roles;
  if ( ! isset( $wp_roles ) )
      $wp_roles = new WP_Roles();

  $edit = $wp_roles->get_role('editor');
  //Adding a 'new_role' with all admin caps
  $wp_roles->add_role('tecnico', 'Técnico', $adm->capabilities);
}

register_activation_hook(__FILE__, 'custom_table_example_install');

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Custom_Table_Example_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'incidencia',
            'plural' => 'incidencias',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_marca($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=reparaciones_update&id=%s">%s</a>', $item['id'], __('Editar', 'reparaciones')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Borrar', 'reparaciones')),
            'PDF' => sprintf('<a href="#" class="pdf-conversion" data-action="http://localhost/wordpress/wp-content/plugins/reparaciones/pdf/pdf.php?id=%s">%s</a>', $item['id'], __('PDF', 'reparaciones')),
        );

        return sprintf('%s %s',
            $item['marca'],
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'marca' => __('Marca móvil', 'reparaciones'),
            'cliente' => __('Cliente','reparaciones'),
            'user_email'  => __('E-mail','reparaciones'),
            'fecha_registro' => __('Fecha registro', 'reparaciones'),
            'tecnico' => __('Técnico', 'reparaciones')
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'cliente' => array('display_name', true),
            'user_email' => array('user_email',true),
            'marca' => array('marca', true),
            'fecha_registro' => array('fecha_registro', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Borrar'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'incidencias_listado_estados'; // do not forget about tables prefix
        $table_listado = $wpdb->prefix . 'incidencias_listado';

        if ('delete' === $this->current_action()) {

            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE incidencia_id IN ($ids)");
                $wpdb->query("DELETE FROM $table_listado WHERE id IN ($ids)");
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'incidencias_listado'; // do not forget about tables prefix
        $table_users = $wpdb->prefix.'users';
        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        //SELECT DISTINCT cliente.user_email, cliente.display_name AS cliente, tecnico.display_name AS tecnico, descripcion, marca, fecha_entrega FROM
        //wp_users, wp_incidencias_listado JOIN wp_users cliente ON cliente.ID = wp_incidencias_listado.user_id JOIN wp_users tecnico ON tecnico.ID = wp_incidencias_listado.tecnico_id
        $query = "SELECT DISTINCT cliente.user_email, cliente.display_name AS cliente, tecnico.display_name AS tecnico, wp_incidencias_listado.id, descripcion, marca, fecha_registro".
         " FROM $table_users, $table_name JOIN $table_users cliente ON cliente.ID = ".$table_name.".user_id JOIN $table_users tecnico ON tecnico.ID = ".$table_name.".tecnico_id";

        //consulta de busqueda
        if (isset( $_REQUEST ["s"] )){
           $search = $_REQUEST["s"];
           $query .= " WHERE cliente.display_name LIKE '%%{$search}%%' OR marca LIKE '%%{$search}%%' OR cliente.user_email LIKE '%%{$search}%%'";
         }
        $this->items = $wpdb->get_results($wpdb->prepare($query, $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'search' =>$_REQUEST["s"] , // busqueda
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));

    }
}

/**
 * PART 3. Admin page
 * ============================================================================
 *
 * In this part you are going to add admin page for custom table
 *
 * http://codex.wordpress.org/Administration_Menus
 */

/**
 * admin_menu hook implementation, will add pages to list persons and to add new one
 */
function custom_table_example_admin_menu()
{
    add_menu_page(__('Reparaciones', 'reparaciones'), __('Reparaciones', 'reparaciones'), 'activate_plugins', 'reparaciones', 'reparaciones_handler');
    add_submenu_page('reparaciones', __('Reparaciones', 'reparaciones'), __('Reparaciones', 'reparaciones'), 'activate_plugins', 'reparaciones', 'reparaciones_handler');
    // add new will be described in next part
    add_submenu_page('reparaciones', __('Crear nueva', 'reparaciones'), __('Crear nueva', 'reparaciones'), 'activate_plugins', 'reparaciones_form', 'reparaciones_add');
    //Estas paginas no aparecen, parent_slug=null
    add_submenu_page( null, __('Actualizar reparación',''), __('Actualizar reparación',''), 'activate_plugins', 'reparaciones_update', 'reparaciones_update');
    add_submenu_page( null, __('Crear PDF',''), __('Crear PDF',''), 'activate_plugins', 'create_PDF', 'create_PDF');
}

add_action('admin_menu', 'custom_table_example_admin_menu');

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function reparaciones_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table();
    $table->prepare_items();
    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Elementos borrados: %d', 'custom_table_example'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Reparaciones', 'custom_table_example')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=reparaciones_form');?>"><?php _e('Nueva reparación', 'custom_table_example')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <?php $table->search_box('Buscar por cliente, email o marca', 'search'); ?>
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>
    <script>
        jQuery(function () {
            jQuery('.pdf-conversion').click(function (e) {
                e.preventDefault();

                jQuery('<form>').attr({
                    'action': jQuery(this).data('action'),
                    'method': 'POST'
                }).appendTo('body').submit().remove();

            });
        });
    </script>
</div>
<?php
}

function reparaciones_add()
{

  include('formulario.php');

}

function reparaciones_alta(){
  if ( ! empty( $_POST ) ) {
    global $wpdb;
    $table = $wpdb->prefix.'incidencias_listado';
    $dia = $_POST['dia'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];

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
    $data = array(
        'user_id' => $_POST['cliente'],
        'tecnico_id' => $_POST['tecnico'],
        'marca' =>$_POST['marca'],
        'descripcion' =>$_POST['descripcion'],
        'password' => $_POST['contrasena'],
        'pinsim' => $_POST['pinsim'],
        'preguntas' => serialize($preguntas),
        'presupuesto' => $_POST['presupuesto'],
        'fecha_entrega' => $ano.$mes.$dia
    );
    $format = array(
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s'
    );
    $success=$wpdb->insert( $table, $data, $format );
    echo $sucess;
    if($success){
      $lastid = $wpdb->insert_id;
      $table = $wpdb->prefix.'incidencias_listado_estados';
      $data = array(
        'estado_id' => 1,
        'incidencia_id' => $lastid
      );
      $format = array(
          '%s',
          '%s'
      );
      $wpdb->insert( $table, $data, $format );
      $admin_url = get_admin_url();
      wp_redirect( $admin_url.'admin.php?page=reparaciones' );
    }
    else{ echo 'error';}
  }
}

function reparaciones_update(){
  include('update.php');
}

function reparaciones_update_form(){
  include('reparaciones_update_form.php');
}

function create_PDF(){
  include('pdf/pdf.php');
}

function custom_table_example_validate_person($item)
{
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
    if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

/*Comprueba si woocommerce está instalado*/
function checar_woocommerce(){
  if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    echo '<div class="notice notice-error is-dismissible"><p>';
        _e( 'ADVERTENCIA: El plugin "Reparaciones" requiere de WooCommerce para funcionar correctamente.', 'sample-text-domain' );
    echo '</p></div>';
  }
}

function anuncio_alta_creada(){
    echo '<div class="notice notice-error is-dismissible"><p>';
        _e( 'Nueva alta creada', 'sample-text-domain' );
    echo '</p></div>';
}

function cargar_validacion(){
  wp_enqueue_script( 'validator', plugins_url( '/jquery-validation/dist/jquery.validate.js', __FILE__ ), array ( 'jquery' ), 1.16, false);
}

//actions
include('clientemeta.php');
add_action( 'admin_enqueue_scripts', 'cargar_validacion' );
add_action ('admin_notices', 'checar_woocommerce');
add_action ('admin_action_reparaciones', 'reparaciones_alta');
add_action ('admin_action_reparaciones_update','reparaciones_update_form' );
