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
  function hl_aff_users(mysqli $db): mysqli_result {
      $query = 'SELECT * FROM users';
      return hl_bd_send_request($db, $query);
  }

  /**
   * Retrieve the list of blablas posted by user of id $user_id from blablas table of the cuiteur database
   * @param mysqli $db_link The database link
   * @param int $user_id The id of the user
   * @return mysqli_result The list of blablas
   */
  function hl_aff_blablas(mysqli $db, int $user_id): mysqli_result {
      // neutralize the user_id
        $user_id = mysqli_real_escape_string($db, $user_id);

      $query = 'SELECT usPseudo, usNom, blablas.*
                FROM blablas INNER JOIN users ON blIDAuteur = users.usID
                WHERE blIDAuteur = ' . $user_id . '
                ORDER BY blDate DESC, blHeure DESC';
      return hl_bd_send_request($db, $query);
  }