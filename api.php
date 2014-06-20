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
    
    if ($action == "redirectInfo") {
        if (isset($_GET['id'])) {
            if (($data = $p->getUrlData($_GET['id']))) {
                outputResult($data);
            }
        } else {
            outputError("Malformed request");
        }
    } else {
        outputError("Invalid action");
    }
    
} elseif ((isset($_POST['action'])) && (isset($_POST['key']))) {
    if (($user = $p->getUserByKey($_POST['key']))) {        
        $action = $_POST['action'];
    
        if ($action == "createShort") {
            if (isset($_POST['url'])) {
                $ident = $p->createShort($_POST['url'],$user['id']);
                if ($ident) {
                    outputResult(array('ident' => $ident));
                } else {
                    outputError("Malformed URI");
                }
            } else {
                outputError("Malformed request");
            }
        } else {
            outputError("Invalid action");
        }
    } else {
        //Invalid key
        outputError("Invalid API key");
    }
} else {
    outputError("Malformed request");
}


?>