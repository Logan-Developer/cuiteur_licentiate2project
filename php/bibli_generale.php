<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Script to list all users
 */

 define('IS_DEV', true);
 define('DB_SERVER', 'localhost');
 define('DB_NAME', 'lepetit_cuiteur');
 define('DB_USER', 'lepetit_u');
 define('BD_PASS', 'lepetit_p');
 define('BD_PASS', 'lepetit_p');

 /**
  * Function to generate the HTML code to display the start of the page
  * @param string $title The title of the page
  * @param string $css_file The CSS file to use (optional)
  *
  * @return void
  */
function hl_aff_debut(string $title, string $style = ''): void {
    echo '<!DOCTYPE html>',
    '<html lang="fr">',
    '<head>',
        '<meta charset="UTF-8">',
        '<meta http-equiv="X-UA-Compatible" content="ie=edge">',
        '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        '<title>', $title, '</title>',
        '<link rel="icon" type="image/x-icon" href="../images/favicon.ico">';
    if ($style != '') {
        echo '<link rel="stylesheet" href="../styles/' . $style . '">';
    }
    echo '</head>
    <body>';
}
/**
* Function to generate the HTML code to display the end of the page
* @return void
*/
function hl_aff_fin(): void {
    echo '</body>
    </html>';
}

/**
 * Function to handle database errors
 * @param array $error_array The array containing the errors
 *  - $error_array['code'] The error code
 *  - $error_array['message'] The error message
 *  - $error_array['title'] The error title
 *  - $error_array['other'] The other error information
 * @return void
 */
function hl_bd_erreur_exit(array $err): void {
    ob_end_clean();
    
    hl_aff_debut('Erreur');

    if (IS_DEV) { // If in dev mode, show all info contained in $err
        echo '<h4>', $err['title'], '</h4>',
             '<pre>',
                '<strong>Erreur mysqli</strong> : ', $err['code'], "\n",
                utf8_encode($err['message']), "\n";

        if (isset($err['others'])) {
            echo "\n";
            foreach($err['others'] as $key => $value) {
                echo '<strong>', $key, '</strong> : ', "\n", $value, "\n";
            }
        }
        echo "\n", '<strong>Pile des appels de fonction :</strong>', "\n", $err['backtrace'],
             '</pre>';
    }
    else {
        echo '<h4>Une erreur est survenue</h4>',
             '<p>Nous sommes désolés de cette situation. Merci de réessayer ultérieurement.</p>';
    }
    hl_aff_fin();

    if (! IS_DEV) { // Save errors in a log file if not in dev mode
        $file = fopen('error.log', 'a');
        if($file){
            fwrite($file, '['.date('d/m/Y').' '.date('H:i:s')."]\n");
            fwrite($file, $err['title']."\n");
            fwrite($file, "Erreur mysqli : {$err['code']}\n");
            fwrite($file, utf8_encode($err['message'])."\n");
            if (isset($err['others'])){
                foreach($err['others'] as $key => $value){
                    fwrite($file,"{$key} :\n{$value}\n");
                }
            }
            fwrite($file,"Pile des appels de fonctions :\n");
            fwrite($file, "{$err['backtrace']}\n\n");
            fclose($file);
        } 
    }
    exit(1);
}