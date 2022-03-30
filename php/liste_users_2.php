<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Script to list all users (v2 with dates in french format)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

hl_aff_debut('Liste des utilisateurs');

echo '<h1>Liste des utilisateurs</h1>';
$conn = hl_bd_connect();
hl_aff_users($conn, true);

hl_aff_fin();