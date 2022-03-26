<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user of id 2 (v2 with css)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

$conn = hl_bd_connect();
$data = hl_aff_blablas($conn, 2);

$blablasUser2 = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)
$blablasUser2['usPseudo'] = htmlspecialchars($blablasUser2['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser2['usPseudo'], 'cuiteur.css');
hl_aff_entete(false, 'Les blablas de ' . $blablasUser2['usPseudo']);
hl_aff_infos();

echo '</main>';
mysqli_free_result($data);
mysqli_close($conn);

hl_aff_fin();