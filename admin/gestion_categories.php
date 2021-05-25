<?php

require_once '../includes/init.php';

if (!isAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}

if (!empty($_POST)) {

    // formulaire d'ajout soumis
    if (isset($_POST['add'])) {
        if (!empty(trim($_POST['categories']))) {
            sql("INSERT INTO categories VALUES(NULL, :nom)", array(
                'nom' => $_POST['categories']
            ));
            add_flash('la catégorie ' . $_POST['categories'] . ' a été ajoutée', 'success');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('La catégorie ne doit pas être vide', 'danger');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        }

    }

    //formulaire d'update soumis
    if (isset($_POST['update'])) {
        if (!empty(trim($_POST['categorie']))) {
            sql("UPDATE categories SET nom=:nouveaunom WHERE id_categorie=:id_categorie", array(
                'nouveaunom' => $_POST['categorie'],
                'id_categorie' => $_POST['id_categorie']
            ));
            add_flash('La catégorie a bien été modifié', 'success');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('La catégorie ne doit pas être vide', 'danger');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

//formulaire delete
if(isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id']) && is_numeric
    ($_GET['id'])) {
    sql("DELETE FROM categories WHERE id_categorie=:id", array(
        'id' => $_GET['id']
    ));
        header('location:' . $_SERVER['PHP_SELF']);
        exit();
}

$categories = sql("SELECT * FROM categories ORDER BY nom");
$title = 'Gestion des catégorie';
require_once '../includes/header.php';
?>

    <div class="row justify-content-center">
        <div class="col">
            <h1>Catégories d'article</h1>
            <hr class="my-3">
            <form action="" method="post" class="row">
                <div class="col-4">
                    <input type="text" id="categorie" name="categories" class="form-control"
                           placeholder="catégorie à ajouter">
                </div>
                <div class="col-4">
                    <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
            <?php if ($categories->rowCount() > 0) : ?>
                <h2>liste des catégories</h2>
                <?php while ($category = $categories->fetch()) : ?>
                    <form action="" method="post" class="row mb-3">
                        <input type="hidden" name="id_categorie"
                               value="<?= $category['id_categorie'] ?>"/>
                        <div class="col-7 col-lg-4">
                            <input type="text" id="categorie" name="categorie" class="form-control"
                                   value="<?=
                            $category['nom'] ?>">
                        </div>
                        <div class="col-4">
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fa fa-edit"></i>
                            </button>
                            <a href="?action=delete&id=<?= $category['id_categorie'] ?>"
                               class="btn btn-danger confirm">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </form>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="mt-4 alert alert-info">Il n'y a pas encore de catégorie</div>
            <?php endif; ?>
        </div>
    </div>

<?php
require_once '../includes/footer.php';
