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
   * Retrieve the list of blablas posted by user (including reposts) of id $user_id from blablas table of the cuiteur database
   * @param mysqli $db_link The database link
   * @param int $user_id The id of the user
   * @return mysqli_result The list of blablas
   */
  function hl_aff_blablas(mysqli $db, int $user_id): mysqli_result {
      // neutralize the user_id
        $user_id = mysqli_real_escape_string($db, $user_id);

      $query = 'SELECT users.usPseudo, users.usNom, users.usAvecPhoto, blablas.*, ORI.usPseudo AS usPseudoOri, ORI.usNom AS usNomOri
                FROM (blablas INNER JOIN users ON blIDAuteur = users.usID)
                LEFT OUTER JOIN users AS ORI ON blIDAutOrig = ORI.usID
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
                        '<h1>', $title, '</h1>',
                        '<img src="../images/trait.png" alt="Bordure titre">',
                      '</div>';
              }
      echo  '</header>';
  }

  /**
   * Generate the HTML code to display aside of the page with info about the user, tendances, suggestions
   * @return void
   */
  function hl_aff_infos(): void {
    echo '<aside>',
            '<section>',
              '<h2>Utilisateur</h2>',
              '<div class=image-box">',
                '<img src="../images/pdac.jpg" alt="Photo profile">',
                '<p><a title="Voir mes infos" href="../index.html">pdac</a> Pierre Dac</p>',
              '</div>',

              '<a title="Voir la liste des mes messages" href="../index.html">100 blablas</a>',
              '<a title="Voir les personnes que je suis" href="../index.html">123 abonnements</a>',
              '<a title="Voir les personnes qui me suivent" href="../index.html">34 abonnés</a>',
            '</section>',

            '<section>',
              '<h2>Tendances</h2>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">info</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">lol</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">imbécile</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">fairelafete</a></p>',

              '<a href="../index.html">Toutes les tendances</a>',
            '</section>',

            '<section>',
              '<h2>Suggestions</h2>',
              '<div class="image-box">',
                '<img src="../images/yoda.jpg" alt="Photo profile utilisateur suggéré">',
                '<p><a title="Voir les infos" href="../index.html">yoda</a> Yoda</p>',
              '</div>',

              '<div class="image-box">',
                '<img src="../images/paulo.jpg" alt="Photo profile utilisateur suggéré">',
                '<p><a title="Voir les infos" href="../index.html">paulo</a> Jean-Paul Sartre</p>',
              '</div>',

              '<a href="../index.html">Plus de suggestions</a>',
            '</section>',
          '</aside>';
  }

  /**
   * Generate the HTML code to display the footer of the page
   * @return void
   */
  function hl_aff_pied(): void {
    echo '<footer>',
            '<nav>',
              '<a href="../index.html">A propos</a>',
              '<a href="../index.html">Publicité</a>',
              '<a href="../index.html">Patati</a>',
              '<a href="../index.html">Aide</a>',
              '<a href="../index.html">Patata</a>',
              '<a href="../index.html">Stages</a>',
              '<a href="../index.html">Emplois</a>',
              '<a href="../index.html">Confidentialité</a>',
            '</nav>',
            '<img src="../images/pied.png" alt="Illustration pied de page">',
          '</footer>',
        '</main>';
  }