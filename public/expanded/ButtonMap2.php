<?php

/* 
 */
$id = 'Minnie';
$Html = <<<EOF
<div class="OSMdivButton"><button id="{$id}" class="OSMbutton"> VIEW MAP </button></div>
EOF;

?>

<!DOCTYPE html>
<html>
    <head>
        <title>ButtonMap2</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <div ><p id="mMess">&nbsp;</p></div>
            <?php
            /*  Map Button  */
            echo $Html;
            ?>
            
            <script>
                let el = document.getElementById('Minnie');
                el.addEventListener('click', function(){
                    //window.open('', 'Minnie');
                });
            </script>
        </div>
    </body>
    
