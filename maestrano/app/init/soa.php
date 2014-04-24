<?php
//-----------------------------------------------
// Define root folder and load base
//-----------------------------------------------
if (!defined('MAESTRANO_ROOT')) {
  define("MAESTRANO_ROOT", realpath(dirname(__FILE__) . '/../../'));
}
require_once MAESTRANO_ROOT . '/app/init/base.php';

error_reporting(0);

//-----------------------------------------------
// Require your app specific files here
//-----------------------------------------------
define('APP_DIR', realpath(MAESTRANO_ROOT . '/../'));
chdir(APP_DIR);
require_once APP_DIR . '/GO.php';

//-----------------------------------------------
// Perform your custom preparation code
//-----------------------------------------------
// If you define the $opts variable then it will
// automatically be passed to the MnoSsoUser object
// for construction
// e.g:
$opts = array();
$opts['db_connection'] = GO::getDbConnection();

MnoSoaDB::initialize($opts['db_connection']);
MnoSoaLogger::initialize();

?>