<?php defined('TOKEN') or die('nope');?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="pdf.css" rel="stylesheet" />
    </head>
    <body>

        <?php 
            $post = $db->row('SELECT * FROM ' . $table_prefix . 'posts WHERE post_type = "post" AND id = ' . $post_id);
        ?>

        
        <h1 class="header"><?=$post['post_title'];?></h1>

        <p>
            <?=$post['post_content']?>
        </p>

    </body>
</html>
