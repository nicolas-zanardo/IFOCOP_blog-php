<?php

require_once '../includes/init.php';

$title= '404';
require_once '../includes/header.php';
?>

    <div class="row">
        <div class="col mt-5 text-center">
            <img src="<?php echo URL ?>images/site/404.jpg" alt="404" class="img-fluid w-50 my-3">
            <p>Le contenu que vous essayez d'atteindre n'existe pas ou été supprimé</p>
            <p>
                <a href="<?php echo URL ?>" class="btn btn-primary">Revenir à la page d'accueil</a>
            </p>
        </div>
    </div>

<?php
require_once '../includes/footer.php';


