<?php

use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp;

 define('VERSION', '1.0.0');

 //Using the CLI factory default services container. 
 $di = new CliDI();

 // Define path to application directory
 defined('APPLICATION_PATH')
 || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

 /**
  * Register the autoloader and tell it to register the tasks directory
  * Registre el cargador automático y dilo a registrar el directorio de tareas
  */
 $loader = new \Phalcon\Loader();
 $loader->registerDirs(
     array(
         APPLICATION_PATH . '/tasks'
     )
 );
 //Registro el Namespaces del directorio de la queues
 $loader->registerNamespaces(
   array(
        'Phalcon' => APPLICATION_PATH . '/vendor/phalcon/incubator/Library/Phalcon'
    )
);

 $loader->register();

 // Load the configuration file (if any) 
 // Cargar el archivo de configuración (si la hay)
 if(is_readable(APPLICATION_PATH . '/config/config.php')) {
     $config = include APPLICATION_PATH . '/config/config.php';
     $di->set('config', $config);
 }

 //Create a console application
 $console = new ConsoleApp();
 $console->setDI($di);

 /**
 * Process the console arguments
 */
 $arguments = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
        $arguments['params'][] = $arg;
     }
 }

 // define global constants for the current task and action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
     // handle incoming arguments
     $console->handle($arguments);
 }
 catch (\Phalcon\Exception $e) {
     echo $e->getMessage();
     exit(255);
 }