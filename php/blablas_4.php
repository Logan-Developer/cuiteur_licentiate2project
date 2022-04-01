<?php

/** 
 * Humbert Logan - 03/26/2022
 * 
 * Script to list all blablas (messages) posted by user specified by id (v4 user specified by id)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

// Verify that there is only one key value pair in the $_GET array
if (count($_GET) != 1) {
    hl_aff_erreur_exit(['title' => 'Il doit y avoir exactement un paramètre dans l\'URL',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the id=usID key value pair exists in the $_GET array
if (!array_key_exists('id', $_GET)) {
    hl_aff_erreur_exit(['title' => 'Le couple clé/valeur id=usID n\est pas présent dans l\'URL',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the id=usID key value pair is an integer and is greater than 0
if (!is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    hl_aff_erreur_exit(['title' => 'Le paramètre id=usID n\'est pas un entier positif',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

$conn = hl_bd_connect();
$data = hl_get_blablas_from_user($conn, $_GET['id']);

$blablasUser = mysqli_fetch_assoc($data); // retrieve the first row of the result (necessary for getting user's pseudo)

// Verify that the user of specified id exists
if ($blablasUser == null) {
    hl_aff_erreur_exit(['title' => 'L\'utilisateur n\'existe pas',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

// Verify that the user of specified id posted at least one message
if ($blablasUser['blIDAuteur'] == null) {
    hl_aff_erreur_exit(['title' => 'L\'utilisateur n\'a pas posté de message',
        'message' => 'Cette page ne peut être appelée en passant en paramètre id="id de l\'utilisateur"']);
}

$blablasUser['usPseudo'] = htmlspecialchars($blablasUser['usPseudo']);

hl_aff_debut('Les blablas de ' . $blablasUser['usPseudo'], 'cuiteur.css');
hl_aff_entete(false, 'Les blablas de ' . $blablasUser['usPseudo']);

hl_aff_infos();
hl_aff_blablas($data, $blablasUser);

mysqli_free_result($data);
mysqli_close($conn);

hl_aff_pied();
hl_aff_fin();