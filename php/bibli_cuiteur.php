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

  /**
   * Generate the HTML code to display the header of the page, including or not the add blabla form
   * @param bool $display_form Whether or not to display the add blabla form
   * @param string $title The title of the page
   * @return void
   */
  function hl_aff_entete(bool $display_form, string $title = ''): void {
    echo '<main>',
            '<header>',
              '<nav>',
                '<a title="Se déconnecter de cuiteur" href="../index.html"><img src="../images/deconnexion.png" alt="Bouton déconnexion"></a>',
                '<a title="Ma page d\'accueil" href="../index.html"><img src="../images/home.png" alt="Bouton accueil"></a>',
                '<a title="Rechercher des personnes à suivre" href="../index.html"><img src="../images/cherche.png" alt="Bouton recherche"></a>',
                '<a title="Modifier mes informations personnelles" href="../index.html"><img src="../images/config.png" alt="Bouton profile"></a>',
              '</nav>';

              if ($display_form) {
                echo '<img src="../images/saisie.png" alt="Fond saisie message">',
                     '<form method="post" action="../index.html">',
                        '<textarea></textarea>',
                        '<button title="Publier mon message" type="submit"></button>',
                      '</form>';
              }
              else {
                echo '<div id="title">',
                        '<h1>' . $title . '</h1>',
                        '<img src="../images/trait.png" alt="Bordure titre">',
                      '</div>';
              }
      echo  '</header>';
  }