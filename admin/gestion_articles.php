<?php
require_once '../includes/init.php';

if (!isAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}

if (!empty($_POST)) {
    $errors = 0;
    if (empty($_POST['titre'])) {
        $errors++;
        add_flash("Le titre ne peut pas être vide", "danger");
    }
    if (empty($_POST['contenu'])) {
        $errors++;
        add_flash("Le contenu ne peut pas être vide", "danger");
    }

    if (empty($_POST['id_categorie'])) {
        $errors++;
        add_flash("La categorie ne peut pas être vide", "danger");
    }


    if ($errors == 0) {
        // insertion en base
        sql("INSERT INTO articles VALUES (NULL, :id_categorie, NOW(), :titre, :contenu, NULL)", array(
            'titre' => $_POST['titre'],
            'id_categorie' => $_POST['id_categorie'],
            'contenu' => $_POST['contenu']
        ));
        global $pdo;
        $id_article = $pdo->lastInsertId();

        $ext_auto = array('image/jpeg', 'image/png');

        if (!empty($_FILES['image']['name']) && in_array($_FILES['image']['type'], $ext_auto)) {
            $nomfichier = $id_article . ' - ' . $_FILES['image']['name'];
            $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'images/articles/';
            move_uploaded_file($_FILES['image']['tmp_name'], $chemin . $nomfichier);

            sql("UPDATE articles SET image=:image WHERE id_article=:id_article", array(
                'image' => $nomfichier,
                'id_article' => $id_article
            ));
        }
        header('location:' . $_SERVER['PHP_SELF']);
        exit();
    }
}

$categories = sql("SELECT * FROM categories ORDER BY nom");
$title = 'Gestion des articles';
require_once '../includes/header.php';
?>


    <div class="row justify-content-center">
        <div class="col">
            <h1>Articles</h1>
            <hr class="my-3">
            <div class="row">
                <div class="col-xl-2">
                    <div class="list-group" id="list-tab" role="tablist">

                        <a class="list-group-item list-group-item-action <?php if (empty($_POST)) echo 'active' ?>"
                           id="list-articles" data-bs-toggle="list" href="#show-articles" role="tab">Liste
                            des articles</a>

                        <a class="list-group-item list-group-item-action <?php if (!empty($_POST)) echo 'active' ?>"
                           id="ajout" data-bs-toggle="list" href="#add-article" role="tab">Nouvel
                            article...</a>
                    </div>

                </div>
                <div class="col-lg-10">
                    <div class="tab-content" id="nav-tabcontent">
                        <div class="tab-pane fade <?php if (empty($_POST)) echo 'show active' ?>"
                             id="show-articles" role="tabpanel">
                            Panel 1
                        </div>
                        <div class="tab-pane fade <?php if (!empty($_POST)) echo 'show active' ?>"
                             id="add-article" role="tabpanel">

                            <h2>Ajout d'un article</h2>
                            <form method="post" enctype="multipart/form-data" class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre</label>
                                        <input type="text" id="titre" name="titre" class="form-control"
                                               value="<?php echo $_POST['titre'] ?? '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contenu" class="form-label">Contenu</label>
                                        <textarea id="contenu" name="contenu" class="form-control"
                                                  rows="12"><?php echo $_POST['contenu'] ?? '' ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label for="categorie" class="form-label">Catégorie</label>
                                        <select id="categorie" name="id_categorie" class="form-select">
                                            <option disabled <?php if (empty($_POST['id_categorie'])) echo 'selected' ?>>
                                                Choisir une catégorie
                                            </option>
                                            <?php if ($categories->rowCount() > 0) : ?>

                                                <?php while ($categorie = $categories->fetch()) : ?>

                                                    <option value="<?php echo $categorie['id_categorie'] ?>" <?php
                                                    if (!empty($_POST['id_categorie']) && $_POST['id_categorie'] == $categorie['id_categorie']) echo 'selected';
                                                    ?>>
                                                        <?php echo $categorie['nom'] ?>
                                                    </option>

                                                <?php endwhile; ?>

                                            <?php else : ?>
                                                <option disabled selected>Pas de catégorie</option>
                                            <?php endif ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Image</label>
                                        <input type="file" id="header" name="image" class="form-control"
                                               accept="image/jpeg,image/png">
                                        <div id="preview" class="mt-3">
                                            <img src="https://via.placeholder.com/1920x1080"
                                                 alt="preview" class="img-fluid">
                                        </div>
                                    </div>


                                </div>

                                <div class="col-12">
                                    <button type="submit" class="mt-3 btn btn-primary">Ajouter</button>
                                </div>


                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php

require_once '../includes/footer.php';
