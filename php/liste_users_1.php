<?php

/** 
 * Humbert Logan - 03/25/2022
 * 
 * Script to list all users
 */

include_once './bibli_generale.php';
include_once './bibli_cuiteur.php';

hl_aff_debut('Liste des utilisateurs');

echo '<h1>Liste des utilisateurs</h1>';
$conn = hl_bd_connect();
$users = hl_bd_get_users($conn);

while($user = mysqli_fetch_assoc($users)) {
   // neutralize the eventual HTML code in the fields
   $user['usID'] = htmlspecialchars($user['usID']);
   $user['usPseudo'] = htmlspecialchars($user['usPseudo']);
   $user['usNom'] = htmlspecialchars($user['usNom']);
   $user['usDateInscription'] = htmlspecialchars($user['usDateInscription']);
   $user['usVille'] = htmlspecialchars($user['usVille']);
   $user['usWeb'] = htmlspecialchars($user['usWeb']);
   $user['usMail'] = htmlspecialchars($user['usMail']);
   $user['usDateNaissance'] = htmlspecialchars($user['usDateNaissance']);
   $user['usBio'] = htmlspecialchars($user['usBio']);

   echo '<h2>Utilisateur ', $user['usID'], '</h2>',
      '<ul>',
         '<li>Pseudo : ', $user['usPseudo'], '</li>',
         '<li>Nom : ', $user['usNom'], '</li>',
         '<li>Inscription : ', $user['usDateInscription'], '</li>',
         '<li>Ville : ', $user['usVille'], '</li>',
         '<li>Web : ', $user['usWeb'], '</li>',
         '<li>Mail : ', $user['usMail'], '</li>',
         '<li>Naissance : ', $user['usDateNaissance'], '</li>',
         '<li>Bio : ', $user['usBio'], '</li>',
      '</ul>';
}

mysqli_free_result($users);
mysqli_close($conn);

hl_aff_fin();