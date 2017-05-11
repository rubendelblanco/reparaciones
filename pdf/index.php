<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../../../wp-config.php';
require_once 'db.php';

$db = new DB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_CHARSET);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="pdf.css" rel="stylesheet" />
        <script src="//code.jquery.com/jquery-1.12.4.min.js" ></script>
    </head>
    <body>
        <h1 class="header">Qué tiempos, cuando se escibía algo</h1>

        <?php 
            $posts = $db->matrix('SELECT * FROM ' . $table_prefix . 'posts WHERE post_type = "post" ORDER BY post_date DESC');
        ?>
        <table border="1" width="100%" >
            <thead>
                <tr>
                    <th>title</th>
                    <th>date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                <tr>
                    <td><a href="#" data-action="/wp-content/plugins/custom/pdf.php?id=<?=$post['ID'];?>" ><?=($post['post_title']);?></a></td>
                    <td><?=$post['post_date']?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
    </body>

    <script>
        $(function () {
            $('a').click(function (e) {
                e.preventDefault();
            
                $('<form>').attr({
                    'action': $(this).data('action'),
                    'method': 'POST'
                }).appendTo('body').submit().remove();

            });
        });
    </script>
</html>
