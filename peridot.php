<?php

require "config.php";

class Peridot {
    function __construct() {
        $this->db = new PDO(CONN_STRING, CONN_USER, CONN_PASSWORD);
        //Use PDO Exceptions
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function createShort($dest,$user = NULL) {

        //Get a unique random identifier, create and then check to make sure it doesn't exist.
        do {
            $ident = createRandomIdentifier(IDENT_LENGTH);
        } while (!checkIdentifier($ident));
        
        //Should probably clean the URL somehow
        //$dest = 
        
        //Insert the redirect
        $stmt = $this->db->prepare("INSERT INTO redirect (ident, url, added, userID) VALUES (?, ?, NOW(), ?)");
        $stmt->execute(array($ident,$dest,$user));
    }

    function checkIdentifier($ident) {
        //Find any rows with the ident already
        $stmt = $this->db->prepare("SELECT ident FROM redirect WHERE ident = ?");
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

    function getUrlData($ident) {
        $stmt = $this->db->prepare("SELECT ident, url, hits, added, userID FROM redirect WHERE redirect.ident = ?");
        $stmt->execute(array($ident));
        
        try {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    function getIdentByURL($url) {
        $stmt = $this->db->prepare("SELECT ident FROM redirect WHERE url = ?");
        
        $stmt->execute(array($url));
                
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function incrementRedirectHits($ident) {
        $stmt = $this->db->prepare("UPDATE redirect SET hits = hits + 1 WHERE ident = ?");
        $stmt->execute(array($ident));
    }

    function createUser($name) {
        $apiKey = createRandomIdentifier(APIKEY_LENGTH);
        
        $stmt = $this->db->prepare("INSERT INTO user (name,apikey) VALUES (?, ?)");
        try {
            $stmt->execute(array($name,$apiKey));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    function updateUserApiKey($user) {
        $apiKey = createRandomIdentifier(APIKEY_LENGTH);
        
        $stmt = $this->db->prepare("UPDATE users SET apikey = ? WHERE id = ?");
        try {
            $stmt->execute(array($apiKey,$user));
            return true;
        } catch (PDOException $e) {
            return false;
        }

    }

    function getUserByKey($key) {
        $stmt = $this->db->prepare("SELECT id,name FROM user WHERE apikey = ?");
        $stmt->execute(array($key));
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }

    function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id,name FROM user WHERE id = ?");
        $stmt->execute(array($id));
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
}


function displayView($file, $vars = array()) {
    extract($vars);
    
    include $file;
}

function displayError($error) {
    displayView('views/error.php',array('error' => $error));
}

?>