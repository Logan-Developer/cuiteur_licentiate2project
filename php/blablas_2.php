<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user of id 2 (v2 with css)
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

        echo '<li class="message-card">';
        if ($blablasUser2['usAvecPhoto']) {
            echo '<img src="../upload/' . $blablasUser2['blIDAuteur'] . '.jpg" alt="Photo profile">';
        }
        else {
            echo '<img src="../images/anonyme.jpg" alt="Photo profile">';
        }
        echo     '<h3>' . $blablasUser2['usPseudo'] . ' ' . $blablasUser2['usNom'] . '</h3>',
                 '<p>' . $blablasUser2['blTexte'] . '<br>',
                    '<span>' . hl_date_to_french_format($blablasUser2['blDate']) . ' à ' . hl_time_to_more_readable_format($blablasUser2['blHeure']),
                        '<a href="../index.html">Répondre</a><a href="../index.html">Recuiter</a>',
                    '</span>',
                '</p>',
             '</li>';
    } while ($blablasUser2 = mysqli_fetch_assoc($data));
}

$conn = hl_bd_connect();
$data = hl_aff_blablas($conn, 2);

$blablasUser2 = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)
$blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser2['usPseudo'], 'cuiteur.css');
hl_aff_entete(false, 'Les blablas de ' . $blablasUser2['usPseudo']);

hl_aff_infos();
hl_aff_messages_list($conn, $data, $blablasUser2);

mysqli_free_result($data);
mysqli_close($conn);

echo '</main>';
hl_aff_fin();