<?php

/** 
 * Humbert Logan - 04/09/2022
 * 
 * Show id of connected user, and all his info from the database
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

if (!hl_est_authentifie()) {
    header('Location: ../index.php');
}

hl_aff_debut('Votre identité');
echo '<h1>Accès restreint aux utilisateurs authentifiés</h1>',
     '<br>',
     '<ul>',
        '<li><strong>ID : ', $_SESSION['usId'], '</strong></li>',
        '<li>SID : ', session_id(), '</li>';

$conn = hl_bd_connect();
$userInfo = mysqli_fetch_assoc(hl_get_user_info($conn, $_SESSION['usId']));

echo    '<li>usID : ', $userInfo['usId'], '</li>',
        '<li>usNom : ', $userInfo['usNom'], '</li>',
        '<li>usVille : ', $userInfo['usVille'], '</li>',
        '<li>usWeb : ', $userInfo['usWeb'], '</li>',
        '<li>usMail : ', $userInfo['usMail'], '</li>',
        '<li>usPseudo : ', $userInfo['usPseudo'], '</li>',
        '<li>usPasse : ', $userInfo['usPasse'], '</li>',
        '<li>usBio : ', $userInfo['usBio'], '</li>',
        '<li>usDateNaissance : ', $userInfo['usDateNaissance'], '</li>',
        '<li>usDateInscription : ', $userInfo['usDateInscription'], '</li>',
        '<li>usAvecPhoto : ', $userInfo['usAvecPhoto'], '</li>',
    '<ul>';

mysqli_free_result($data);
mysqli_close($conn);
    
hl_aff_fin();