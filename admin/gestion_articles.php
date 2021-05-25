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

    // IMAGE
    if(empty($_FILES['image']['name']) && empty($_POST['nom_original'])) {
        $errors++;
        add_flash("Merci de choisir une image pour l'article", "danger");
    }
    $ext_auto = ['image/jpeg', 'image/png'];
    if(!empty($_FILES['image']['name']) && !in_array($_FILES['image']['type'], $ext_auto)) {
        $errors++;
        add_flash('Format autorisé: JPEG ou PNG', 'danger');
    }


    if ($errors == 0) {
        // Mode edition
        if( isset($_GET['action']) && $_GET['action'] == 'edit') {
            //update
            sql("UPDATE articles SET id_categorie=:id_categorie, title=:titre, content=:contenu WHERE id_article=:id", array(
                    'id_categorie' => $_POST['id_categorie'],
                    'titre' => $_POST['titre'],
                    'contenu' => $_POST['contenu'],
                    'id' => $_GET['id'],
            ));
            $id_article = $_GET['id'];
            add_flash("l'article $_POST[titre] a bien été mise à jours", 'success');
        } else {
            // insertion en base
            sql("INSERT INTO articles VALUES (NULL, :id_categorie, NOW(), :titre, :contenu, NULL)", array(
                'titre' => $_POST['titre'],
                'id_categorie' => $_POST['id_categorie'],
                'contenu' => $_POST['contenu']
            ));
            global $pdo;
            $id_article = $pdo->lastInsertId();
            add_flash("l'article $_POST[titre] a bien été ajouter", 'success');
        }



        $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'images/articles/';
//        $ext_auto = array('image/jpeg', 'image/png');

        // 1er cas $_FILES est dispo
        if (!empty($_FILES['image']['name'])) {
            $nomfichier = $id_article . ' - ' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $chemin . $nomfichier);
        } elseif (!empty($_POST['data_img'])) {
            // 2eme cas, on utilise la mémoire car $_FILES est perdu
            $nomfichier = $id_article . ' _ ' . $_POST['nom_original'];
            /**
             * data:image/jpeg; base64, / 9J / ..
             */
            list(,$data) = explode(',', $_POST['data_img']);
            // écriture du fichier
            file_put_contents($chemin.$nomfichier, base64_decode($data));
        }
        if(!empty($_FILES['image']['name']) || !empty($_POST['data_img'])){
            // sppression de l'eventuelle ancienne images cas de mise à jour
            if(isset($_GET['action']) && $_GET['action'] == 'edit' && $_POST['img_actuelle'] != $nomfichier) {
                if(file_exists($chemin.$_POST['img_actuelle'])) {
                    unlink($chemin.$_POST['img_actuelle']);
                }
            }

            sql("UPDATE articles SET image=:image WHERE id_article=:id_article", array(
                'image' => $nomfichier,
                'id_article' => $id_article
            ));

        }
       header('location:' . $_SERVER['PHP_SELF']);
       exit();
    }
}

// SPRESSION d'un article
if(isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    $article = sql("SELECT * FROM articles WHERE id_article=:id", array(
            'id' => $_GET['id']
    ));
    if($article->rowCount()>0) {
        $infos = $article->fetch();
        // sppression du fichier
        $chemin = $_SERVER['DOCUMENT_ROOT'].URL.'image/articles/';
        if(file_exists($chemin.$infos['image'])) {
            unlink($chemin.$infos['image']);
        }
        $titre = $infos['title'];
        // suppression en bdd
        sql("DELETE FROM articles WHERE id_article=:id", array(
            'id' => $_GET['id']
        ));
        add_flash("L'article ". $titre . " a été supprimé", "success");
        header("location:".$_SERVER['PHP_SELF']);
        exit();
    } else {
        add_flash('Article introuvable', 'warning');
    }
}

//demande d'édition
if(isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    $article = sql("SELECT * FROM articles WHERE id_article=:id", array(
            'id' => $_GET['id']
    ));
    if($article->rowCount() > 0 ) {
        $current = $article->fetch();
        $current['img_actuelle'] = $current['image'];
        if(!isset($_POST['nom_original'])) $_POST['nom_original'] = $current['image'];
    } else {
        add_flash('article introuvalbe', 'warning');
    }
}

