<?php defined('TOKEN') or die('nope');?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="pdf.css" rel="stylesheet" />
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
                    <td><?=($post['post_title']);?></td>
                    <td><?=$post['post_date']?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
    </body>
</html>
