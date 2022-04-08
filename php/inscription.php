<?php

/** 
 * Humbert Logan - 04/07/2022
 * 
 * Script to show registration form
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

hl_aff_debut('Inscription', 'cuiteur.css');
hl_aff_entete(false, 'inscription');

echo '<aside></aside>'; // empty aside

hl_aff_form_registration();

hl_aff_pied();
hl_aff_fin();