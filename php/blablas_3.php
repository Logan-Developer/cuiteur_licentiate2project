<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user of id 2 (v3 distinction between posted and reposted messages)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

$conn = hl_bd_connect();
$data = hl_get_blablas_from_user($conn, 2);

$blablasUser = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)
$blablasUser['usPseudo'] = htmlspecialchars($blablasUser['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser['usPseudo'], 'cuiteur.css');
hl_aff_entete(false, 'Les blablas de ' . $blablasUser['usPseudo']);

hl_aff_infos();
hl_aff_blablas($data, $blablasUser);

mysqli_free_result($data);
mysqli_close($conn);

hl_aff_pied();
hl_aff_fin();