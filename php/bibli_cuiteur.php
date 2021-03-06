<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Library containing specific methods for the application
 */

include_once './bibli_generale.php';

/**
 * Retrieve the list of users from the database
 * @param mysqli $bd the database connection
 * @return mysqli_result the list of users
 */
function hl_get_users(mysqli $bd): mysqli_result {
    $query = "SELECT * FROM `users`";
    return mysqli_query($bd, $query);
}

/**
 * Check if a user is registered, giving its pseudo
 * @param mysqli $bd the database connection
 * @param string $pseudo the user pseudo
 */
function hl_user_exists(mysqli $bd, string $pseudo): bool {
    $pseudo = mysqli_real_escape_string($bd, $pseudo);
    $query = "SELECT * FROM `users` WHERE `usPseudo` = '$pseudo'";
    $result = mysqli_query($bd, $query);

    $nbRows = mysqli_num_rows($result);
    mysqli_free_result($result);

    return $nbRows > 0;
}

/**
 * Retrieve the user info from the database
 * @param mysqli $bd the database connection
 * @param int $id the user id
 * @return mysqli_result the user info
 */
function hl_get_user_info(mysqli $bd, int $id): mysqli_result {
    $id = mysqli_real_escape_string($bd, $id);
    $query = "SELECT * FROM `users` WHERE `usId` = '$id'";
    return mysqli_query($bd, $query);
}

/**
 * Retrieve the list of messages posted by a user from the database
 * @param mysqli $bd the database connection
 * @param int $user_id the user id
 * @return mysqli_result the list of messages posted by the user
 */
function hl_get_blablas_from_user(mysqli $bd, int $user_id): mysqli_result {
    // neutralize the user_id
    $user_id = mysqli_real_escape_string($bd, $user_id);

    $query = 'SELECT users.usPseudo, users.usNom, users.usAvecPhoto, blablas.*, ORI.usPseudo AS usPseudoOri, ORI.usNom AS usNomOri
              FROM (users LEFT OUTER JOIN blablas ON blIDAuteur = users.usID)
              LEFT OUTER JOIN users AS ORI ON blIDAutOrig = ORI.usID
              WHERE users.usID = ' . $user_id . '
              ORDER BY blDate DESC, blHeure DESC';
    return mysqli_query($bd, $query);
}

/**
 * Retrieve the list of messages of a user's feed from the database (all messages posted by the user, posted by the user's followers, and the messages that mention the user)
 * @param mysqli $bd the database connection
 * @param int $user_id the user id
 * @param string $user_pseudo the user pseudo
 * @return mysqli_result the list of messages of a user's feed
 */
function hl_get_blablas_feed(mysqli $bd, int $user_id, $user_pseudo): mysqli_result {
    // neutralize the user_id
    $user_id = mysqli_real_escape_string($bd, $user_id);

    $query = 'SELECT users.usPseudo, users.usNom, users.usAvecPhoto, blablas.*, ORI.usPseudo AS usPseudoOri, ORI.usNom AS usNomOri
              FROM ((users LEFT OUTER JOIN blablas ON blIDAuteur = users.usID)
              LEFT OUTER JOIN users AS ORI ON blIDAutOrig = ORI.usID)
              LEFT OUTER JOIN mentions ON blID = meIDBlabla
              WHERE users.usID = ' . $user_id . '
              OR users.usID IN (SELECT eaIDAbonne FROM estabonne WHERE eaIDUser = ' . $user_id . ')
              OR meIDUser = ' . $user_id . '
              ORDER BY blDate DESC, blHeure DESC';
    return mysqli_query($bd, $query);
}

/**
 * Insert a new user in the database
 * @param mysqli $bd the database connection
 * @param string $nom the user's name
 * @param string $pseudo the user's pseudo
 * @param string $mail the user's mail
 * @param string $passe the user's password
 * @param string $dateNaissance the user's date of birth
 * @param string $dateInscription the user's date of inscription
 * @return int the id of the new user (0 if an error occurred)
 */