$categories = sql("SELECT * FROM categories ORDER BY nom");
$articles = sql("SELECT a.*, c.nom as categorie, date_format(a.date_article,'%d/%m/%Y à %H:%i:%s') as date_articleFR FROM articles a INNER JOIN categories c ON c.id_categorie = a.id_categorie ORDER BY date_article DESC");
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

                            <!-- Show Article -->
                            <?php if ($articles->rowCount() > 0)  :?>
                                <h2>liste des articles</h2>
                            <table class="table table-bordered table-hover ">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Catégorie</th>
                                    <th>Article</th>
                                    <th>Extrait</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while($article = $articles->fetch()): ?>
                                    <tr>
                                        <td><?php echo $article['id_article'] ?></td>
                                        <td><?php echo $article['date_articleFR'] ?></td>
                                        <td><?php echo $article['categorie'] ?></td>
                                        <td class="w-25"><?php echo $article['title'] ?>
                                            <img src="<?php echo URL ?>images/articles/<?php echo $article['image'] ?>" alt="<?php echo $article['title'] ?>" class="img-fluid">
                                        </td>
                                        <td class="w-25">
                                            <?php $extrait = substr($article['content'], 0 , 80);
                                                echo (iconv_strlen($article['content']) > 80) ? substr($extrait,0,strrpos($extrait, ' ')) . '&hellip;' : $extrait;
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo URL ?>article.php?id=<?php echo $article['id_article'] ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                                            <a href="?action=edit&id=<?php echo $article['id_article'] ?>" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                            <a href="?action=delete&id=<?php echo $article['id_article'] ?>" class="btn btn-danger confirm"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                            <?php else :?>
                                <div class="mt-4 alert alert-info">il n'y a pas encore d'article</div>
                            <?php endif; ?>


                        </div>
                        <div class="tab-pane fade <?php if (!empty($_POST)) echo 'show active' ?>"
                             id="add-article" role="tabpanel">

                            <h2>Ajout d'un article</h2>
                            <form method="post" enctype="multipart/form-data" class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre</label>
                                        <input type="text" id="titre" name="titre" class="form-control"
                                               value="<?php echo $_POST['titre'] ?? $current['title'] ?? '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contenu" class="form-label">Contenu</label>
                                        <textarea id="contenu" name="contenu" class="form-control"
                                                  rows="12"><?php echo $_POST['contenu'] ?? $current['content'] ?? '' ?></textarea>
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
                                                    if (
                                                        (!empty($_POST['id_categorie']) && $_POST['id_categorie'] == $categorie['id_categorie']) ||
                                                        (!empty($current['id_categorie']) && $current['id_categorie'] == $categorie['id_categorie'] )
                                                    ) echo 'selected';
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
                                        <label class="form-label">Image</label>
                                        <input type="file" id="header" name="image" class="form-control"
                                               accept="image/jpeg,image/png">
                                        <label for="header" id="preview" class="mt-3">
                                            <img src="<?php echo (!empty($_POST['data_img'])) ? $_POST['data_img'] : ( (isset($current['img_actuelle']) ) ? URL . 'images/articles/' . $current['img_actuelle'] : 'https://via.placeholder.com/1920x1080') ?>"
                                                 alt="preview" class="img-fluid">
                                        </label>
                                        <input type="hidden" name="nom_original" id="nom_original" value="<?php echo $_POST['nom_original'] ?? '' ?>">
                                        <input type="hidden" id="data_img" name="data_img" value="<?php echo $_POST['data_img'] ?? '' ?>">
                                        <!-- Mémorise le nom du fichier de l'image actuelle-->
                                        <?php if(isset($current['img_actuelle'])): ?>
                                            <input type="hidden" name="img_actuelle" value="<?php echo $current['img_actuelle'] ?>">
                                        <?php endif ?>
                                    </div>


                                </div>

                                <div class="col-12">
                                    <button type="submit" class="mt-3 btn btn-primary">
                                        <?php echo (isset($_GET['action']) && $_GET['action'] == 'edit') ? 'Mettre à jour' : "Ajouter" ?>
                                    </button>
                                    <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn btn-danger mt-3">Annuler</a>
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
