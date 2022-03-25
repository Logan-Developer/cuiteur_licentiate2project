<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Library containing specific methods for the application
 */

include_once './bibli_generale.php';

 /**
  * Retrieve the list of users from users table of the cuiteur database
  * @param mysqli $db_link The database link
  * @return mysqli_result The list of users
  */
  function hl_bd_get_users(mysqli $db): mysqli_result {
      $query = 'SELECT * FROM users';
      return hl_bd_send_request($db, $query);
  }