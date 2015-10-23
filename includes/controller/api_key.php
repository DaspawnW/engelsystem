<?php

/**
* url: /?p=api_key
* Returns api_key for user by basic authentication
* {
*   api_token: "TOKEN"
*  }
*The API_Key is important, to save the api before unallowed acces. Every user has his own api key and can change him, so the risc, that the api key is stolen is very small.
*/
function getAPIKey() {
  header("Content-Type: application/json; charset=utf-8");
  
  $user = $_SERVER["PHP_AUTH_USER"];
  $password = $_SERVER["PHP_AUTH_PW"];
  
  if($user == "" || $password == "") { // user is not authenticated
    
    header("WWW-Authenticate: Basic realm=Authorization Required");
    header("HTTP/1.1 401 unauthorized");
    echo "{\"error\": \"please send basic auth header\"}";
    die();
    
  } else { // check user
  
    $foundUser = sql_select("SELECT * FROM `User` WHERE `Nick`='" . sql_escape($user) . "'"); // find user by username
    if (count($foundUser) == 1) {
      $user = $foundUser[0];
      if (verify_password($password, $user['Passwort'], $user['UID'])) {
        echo "{\"api_token\": \"" . $user["api_key"] . "\"}"; 
      }
    } else {
      // TODO: handle wrong auth
      header("HTTP/1.1 403 Forbidden");
      echo "{\"error\": \"forbidden\"}";
    }
    
  die();
  }
    
  
  
}

?>
