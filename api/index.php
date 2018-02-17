<?php

require_once 'db.php';
require_once 'functions.php';

$acceptedMethods = array('regions','cities', 'districts', 'validate', 'users', 'registration', 'deleteuser', 'user');

if(isset($_REQUEST['task']) && !empty($_REQUEST['task'])){
    $task = $_REQUEST['task'];
    $params = isset($_REQUEST['params'])?$_REQUEST['params']:null;
    if(in_array($task, $acceptedMethods)){
        $functions = new Functions();
        $functions->actionMethod($task, $params);
    }
}
