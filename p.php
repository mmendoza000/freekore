<?
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL V2
 */
// Load page with mode_rewrite disabled
include_once('./app/start/start.php');
fk_ini_page($_GET,$mod_rewrite=false);
