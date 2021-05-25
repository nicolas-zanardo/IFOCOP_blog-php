<?php

require_once 'includes/init.php';

$categories = sql("SELECT * FROM categories ORDER BY nom");

/*
 * Requete articles
 */
$requete = "SELECT a.*, c.nom as categorie, date_format(a.date_article, '%d/%m/%Y a %H:%i:%s') as date_articleFR, count(com.id_article) as nbcomments 
FROM articles a 
    LEFT JOIN categories c ON c.id_categorie = a.id_categorie
    LEFT JOIN comments com ON com.id_article = a.id_article";

$params = array();

// Tient compte de'un éentuel filter sur la catégorie
if(isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $requete .=" WHERE a.id_categorie =:id";
    $params['id'] = $_GET['cat'];
}
$requete .= " GROUP BY a.id_article ORDER BY a.date_article DESC";

$articles = sql($requete,$params);


$title= 'Accueil';

require_once 'includes/header.php';
?>

<div class="row" id="articles">
    <div class="col-md-9 order-1 order-md-0">
        <?php if($articles->rowCount()>0) : ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
                <?php while($article = $articles->fetch()) : ?>
                    <div class="col mb-3 px-2">
                        <figure class="border border-dark rounded bg-light position-relative">
                            <a href="<?php echo URL ?>article.php?id=<?php echo $article['id_article'] ?>" class="d-block illustration w-100" style="background-image: url('<?php echo URL ?>images/articles/<?php  echo $article['image'] ?>');"></a>
                            <figcaption class="p-4 d-flex">
                                <div class="w-100">
                                    <h5><a href="<?php echo URL ?>article.php?id=<?php echo $article['id_article'] ?>" class="align-self-end mt-3 text-decoration-none"><?php echo $article['title'] ?></a></h5>
                                        <small class="float-end"><i class="far fa-comments"></i><?php echo $article['nbcomments'] ?></small>
                                    <?php if($article['title']) : ?>
                                        <small class="b-block w-100 fst-italic border-bottom border-dark pb-2"><?php echo $article['date_articleFR'] ?></small>
                                        <p class="mt-3">
                                            <?php
                                                $extrait = substr($article['content'], 0, 80);
                                                echo (iconv_strlen($article['content']) > 80 ) ? substr ($extrait,0 , strrpos($extrait, ' ')) . '&hellip;' : $extrait;
                                            ?>
                                        </p>
                                    <?php endif ?>
                                </div>
                            </figcaption>
                            <a href="<?php echo URL ?>article.php?id=<?php echo $article['id_article'] ?>" class="align-self-end mt-3 btn btn-secondary btn-sm position-absolute">lire l'article</a>
                        </figure>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info">Pas d'article dans cette catégories</div>
        <?php endif; ?>
    </div>
    <aside class="col-md-3 order-0 order-md-1 border-start border-dark">
        <div class="list-group mb-2">
            <a href="<?php echo URL ?>" class="list-group-item list-group-item-action <?php if(!isset($_GET['cat'])) echo "active" ?>">Tout voir</a>
            <?php if($categories->rowCount() > 0 ) :
                while($categorie = $categories->fetch()) :
                    ?>
                    <a href="?cat=<?php echo $categorie['id_categorie'] ?>#articles" class="list-group-item list-group-item-action <?php if(isset($_GET['cat']) && $_GET['cat'] == $categorie['id_categorie']) echo 'active' ?>"><?php echo $categorie['nom'] ?></a>
            <?php endwhile;
            endif; ?>
        </div>
    </aside>
</div>

<?php
require_once 'includes/footer.php';