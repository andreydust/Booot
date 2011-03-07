<?php

class PasswdAuth {
    
  
    var $auth;
    
    var $htPasswdPath = '';
    
    // Where to go when authorized
    // If is is not set then PHP_SELF is used as a default
    var $authorizedUrl;
    
    // Where to go when not authorized
    var $notAuthorizedUrl;
    
    // The deliminator used in the passwd file
    var $deliminator = ':';
    
   
    function PasswdAuth($authorizedUrl = '', $notAuthorizedUrl = '') {
        global $PHP_SELF;
        
        $this->htPasswdPath = realpath(DIR.'/admin');
        
        $authorizedUrl == '' ? $this->authorizedUrl = $PHP_SELF : $this->authorizedUrl = $authorizedUrl;
        $this->notAuthorizedUrl = $notAuthorizedUrl;
    }
    
    
    function check($user = '') {
        global $PHP_AUTH_USER, $PHP_AUTH_PW;
        
        $this->auth = false;
        if (isset($PHP_AUTH_USER) && isset($PHP_AUTH_PW)) {
            
            $lines = &$this->_getUsersArray();
            
            foreach($lines as $line) {
                list($username, $password) = explode($this->deliminator, $line);
                if(($user != '') && ($username != $user)) {
                    continue;
                }
                if ($username == $PHP_AUTH_USER) {
                    $salt = substr($password ,0 ,2);
                    $cryptPasswd = crypt($PHP_AUTH_PW, $salt);
                    if($password == $cryptPasswd) {
                        $this->auth = true;
                        break;
                        
                    }
                }
            }
        }
        
        if(!$this->auth) {
            if($this->notAuthorizedUrl == '') {
                header('WWW-Authenticate: Basic realm="Private"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Authorization Required';
                exit;
            } else {
                header($this->notAuthorizedUrl);
            }
        } else {
            header($this->authorizedUrl);
        }
    }
    
    /*
        Returns true if the given user is found in the passwd file. False otherwise.
    */
    function checkUser($user) {
        $lines = $this->_getUsersArray();
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) == $user) {
                return true;
            }
        }
        return false;
    }
    
    /*
        Removes the given user from the passwd file. If the user was removed succesfully,
        true is returned, false otherwise (e.g.: if the user was not in the file, false
        is returned.
    */
    function deleteUser($user) {
        $lines = &$this->_getUsersArray();
        $usersArr = array();
        $result = false;
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) != $user) {
                $usersArr[] = $line;
            } else {
                $result = true;
            }
        }
        $this->_saveUsersArray($usersArr);
        return $result;
    }
    
    /*
        Adds the given user to the passwd file. If the user was added succesfully,
        true is returned, false otherwise (e.g.: if the user was already in the file, false
        is returned.
    */
    function addUser($user, $password) {
        if(!$this->checkUser($user)) {
            $usersArr = $this->_getUsersArray();
            $usersArr[] = $user.$this->deliminator.crypt($password);
            $this->_saveUsersArray($usersArr);
            return true;
        }
        return false;
    }
    
    /*
        Changes the password of the given user. If the password was changes succesfully,
        true is returned, false otherwise (e.g.: if the user was not in the file, false
        is returned.
    */
    function changePassword($user, $newPassword) {
        $lines = &$this->_getUsersArray();
        $usersArr = array();
        $changed = false;
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) != $user) {
                $usersArr[] = $line;
            } else {
                $usersArr[] = $user.$this->deliminator.crypt($newPassword);
                $changed = true;
            }
        }
        if($changed) {
            $this->_saveUsersArray($usersArr);
        }
        return $changed;
    }
    
    function getUsers() {
        $lines = $this->_getUsersArray();
        $users = array();
        foreach($lines as $line) {
            $users[] = $this->_retrieveUsername($line);
        }
        return $users;
    }
    
    /*
        PRIVATE FUNCTIONS
    */
    
    function _getUsersArray() {
        $filename = $this->htPasswdPath.'/.htpasswd';
        $fp = fopen($filename, 'r');
        $file_contents = fread($fp, filesize($filename));
        fclose($fp);
        
        //var_dump(trim($file_contents));
        
        return explode ("\n", trim($file_contents));
    }
    
    function _saveUsersArray(&$arr) {
        $file_contents = implode("\n", $arr);
        $filename = $this->htPasswdPath.'/.htpasswd';
        $fp = fopen($filename, 'w');
        fwrite ($fp, trim($file_contents));
        fclose($fp);
    }
    
    function _retrieveUsername($line) {
        return substr($line, 0, strrpos($line, $this->deliminator));
    }
    
    function _retrievePassword($line) {
        return substr($line, strrpos($line, $this->deliminator) + 1);
    }
}
        
?>