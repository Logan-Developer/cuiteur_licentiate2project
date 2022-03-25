<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Script to list all users
 */

 /**
  * Function to generate the HTML code to display the start of the page
  */
function hl_aff_debut($titre, $style = '') {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>' . $titre . '</title>';
    if ($style != '') {
        echo '<link rel="stylesheet" href="' . $style . '">';
    }
    echo '</head>
    <body>';
}
/**
* Function to generate the HTML code to display the end of the page
*/
function hl_aff_fin() {
    echo '</body>
    </html>';
}