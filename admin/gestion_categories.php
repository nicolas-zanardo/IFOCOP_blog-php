<?php

require_once '../includes/init.php';

if(!isAdmin()){
    header('location:'.URL. 'connexion.php');
    exit();
}

$title= 'Gestion';
require_once '../includes/header.php';
require_once '../includes/footer.php';
