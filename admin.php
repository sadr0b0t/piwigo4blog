<?php
// Chech whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// better rename generated file with random name to main.js
//$template_react_main_js = "/plugins/piwigo4blog/static/js/main.85f77728.js";
$template_react_main_js = "/plugins/piwigo4blog/static/js/main.js";

// Fetch the template.
global $template;

// Add our template to the global template
$template->set_filenames(
    array(
        'plugin_admin_content' => dirname(__FILE__).'/admin.tpl'
    )
);

$template->assign('react_main_js', $template_react_main_js);

// should be here at the end
// Assign the template contents to ADMIN_CONTENT
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>

