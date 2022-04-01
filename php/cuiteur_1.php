<?php

/** 
 * Humbert Logan - 04/01/2022
 * 
 * Script to list a user's feed
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

$conn = hl_bd_connect();
$data = hl_get_blablas_feed($conn, 23, 'nono');

hl_aff_debut('Votre fil Cuiteur', 'cuiteur.css');
hl_aff_entete(true);

hl_aff_infos();
hl_aff_blablas($data);

mysqli_free_result($data);
mysqli_close($conn);

hl_aff_pied();
hl_aff_fin();