<?php

/** 
 * Humbert Logan - 04/07/2022
 * 
 * Script to handle registration form submission (Only shows values submitted)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

hl_aff_debut('Inscription');

foreach($_POST as $value) {
    var_dump($value);
}

echo '<pre>';
print_r($_POST);
echo '</pre>';

hl_aff_fin();