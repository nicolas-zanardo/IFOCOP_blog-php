<?php

require_once '../includes/init.php';

if (!isAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}


if(!empty($_POST)) {
    echo '<pre class="mt-5"';
    var_dump($_POST);
    var_dump($_FILES);
    echo '</pre>';
    // si chap vides, on met les valuer par défault
}


$title = 'Gestion du blog';
require_once '../includes/header.php';

?>
    <div class="row justify-content-center">
        <div class="col">
            <h1>Info sur le blog</h1>
            <hr class="my-3">
            <!--        enctype="multipart/form-data" imperatif pour alimenter les fichier joinrs-->
            <form method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="title" class="form-label">Titre du blog</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo (defined
                    ('BLOG_TITLE')) ? BLOGTITLE : 'ANONYMOUS' ?>">
                </div>

                <div class="mb-3">
                    <label for="slogan" class="form-label">Slogan du blog</label>
                    <input type="text" name="slogan" id="slogan" class="form-control" value="<?php echo (defined
                    ('BLOGSLOGAN')) ? BLOGSLOGAN : 'Le blog de l\'extreme' ?>">
                </div>

                <div class="mb-3">
                    <label for="header" class="form-label">Image header</label>
                    <input type="file" name="header" id="header" class="form-control" accept="image/jpeg,
                            image/png">
                    <label>Images actuelle</label>
                    <div class="preview">
                        <img src="<?php echo (defined('BLOGHEADER')) ? URL. 'images/site/'. BLOGHEADER
                            : URL . 'images/site/default-header.jpg'; ?>" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Mettre à  jours</button>
                </div>

            </form>
        </div>
    </div>

<?php
require_once '../includes/footer.php';
