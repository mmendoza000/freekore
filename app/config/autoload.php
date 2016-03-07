<?php 
/*--------------------------------------------------------------
 / Auto Load file defines which libraries, models, plugins , etc. 
 / needs to be loaded when program starts.
 / -------------------------------------------------------------*/

/*---------------------------------------------------
/   freekore : /freekore/
/---------------------------------------------------*/
/*
 /  freekore : This is the section for freekore native libraries at /freekore/
 /    - libs : freekore/libs
 /    - debug : freekore/debug
 /    - database : freekore/db/* & freekore/db/adapters/{configured_adapter} 
 */

//libs
//Example: $autoload['freekore-libs'] = array('security','appform','applist','ajax','log4php/src/main/php/Logger');
// ajax: allows to execute ajax object
// security: use it to implements sesion security on the app
// appform: to create appforms
// applist: to create dinamic tables.    
//$autoload['freekore-libs'] = array('log4php/src/main/php/Logger');
$autoload['freekore-libs'] = array();

/*
/ debug : Runs when app activated debug flag is on
/ freekore/debug
*/
//Example: $autoload['freekore-debug'] = array('general');
// general: library to use when debug mode is activated.  
$autoload['freekore-debug'] = array('general');


/*
/ $autoload['database'] is an special autoload parameter 
/ Database functions are autoloaded when $autoload['database'] = true
/ Set to true, wheater database function needs to be executed  
/ almost all time your application runs
*/ 
$autoload['database'] = false;




/*---------------------------------------------------
/   app : /app/
/---------------------------------------------------*/
/*
 /   app : This is for your application folder, located at /app/
 /    - models
 /    - plugins
 /    - libraries
 /    - helpers
 */

// Models
$autoload['models'] = array();

//Plugins
$autoload['plugins'] = array();

//Libraries
$autoload['libraries'] = array('utils');


//Helpers
$autoload['helpers'] = array();


// Send auto load data to a global variable
// Note: don't remove or change this statement 
$GLOBALS['autoload'] = $autoload;