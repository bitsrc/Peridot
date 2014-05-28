<?php

require "config.php";

$db = new PDO(CONN_STRING, CONN_USER, CONN_PASSWORD);
//Use PDO Exceptions
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function createShort($db,$dest,$user = NULL) {
    //Get a unique random identifier, create and then check to make sure it doesn't exist.
    do {
        $ident = createRandomIdentifier(IDENT_LENGTH);
    } while (!checkIdentifier($db,$ident));
    
    //Insert the redirect
    $stmt = $db->prepare("INSERT INTO redirect (ident, url, added, userID) VALUES (?, ?, NOW(), ?)");
    $stmt->execute(array($ident,$dest,$user));
}

function checkIdentifier($db,$ident) {
    //Find any rows with the ident already
    $stmt = $db->prepare("SELECT ident FROM redirect WHERE ident = ?");
    $stmt->execute(array($ident));
    
    //Check row count (should be 0 or 1)
    if ($stmt->rowCount() > 0) {
        return true;
    }
    
    return false;
}

function createRandomIdentifier($length) {
    //List of possible characters to use in an ident, removed i, l, I, and L, along with O and 0 to attempt to prevent typos
    $chars = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ1234567890';
    $ident = '';
    
    //Select a random character from the string and append
    for ($i = 0; $i < $length; $i++) {
        $ident .= $chars[mt_rand(0,strlen($chars) - 1)];
    }
    
    return $ident;
}

function getUrlData($db,$ident) {
    $stmt = $db->prepare("SELECT ident, url, hits, added, name
                            FROM redirect
                            INNER JOIN users ON redirect.userID = user.id
                            WHERE redirect.ident = ?");
    $stmt->execute(array($ident));
    
    try {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getIdentByURL($db,$url) {
    $stmt = $db->prepare("SELECT ident FROM redirect WHERE url = ?");
    
    $stmt->execute(array($url));
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser($db,$name) {
    $apiKey = createRandomIdentifier(APIKEY_LENGTH);
    
    $stmt = $db->prepare("INSERT INTO users (name,apikey) VALUES (?, ?)");
    try {
        $stmt->execute(array($name,$apiKey));
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function updateUserApiKey($db,$user) {
    $apiKey = createRandomIdentifier(APIKEY_LENGTH);
    
    $stmt = $db->prepare("UPDATE users SET apikey = ? WHERE id = ?");
    try {
        $stmt->execute(array($apiKey,$user));
        return true;
    } catch (PDOException $e) {
        return false;
    }

}

function getUserFromKey($db,$key) {
    $stmt = $db->prepare("SELECT id,name FROM users WHERE apikey = ?");
    $stmt->execute(array($key));
    
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return false;
}

if (isset($_GET['id'])) {
    //redirect
    $data = getUrlData($db,$_GET['id']);
    http_redirect($data['url']);
} elseif (isset($_POST['name'])) {
    //Creating user
    createUser($db,$_POST['name']);
} elseif (isset($_POST['url'])) {
    //Creating redirect
    
    if (isset($_POST['key'])) {
        if (($user = getUserFromKey($db,$_POST['key'])) {
            createShort($db,$_POST['url'],$user['id']);
        } else {
            //Invalid key
            
        }
    } elseif (ALLOW_PUBLIC) {
        //Anonymous redirect
        createShort($db,$_POST['url']);
    } else {
        //No key given, and not public
          
    }
}



?>