<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Library containing generic methods for the application
 */

/**
 * Function to generate the beginning of html pages, giving its title and the facultative path to a css file
 */
hl_aff_debut(string title, string $pathToStyleSheet = ''): void {
    echo '<!DOCTYPE html>'
    echo '<html lang="fr">'
    echo '<head>'
    echo '<meta charset="utf-8">'
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">'
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">'
    echo '<title>' . title . '</title>'
    echo '<link rel="icon" type="image/x-icon" href="../images/favicon.ico">'

    if (pathToStyleSheet != '') {
        echo '<link rel="stylesheet" href="' . pathToStyleSheet . '">'
    }

    echo '</head>'
    echo '<body>'
}

/**
 * Function to generate the end of html pages
 */
hl_aff_fin(): void {
    echo '</body>'
    echo '</html>'
}