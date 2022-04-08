<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Script to list all users
 */

 define('IS_DEV', true);
 define('DB_SERVER', 'localhost');
 define('DB_NAME', 'cuiteur_bd');
 define('DB_USER', 'cuiteur_userl');
 define('DB_PASS', 'cuiteur_passl');

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

/**
 * Open the connection to the database by handling errors
 * 
 * If an error occurs, a clean page is displayed with the error message
 * @return mysqli The connection to the database
 */
function hl_bd_connect(): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    }
    catch (mysqli_sql_exception $e) {
        $err = array(
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'title' => 'Erreur de connexion à la base de données',
            'others' => array(
                'Nom du serveur' => DB_SERVER,
                'Nom de la base de données' => DB_NAME,
                'Nom d\'utilisateur' => DB_USER,
                'Mot de passe' => DB_PASS
            ),
            'backtrace' => $e->getTraceAsString()
        );
        hl_bd_erreur_exit($err);
    }

    try {
        mysqli_set_charset($conn, 'utf8');
        return $conn;
    }
    catch (mysqli_sql_exception $e) {
        $err = array(
            'code' => $e->getCode(),
            'title' => 'Erreur lors de la définition du jeu de caractères',
            'message' => $e->getMessage(),
            'backtrace' => $e->getTraceAsString()
        );
        hl_bd_erreur_exit($err);
    }
}

/**
 * Send a query to the database
 * @param mysqli $db The connection to the database
 * @param string $query The sql query to send
 * @return mysqli_result The result of the query
 */
function hl_bd_send_request(mysqli $db, string $query): mysqli_result|bool {
    try {
        return mysqli_query($db, $query);
    }
    catch (mysqli_sql_exception $e) {
        $err = array(
            'code' => $e->getCode(),
            'title' => 'Erreur lors de l\'envoi de la requête',
            'message' => $e->getMessage(),
            'backtrace' => $e->getTraceAsString(),
            'others' => array(
                'Requête' => $query
            )
        );
        hl_bd_erreur_exit($err);
    }
}

/**
 * Convert date to french format (dd month yyyy)
 * @param string $date The date to convert (yyyymmdd)
 * @return string The date in french format (dd month yyyy)
 */
function hl_date_to_french_format(string $date): string {
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);

    $months = array(
        '01' => 'janvier',
        '02' => 'février',
        '03' => 'mars',
        '04' => 'avril',
        '05' => 'mai',
        '06' => 'juin',
        '07' => 'juillet',
        '08' => 'août',
        '09' => 'septembre',
        '10' => 'octobre',
        '11' => 'novembre',
        '12' => 'décembre'
    );
    return $day.' '.$months[$month].' '.$year;
}

/**
 * Convert time from HH:MM:SS to HHhmn
 * @param string $time The time to convert (HH:MM:SS)
 * @return string The converted time (HHhmn)
 */
function hl_time_to_more_readable_format(string $time): string {
    $hours = substr($time, 0, 2);
    $minutes = substr($time, 3, 2);
    return $hours.'h'.$minutes . 'mn';
}

/**
 * Display errors in a clean page
 * @param array $err The error to display
 * - code: The error code
 * - title: The error title
 * - message: The error message
 * - others: The other error details
 * @return void
 */
function hl_aff_erreur_exit(array $err) {
    ob_end_clean();
    hl_aff_debut('Erreur');

    echo '<h4>Une erreur est survenue</h4>',
         '<p>Nous sommes désolés de cette situation. Merci de réessayer ultérieurement.</p>';

    if (IS_DEV) {
        echo '<pre>',
                '<strong>Erreur :</strong> ',
                $err['title'],
                '<br>',
                '<strong>Message :</strong> ',
                $err['message'];
        if (isset($err['others'])) {
            echo '<br>',
                 '<strong>Autres informations :</strong> ',
                 '<br>';
            foreach($err['others'] as $key => $value){
                echo '<strong>'.$key.' :</strong> ', $value, '<br>';
            }
        }
        echo '</pre>';
    }
    hl_aff_fin();

    // Save info in log file
    if (!IS_DEV) {
        $file = fopen('error.log', 'a');
        if($file){
            fwrite($file, '['.date('d/m/Y').' '.date('H:i:s')."]\n");
            fwrite($file, $err['title']."\n");
            fwrite($file, utf8_encode($err['message'])."\n");
            if (isset($err['others'])){
                foreach($err['others'] as $key => $value){
                    fwrite($file,"{$key} :\n{$value}\n");
                }
            }
            fclose($file);
        }
    }
    exit(1);
}

/**
 * Check that all values submitted by form are valid
 * @param array $required The required fields
 * @param array $optional The optional fields
 * @return bool True if all values are valid, false otherwise
 */
function hl_verify_form_submission(array $required, array $optional = []): bool {
    $values = array_merge($required, $optional);
    foreach ($values as $value) {
        if (!isset($_POST[$value]) || $_POST[$value] === '') {
            return false;
        }
    }
    return true;
}

/**
 * Verify if the form field contains html code
 * @param string $string String to verify
 * @return bool True if the string contains html code, false otherwise
 */
function hl_contains_html_code(string $string): bool {
    $txt = trim($string);
    $noTags = strip_tags($txt);

    return $txt !== $noTags;
}