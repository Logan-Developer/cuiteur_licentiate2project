<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user of id 2
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

$conn = hl_bd_connect();
$data = hl_aff_blablas($conn, 2);

$blablasUser2 = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)
$blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser2['usPseudo']);
echo '<h1>Les blablas de ' . $blablasUser2['usPseudo'] . '</h1>';

do {
    // neutralize the eventual HTML code in the fields
    $blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);
    $blablasUser2['usNom'] = htmlspecialchars($blablasUser2['usNom']);
    $blablasUser2['blTexte'] = htmlspecialchars($blablasUser2['blTexte']);
    $blablasUser2['blDate'] = htmlspecialchars($blablasUser2['blDate']);
    $blablasUser2['blHeure'] = htmlspecialchars($blablasUser2['blHeure']);

    echo '<ul>',
            '<li>',
                $blablasUser2['usPseudo'], ' ' . $blablasUser2['usNom'], '<br>',
                $blablasUser2['blTexte'], '<br>',
                hl_date_to_french_format($blablasUser2['blDate']), ' à ', hl_time_to_more_readable_format($blablasUser2['blHeure']), '<br>',
            '</li>',
        '</ul>';
} while ($blablasUser2 = mysqli_fetch_assoc($data));

mysqli_free_result($data);
mysqli_close($conn);

hl_aff_fin();