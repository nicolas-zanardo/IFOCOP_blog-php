<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo (defined('BLOGTITLE')) ? BLOGTITLE : 'ANONYMOUS' ?> | <?php echo $title ?? '' ?> </title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- css principal -->
    <link rel="stylesheet" href="<?php echo URL ?>css/style.css">


</head>

<body>
<header>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
        <div class="container-fluid">

            <a class="navbar-brand" href="<?php echo URL ?>"><?php echo (defined('BLOGTITLE')) ? BLOGTITLE : 'ANONYMOUS' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">

                    <li class="nav-item">
                        <a class="nav-link <?php if ($title == "Accueil") echo 'active'; ?>" aria-current="page" href="<?php echo URL ?>"><i class="fas fa-home"></i> Blog</a>
                    </li>

                    <?php if (!isConnected()) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($title == "Inscription") echo 'active'; ?>" href="<?php echo URL ?>inscription.php">Inscription</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php if ($title == "Connexion") echo 'active'; ?>" href="<?php echo URL ?>connexion.php">Connexion</a>
                        </li>
                    <?php endif; ?>

                    <?php if (isAdmin()) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="sousmenu" role="button" data-bs-toggle="dropdown">Back Office</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?php echo URL ?>admin/gestion_blog.php">Gestion du blog</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo URL ?>admin/gestion_categories.php">Gestion des catégories</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo URL ?>admin/gestion_articles.php">Gestion des articles</a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (isConnected()) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($title == "Profil") echo 'active'; ?>" href="<?php echo URL ?>profil.php">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URL ?>connexion.php?action=logout"><i class="fas fa-power-off"></i></a>
                        </li>
                    <?php endif; ?>

                </ul>
                <form class="d-flex" method="post" action="<?php echo URL ?>recherche.php">
                    <input class="form-control me-2" type="search" placeholder="mot à rechercher" aria-label="Search" name="critere" value="<?php echo $_POST['critere'] ?? '' ?>">
                    <button class="btn btn-outline-info" type="submit">Rechercher</button>
                </form>
            </div>
        </div>
    </nav>

    <?php if($title == "Accueil") :?>
        <div class="container-fluid">
            <div class="row">
                <div class="col header-img gx-0" style="background-image: url(<?php echo (defined
                ('BLOGHEADER')) ? URL . 'images/site/' . BLOGHEADER : URL . 'images/site/default-header.jpg' ?>);">
                    <h1 class="display-1 text-light w-100 h-50 d-flex justify-content-center
                    align-items-end mb-0">
                        <?php echo (defined('BLOGTITLE')) ? BLOGTITLE: 'ANONYMOUS' ?>
                    </h1>
                    <h2 class="display-6 text-light w-100 h-50 d-flex justify-content-center align-items-start">
                        <?php echo (defined('BLOGSLOGAN')) ? BLOGSLOGAN : 'Le blog de l\'extreme' ?>
                    </h2>
                </div>
            </div>
        </div>
    <?php endif; ?>

</header>
<main class="container my-5">

    <?php if (!empty(show_flash())) : ?>
    <div class="row justify-content-center">
        <div class="col">
            <?php echo show_flash('reset'); ?>
        </div>
    </div>
<?php endif; ?>