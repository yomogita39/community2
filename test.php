<?php

    require_once("Smarty/Smarty.class.php");
    require_once('./include/line_define.php');
	require_once('./include/common.php');
    
    $smarty = new Smarty;
    
    $smarty->template_dir = "./templates/";
    $smarty->compile_dir  = "./templates_c/";
    
    $smarty->display("test.tpl");

?>