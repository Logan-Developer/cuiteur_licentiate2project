<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user specified by id (v4 user specified by id)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

/**
 * Generate the list of messages posted or reposted by user of id 2
 * @param mysqli $conn database connection
 * @param mysqli_result $data list of messages posted or reposted by user of id 2
 * @param array $blablasUser2 array containing the first row of the result
 * @return string HTML code of the list of messages posted or reposted by user of id 2
 */
function hl_aff_messages_list(mysqli $db, mysqli_result $data, array $blablasUser2) {
    echo '<ul id="messages">';
         
    do {
        // neutralize the eventual HTML code in the fields
        $blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);
        $blablasUser2['usNom'] = htmlspecialchars($blablasUser2['usNom']);
        $blablasUser2['blTexte'] = htmlspecialchars($blablasUser2['blTexte']);
        $blablasUser2['blDate'] = htmlspecialchars($blablasUser2['blDate']);
        $blablasUser2['blHeure'] = htmlspecialchars($blablasUser2['blHeure']);
        $blablasUser2['usAvecPhoto'] = htmlspecialchars($blablasUser2['usAvecPhoto']);

        // Default photo if user has no photo uploaded
        echo '<li class="message-card">';
        if (!$blablasUser2['usAvecPhoto']) {
            echo '<img src="../images/anonyme.jpg" alt="Photo profile">';
        }

        // User's info for reposted messages
        if ($blablasUser2['blIDAutOrig'] != null) {
            if ($blablasUser2['usAvecPhoto']) {
                echo '<img src="../upload/' . $blablasUser2['blIDAutOrig'] . '.jpg" alt="Photo profile">';
            }

            echo    '<h3>',
                        '<a href="utilisateur.php?id=' . $blablasUser2['blIDAutOrig'] . '">' . $blablasUser2['usPseudoOri'] . '</a>',
                        ' ', $blablasUser2['usNomOri'],
                        ', recuité par <a href="utilisateur.php?id=', $blablasUser2['blIDAuteur'], '">', $blablasUser2['usPseudo'], '</a>';
                    '</h3>';
        }

        // User's info for original messages
        else {
            if ($blablasUser2['usAvecPhoto']) {
                echo '<img src="../upload/' . $blablasUser2['blIDAuteur'] . '.jpg" alt="Photo profile">';
            }

            echo    '<h3>',
                        '<a href="utilisateur.php?id=' . $blablasUser2['blIDAuteur'] . '">' . $blablasUser2['usPseudo'] . '</a>',
                        ' ', $blablasUser2['usNom'],
                    '</h3>';
        }

        echo    '<p>', $blablasUser2['blTexte'], '<br>',
                    '<span>', hl_date_to_french_format($blablasUser2['blDate']), ' à ', hl_time_to_more_readable_format($blablasUser2['blHeure']),
                        '<a href="../index.html">Répondre</a><a href="../index.html">Recuiter</a>',
                    '</span>',
                '</p>',
             '</li>';
    } while ($blablasUser2 = mysqli_fetch_assoc($data));
    echo '</ul>';
}


// Verify that there is only one key value pair in the $_GET array
if (count($_GET) != 1) {
    hl_aff_erreur_exit(['title' => 'Il doit y avoir exactement un paramètre dans l\'URL',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the id=usID key value pair exists in the $_GET array
if (!array_key_exists('id', $_GET)) {
    hl_aff_erreur_exit(['title' => 'Le couple clé/valeur id=usID n\est pas présent dans l\'URL',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the id=usID key value pair is an integer and is greater than 0
if (!is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    hl_aff_erreur_exit(['title' => 'Le paramètre id=usID n\'est pas un entier positif',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}


$conn = hl_bd_connect();
$data = hl_aff_blablas($conn, $_GET['id']);

$blablasUser2 = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)

// Verify that the user of specified id exists
if ($blablasUser2 == null) {
    hl_aff_erreur_exit(['title' => 'L\'utilisateur n\'existe pas',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the user of specified id posted at least one message
if ($blablasUser2['blIDAuteur'] == null) {
    hl_aff_erreur_exit(['title' => 'L\'utilisateur n\'a pas posté de message',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

$blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser2['usPseudo'], 'cuiteur.css');
hl_aff_entete(false, 'Les blablas de ' . $blablasUser2['usPseudo']);

hl_aff_infos();
hl_aff_messages_list($conn, $data, $blablasUser2);

mysqli_free_result($data);
mysqli_close($conn);

hl_aff_pied();
hl_aff_fin();