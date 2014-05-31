<?php

require "peridot.php";

$p = new Peridot();

if (isset($_GET['id'])) {
    //redirect or preview
    if (($data = $p->getUrlData($_GET['id']))) {
        
        $data['url'] = htmlspecialchars($data['url'], ENT_QUOTES, 'UTF-8');
        
        if (isset($_GET['preview'])) {
            //Preview
            
            //If there is a user provided, display username
            if ($data['userID'] != NULL) {
                $user = $p->getUserById($data['userID']);
                $data['name'] = $user['name'];
            } else {
                //Else use a placeholder
                $data['name'] = 'Anonymous Coward';
            }
            
            displayView('views/preview.php',$data);
        } else {
            //Redirect
            header("HTTP/1.1 301 Moved Permanently"); 
            header("Location: {$data['url']}");
            
            displayView('views/redirect.php',$data, false);
            
            $p->incrementRedirectHits($data['ident']);
        }
    } else {
        //No such ident
        displayError("Non-existent identifier.");
    }
} elseif (isset($_POST['name'])) {
    //Creating user
    $p->createUser($_POST['name']);
} elseif (isset($_POST['url'])) {
    //Creating redirect
    
    if (isset($_POST['key'])) {
        if (($user = $p->getUserByKey($_POST['key']))) {
            $p->createShort($_POST['url'],$user['id']);
        } else {
            //Invalid key
            displayError("Invalid API key");
        }
    } elseif (ALLOW_PUBLIC) {
        //Anonymous redirect
        $p->createShort($_POST['url']);
    } else {
        //No key given, and not public
        displayError("No API key was provided, and anonymous redirect creation is disabled.");
    }
} else {
    displayView('views/create.php');
}

?>