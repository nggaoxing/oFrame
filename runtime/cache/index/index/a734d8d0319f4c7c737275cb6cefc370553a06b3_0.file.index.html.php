<?php
/* Smarty version 3.1.29, created on 2017-08-05 13:58:34
  from "C:\myphp\apache\htdocs\OopFrame\view\index\index\index.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_59855e8a3256a3_03665807',
  'file_dependency' => 
  array (
    'a734d8d0319f4c7c737275cb6cefc370553a06b3' => 
    array (
      0 => 'C:\\myphp\\apache\\htdocs\\OopFrame\\view\\index\\index\\index.html',
      1 => 1501912711,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59855e8a3256a3_03665807 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
    <head>
        <title>of框架</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                margin-top: 200px;
                text-align: center;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 50px;
            } 
            .title2 {
                font-size: 16px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title"><?php echo $_smarty_tpl->tpl_vars['welcome']->value;?>
</div>
                <div class="title2">作者：高兴</div>
            </div>
        </div>
    </body>
</html>
<?php }
}
