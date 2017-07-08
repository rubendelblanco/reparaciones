<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('TOKEN', time());
define('DEBUG', isset($_GET['debug']));

require_once 'dompdf/autoload.inc.php';
require_once '../../../../wp-config.php';
require_once 'db.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$db = new DB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_CHARSET);

ob_start();

$id = intval($_GET['id']);

require_once 'resguardo.php';

$content = ob_get_clean();

ob_end_flush();

if (DEBUG) {
    echo $content;
} else {

    $dompdf->loadHtml($content);
    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();
}

?>