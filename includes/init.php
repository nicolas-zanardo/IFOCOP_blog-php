<?php

//fuseau horaire
date_default_timezone_set('Europe/Paris');

// Nom et ouverture de sessions
session_name('MYBLOG'); // default : PHPSESSID
session_start();

// Connexion BDD
$pdo = new PDO(
    'mysql:host=localhost;charset=utf8;dbname=blog_ifocop',
    'admin',
    'admin',
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )
);

// Inclusion des fonction du site
require_once('functions.php');

// Constante du site
define('URL', '/');

// Récupération du titre du blog, son slogan et son image d'entrée
$recup_infos = sql('SELECT * FROM bloginfo');

if($recup_infos->rowCount() > 0) {
    while($bloginfo = $recup_infos->fetch()) {

        switch ($bloginfo['nom']) {
            case 'title': define('BLOGTITLE', $bloginfo['valeur']); break;
            case 'slogan': define('BLOGSLOGAN', $bloginfo['valeur']); break;
            case 'header': define('BLOGHEADER', $bloginfo['valeur']); break;
        }

    }
}

