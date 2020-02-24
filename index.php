if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

$url = '../';
header( 'Request-URI: '.$url );
header( 'Content-Location: '.$url );
header( 'Location: '.$url );
exit();