function hl_insert_user(mysqli $bd, string $nom, string $pseudo, string $mail, string $passe, string $dateNaissance, $dateInscription): int {
    $query = 'INSERT INTO users (usNom, usPseudo, usMail, usPasse, usDateNaissance, usDateInscription, usVille, usWeb, usBio)
              VALUES ("' . $nom . '", "' . $pseudo . '", "' . $mail . '", "' . $passe . '", "' . $dateNaissance . '", "' . $dateInscription . '", "", "", "")';
    mysqli_query($bd, $query);
    return mysqli_insert_id($bd);
}

 /**
  * Display the users information
  * @param mysqli $db_link The database link
  * @param bool $datesInFrenchFormat If true, the dates are displayed in french format, otherwise YYYYMMDD  (default: false)
  * @return void
  */
  function hl_aff_users(mysqli_result $users, bool $datesInFrenchFormat = false) {
      while($user = mysqli_fetch_assoc($users)) {
        // neutralize the eventual HTML code in the fields
        $user['usID'] = htmlspecialchars($user['usID']);
        $user['usPseudo'] = htmlspecialchars($user['usPseudo']);
        $user['usNom'] = htmlspecialchars($user['usNom']);
        $user['usDateInscription'] = htmlspecialchars($user['usDateInscription']);
        $user['usVille'] = htmlspecialchars($user['usVille']);
        $user['usWeb'] = htmlspecialchars($user['usWeb']);
        $user['usMail'] = htmlspecialchars($user['usMail']);
        $user['usDateNaissance'] = htmlspecialchars($user['usDateNaissance']);
        $user['usBio'] = htmlspecialchars($user['usBio']);

        if ($datesInFrenchFormat) {
          $user['usDateInscription'] = hl_date_to_french_format($user['usDateInscription']);
          $user['usDateNaissance'] = hl_date_to_french_format($user['usDateNaissance']);
        }
     
        echo '<h2>Utilisateur ', $user['usID'], '</h2>',
           '<ul>',
              '<li>Pseudo : ', $user['usPseudo'], '</li>',
              '<li>Nom : ', $user['usNom'], '</li>',
              '<li>Inscription : ', $user['usDateInscription'], '</li>',
              '<li>Ville : ', $user['usVille'], '</li>',
              '<li>Web : ', $user['usWeb'], '</li>',
              '<li>Mail : ', $user['usMail'], '</li>',
              '<li>Naissance : ', $user['usDateNaissance'], '</li>',
              '<li>Bio : ', $user['usBio'], '</li>',
           '</ul>';
      }
  }

  /**
 * Generate the list of messages posted or reposted by user
 * @param mysqli_result $data list of messages posted or reposted by user
 * @param array $blablasUser array containing the first row of the result
 * @return string HTML code of the list of messages posted or reposted by user
 */
