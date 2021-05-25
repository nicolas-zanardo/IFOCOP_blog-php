<?php

require_once 'includes/init.php';

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article = sql("SELECT a.*, c.nom FROM articles a
    INNER JOIN categories c ON c.id_categorie = a.id_categorie
    WHERE a.id_article=:id", array(
        'id' => $_GET['id']
    ));
    if($article->rowCount()>0) {
        $infos = $article->fetch();
        $comments = sql("SELECT c.*, u.login FROM comments c 
        LEFT JOIN users u  ON u.id_user = c.id_user
        WHERE id_article=:id", array(
            'id' => $_GET['id']
        ));
    } else {
        /**
         * add_flash('Article introuvable', 'warning');
         * header('location:'.URL);
         * exit();
         * */
        header('location:'.URL.'error/404.php');
    }
} else {
    header('location:' .URL);
    exit();
}

$title= 'Article';
require_once 'includes/header.php';
?>
<div class="row">
    <div class="col">
        <h1 class="text-center"><?php echo $infos['title'] ?></h1>
        <img src="<?php echo URL ?>images/articles/<?php echo $infos['image'] ?>" alt="<?php echo $infos['title'] ?>" class="img-fluid d-block mx-auto">
        <p class="py-3">
            <?php echo str_replace(PHP_EOL, '<br>', $infos['content']) ?>
        </p>
        <hr class="mb-3">
        <?php if ($comments->rowCount() > 0) : ?>
            <?php while($comment = $comments->fetch()) : ?>
                <p class="bg-light p-3 mb-3">
                    <h5>commentaire laissé par  <?php echo ($comments['login']) ? $comments['login'] : 'anonyme' ?> le <?php echo date('d/m/Y', strtotime($comment['date_comment'])) ?></h5>
                    <hr>
                    <?php echo $comment['comment'] ?>
                </p>
            <?php endwhile ?>
        <?php else: ?>
            <div class="alert alert-info">
                il n'y a pas encore de commentaire. Soyez le premier.
            </div>
        <?php endif ?>
    </div>
</div>


<?php
require_once 'includes/footer.php';