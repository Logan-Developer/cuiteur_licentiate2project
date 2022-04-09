<?php

/** 
 * Humbert Logan - 04/09/2022
 * 
 * Handle user deconnection
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

if (hl_est_authentifie()) {
    hl_session_exit('../index.php');
}