function hl_aff_blablas(mysqli_result $data, array $blablasUser = null) {
  if ($blablasUser == null) {
    $blablasUser = mysqli_fetch_assoc($data); // get the first row of the result if it is not provided
  }

  echo '<ul id="messages">';
       
  do {
      // neutralize the eventual HTML code in the fields
      $blablasUser['usPseudo'] = htmlspecialchars($blablasUser['usPseudo']);
      $blablasUser['usNom'] = htmlspecialchars($blablasUser['usNom']);
      $blablasUser['blTexte'] = htmlspecialchars($blablasUser['blTexte']);
      $blablasUser['blDate'] = htmlspecialchars($blablasUser['blDate']);
      $blablasUser['blHeure'] = htmlspecialchars($blablasUser['blHeure']);
      $blablasUser['usAvecPhoto'] = htmlspecialchars($blablasUser['usAvecPhoto']);

      // Default photo if user has no photo uploaded
      echo '<li class="message-card">';
      if (!$blablasUser['usAvecPhoto']) {
          echo '<img src="../images/anonyme.jpg" alt="Photo profile">';
      }

      // User's info for reposted messages
      if ($blablasUser['blIDAutOrig'] != null) {
          if ($blablasUser['usAvecPhoto']) {
              echo '<img src="../upload/' . $blablasUser['blIDAutOrig'] . '.jpg" alt="Photo profile">';
          }

          echo    '<h3>',
                      '<a href="utilisateur.php?id=' . $blablasUser['blIDAutOrig'] . '">' . $blablasUser['usPseudoOri'] . '</a>',
                      ' ', $blablasUser['usNomOri'],
                      ', recuit?? par <a href="utilisateur.php?id=', $blablasUser['blIDAuteur'], '">', $blablasUser['usPseudo'], '</a>';
                  '</h3>';
      }

      // User's info for original messages
      else {
          if ($blablasUser['usAvecPhoto']) {
              echo '<img src="../upload/' . $blablasUser['blIDAuteur'] . '.jpg" alt="Photo profile">';
          }

          echo    '<h3>',
                      '<a href="utilisateur.php?id=' . $blablasUser['blIDAuteur'] . '">' . $blablasUser['usPseudo'] . '</a>',
                      ' ', $blablasUser['usNom'],
                  '</h3>';
      }

      echo    '<p>', $blablasUser['blTexte'], '<br>',
                  '<span>', hl_date_to_french_format($blablasUser['blDate']), ' ?? ', hl_time_to_more_readable_format($blablasUser['blHeure']),
                      '<a href="../index.html">R??pondre</a><a href="../index.html">Recuiter</a>',
                  '</span>',
              '</p>',
           '</li>';
  } while ($blablasUser = mysqli_fetch_assoc($data));
  echo '</ul>';
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
                '<a title="Se d??connecter de cuiteur" href="../index.html"><img src="../images/deconnexion.png" alt="Bouton d??connexion"></a>',
                '<a title="Ma page d\'accueil" href="../index.html"><img src="../images/home.png" alt="Bouton accueil"></a>',
                '<a title="Rechercher des personnes ?? suivre" href="../index.html"><img src="../images/cherche.png" alt="Bouton recherche"></a>',
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
              '<a title="Voir les personnes qui me suivent" href="../index.html">34 abonn??s</a>',
            '</section>',

            '<section>',
              '<h2>Tendances</h2>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">info</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">lol</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">imb??cile</a></p>',
              '<p># <a title="Voir les messages contenant ce tag" href="../index.html">fairelafete</a></p>',

              '<a href="../index.html">Toutes les tendances</a>',
            '</section>',

            '<section>',
              '<h2>Suggestions</h2>',
              '<div class="image-box">',
                '<img src="../images/yoda.jpg" alt="Photo profile utilisateur sugg??r??">',
                '<p><a title="Voir les infos" href="../index.html">yoda</a> Yoda</p>',
              '</div>',

              '<div class="image-box">',
                '<img src="../images/paulo.jpg" alt="Photo profile utilisateur sugg??r??">',
                '<p><a title="Voir les infos" href="../index.html">paulo</a> Jean-Paul Sartre</p>',
              '</div>',

              '<a href="../index.html">Plus de suggestions</a>',
            '</section>',
          '</aside>';
  }

  /**
   * Generate the HTML code to display the registration form
   * @param array $content The values filled in the form (optional)
   * @return void
   */
  function hl_aff_form_registration(array $content = []): void {
    echo '<form id="registration" method="post" action="./inscription_5.php">',
            '<p>Pour vous inscrire, merci de fournir les informations suivantes.</p>',
            '<br>',
            '<table>';
              hl_aff_ligne_input('pseudo', 'text', 'Votre pseudo', $content['pseudo'] ?? '', true);
              hl_aff_ligne_input('passe1', 'password', 'Votre mot de passe', '', true);
              hl_aff_ligne_input('passe2', 'password', 'R??p??tez votre mot de passe', '', true);
              hl_aff_ligne_input('nomprenom', 'text', 'Nom et pr??nom', $content['nomprenom'] ?? '', true);
              hl_aff_ligne_input('email', 'email', 'Votre adresse mail', $content['email'] ?? '', true);
              hl_aff_ligne_input('naissance', 'date', 'Votre date de naissance', $content['naissance'] ?? '', true);

    echo      '<tr>',
                '<td colspan="2">',
                  '<button type="submit" name="btnSInscrire" value="S\'inscrire">S\'inscrire</button>',
                  '<button type="reset" value="R??initialiser">R??initialiser</button>
                </td>',
              '</tr>',
            '</table>',
          '</form>';
  }

  /**
   * Generate the HTML code to display the footer of the page
   * @return void
   */
  function hl_aff_pied(): void {
    echo '<footer>',
            '<nav>',
              '<a href="../index.html">A propos</a>',
              '<a href="../index.html">Publicit??</a>',
              '<a href="../index.html">Patati</a>',
              '<a href="../index.html">Aide</a>',
              '<a href="../index.html">Patata</a>',
              '<a href="../index.html">Stages</a>',
              '<a href="../index.html">Emplois</a>',
              '<a href="../index.html">Confidentialit??</a>',
            '</nav>',
            '<img src="../images/pied.png" alt="Illustration pied de page">',
          '</footer>',
        '</main>';
  }