<?php

/** 
 * Humbert Logan - 04/08/2022
 * 
 * Script to show registration form (With errors verification + Database insertion)
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

/**
 * Redirect detected hackers to index.php
 */
function hl_expulse_hackers(): void {
    header('Location: ../index.php');
}

/**
 * Verification of the submitted data
 * @return array The eventual errors detected
 */
function hl_traitement_inscription(): array {
    $errors = [];

    if (!hl_verify_form_submission(array('pseudo', 'passe1', 'passe2', 'nomprenom', 'email', 'naissance', 'btnSInscrire'))) {
        $errors['form_incomplete'] = 'Vous n\'avez pas rempli tous les champs du formulaire.';
    }

    // verify pseudo
    if (!ctype_alnum($_POST['pseudo']) || strlen($_POST['pseudo']) < 4 || strlen($_POST['pseudo']) > 30) {
        $errors['pseudo'] = 'Le pseudo doit contenir entre 4 et 30 caractères alphanumériques.';
    }

    // verify password
    if (hl_contains_html_code($_POST['passe1'])) {
        hl_expulse_hackers();
    }
    if (strlen($_POST['passe1']) < 4 || strlen($_POST['passe1']) > 20) {
        $errors['passe'] = 'Le mot de passe doit contenir entre 4 et 20 caractères.';
    }
    if ($_POST['passe1'] !== $_POST['passe2']) {
        $errors['passe_not_equals'] = 'Les mots de passe ne correspondent pas.';
    }

    // verify lastname and firstname
    if (hl_contains_html_code($_POST['nomprenom'])) {
        hl_expulse_hackers();
    }
    if (!preg_match ('/^[a-zA-Z\s]+$/', $_POST['nomprenom']) || strlen($_POST['nomprenom']) > 60) {
        $errors['nomprenom'] = 'Le nom et prénom ne doivent contenir que des lettres et ne doivent pas dépasser 60 caractères.';
    }

    // verify email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || strlen($_POST['email']) > 80) {
        $errors['email'] = 'L\'adresse email n\'est pas valide, ou elle dépasse 80 caractères.';
    }

    // verify birthdate
    if (strlen($_POST['naissance']) !== 10) {
        $errors['naissance'] = 'La date de naissance n\'est pas valide.';
    }
    else {
        list($year, $month, $day) = explode('-', $_POST['naissance']);
        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            $errors['naissance'] = 'La date de naissance n\'est pas valide.';
        }
    }


    // Get today date for age verification and registration date
    $today = new DateTime();

    // Get birthdate for age verification and database insertion
    $birthdate = new DateTime($_POST['naissance']);


    // user age > 18 and < 120
    $interval = $today->diff($birthdate);

    if ($interval->y < 18 || $interval->y > 120) {
        $errors['age'] = 'Vous devez avoir entre 18 et 120 ans pour vous inscrire.';
    }

    if (count($errors) === 0) {
        $conn = hl_bd_connect();

        // verify that user is not already registered
        if (hl_user_exists($conn, $_POST['pseudo'])) {
            $errors['pseudo_already_used'] = 'Ce pseudo est déjà utilisé.';
        }
        
        if (count($errors) === 0) {
            // register user in database if no errors
            if (!hl_insert_user($conn, 
                $_POST['nomprenom'], 
                $_POST['pseudo'], 
                $_POST['email'], 
                password_hash($_POST['passe1'], PASSWORD_DEFAULT),
                $birthdate->format('Ymd'),
                $today->format('Ymd')
            )) {
                $errors['database'] = 'Une erreur est survenue lors de l\'enregistrement de votre compte dans la base de données.';
            }
        }
        mysqli_close($conn);
    }
    return $errors;
}

/**
 * Display registration form
 * @param array $errors The eventual errors detected
 */
function hl_aff_formulaire(array $errors = []) {
    hl_aff_debut('Inscription', 'cuiteur.css');
    hl_aff_entete(false, 'inscription');
    echo '<aside></aside>'; // empty aside

    if (count($errors) > 0) {
        echo '<div class="form-errors">',
             '<p>Les erreurs suivantes ont été détectées :</p>',
             '<ul>';
        foreach ($errors as $error) {
            echo '<li>- ', $error, '</li>';
        }
        echo '</ul>',
             '</div>',
             '<br />';
        hl_aff_form_registration($_POST);
    }
    else {
        hl_aff_form_registration();
    }

    hl_aff_pied();
    hl_aff_fin();
}

// form not submitted
if (!isset($_POST['btnSInscrire'])) {
    hl_aff_formulaire();
}
else {
    // form submitted
    $errors = hl_traitement_inscription();

    if (count($errors) > 0) {
        hl_aff_formulaire($errors);
    }
    else {
        header('Location: protegee.php');
    }
}