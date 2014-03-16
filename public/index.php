<?php
/**
 * FreeKore Php Framework
 * Version: 0.2 
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU  GENERAL PUBLIC LICENSE
 */


define('INCPATH', '../');
include(INCPATH.'freekore/start/start.php');

// Ejecutar FreeKore
fkore::InitializeFK($_GET);
