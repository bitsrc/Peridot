<?php
require "peridot.php";

function outputResult($data) {
    echo json_encode($data);
}

function outputError($error) {
    //header();
    outputResult(array('error' => $error));
}


$p = new Peridot();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
} elseif ((isset($_POST['action'])) && (isset($_POST['key']))) {
    if (($user = $p->getUserByKey($_POST['key']))) {        
        $action = $_POST['action'];
    
        if ($action == "createShort") {
            if (isset($_POST['url'])) {
                $p->createShort($_POST['url'],$user['id']);
            } else {
                outputError("Malformed request");
            }
        }
    } else {
        //Invalid key
        outputError("Invalid API key");
    }
} else {
    outputError("Malformed request");
}


?>