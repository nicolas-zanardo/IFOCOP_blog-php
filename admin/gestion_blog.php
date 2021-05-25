<?php
// Pour changer les valeur php.init
ini_set('upload_max_filesize', '20M');
require_once '../includes/init.php';

if (!isAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}


if(isset($_GET['action']) && $_GET['action']  == 'supimg') {
    $header_existant = sql("SELECT * FROM bloginfo WHERE nom=:nom", array(
        'nom' => 'header'
    ));

    $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'images/sites/';

    $nom_fichier_to_delete = $header_existant->fetch()['valeur'];
    if (file_exists($chemin . $nom_fichier_to_delete)) {
        // supression de l'ancien fichier
        unlink($chemin . $nom_fichier_to_delete);
    }

    sql("DELETE FROM bloginfo WHERE nom=:nom", array(
            'nom' => 'header'
    ));

    add_flash('Image par default rétablie', 'info');
    header('location:' . $_SERVER['PHP_SELF']);
    exit();
    }

if (!empty($_POST)) {
    echo '<pre class="mt-5"';
    var_dump($_POST);
    var_dump($_FILES);
    echo '</pre>';
    // si chap vides, on met les valuer par défault

    if (empty(trim($_POST['title']))) {
        $_POST['title'] = 'ANONYMOUS';
        sql('DELETE FROM bloginfo WHERE nom=:nom', array(
            'nom' => 'title'
        ));
        add_flash('La valeur par default du titre a été rétablie', 'info');
        // [PHP_SELF] fichier.php?action=delete => fichier.php
        // [REQUEST_URI] fichier.php?action=delete => fichier.php?action=delete
    } else {
        $titre_existant = sql("SELECT * FROM bloginfo WHERE nom=:nom", array(
            'nom' => 'title'
        ));
        if ($titre_existant->rowCount() > 0) {
            sql("UPDATE bloginfo SET valeur=:nouveautitre WHERE nom=:nom", array(
                'nouveautitre' => $_POST['title'],
                'nom' => 'title'
            ));
        } else {
            sql("INSERT INTO bloginfo (nom, valeur) VALUES (:nom, :nouveautitre)", array(
                'nouveautitre' => $_POST['title'],
                'nom' => 'title'
            ));
        }

        add_flash('Les titre a été mis à jours', 'success');
    }

    if (empty(trim($_POST['slogan']))) {
        $_POST['slogan'] = 'Le blog de l\'extreme';
        sql('DELETE FROM bloginfo WHERE nom=:nom', array(
            'nom' => 'slogan'
        ));
        add_flash('La valeur par default du slogan a été rétablie', 'info');
    } else {
        $titre_existant = sql("SELECT * FROM bloginfo WHERE nom=:nom", array(
            'nom' => 'slogan'
        ));
        if ($titre_existant->rowCount() > 0) {
            sql("UPDATE bloginfo SET valeur=:nouveauslogan WHERE nom=:nom", array(
                'nouveauslogan' => $_POST['slogan'],
                'nom' => 'slogan'
            ));
        } else {
            sql("INSERT INTO bloginfo (nom, valeur) VALUES (:nom, :nouveauslogan)", array(
                'nouveauslogan' => $_POST['slogan'],
                'nom' => 'slogan'
            ));
        }
        add_flash('Les slogan a été mis à jours', 'success');
    }


    /**
     * IMAGE
     * ------
     * name
     * type
     * tmp_name
     * error
     * size
     */
    if (!empty($_FILES['header']['name']) && $_FILES['header']['error'] == 0) {

        $ext_autorise = array('image/jpeg', 'image/png');

        if ($_FILES['header']['size'] > 2e6) {
            add_flash('image trop grande', 'danger');
        } elseif (!in_array($_FILES['header']['type'], $ext_autorise)) {
            add_flash('Seules les images JPEG et PNG sont autorisé', 'danger');
        } else {
            // copie physique du fichier
            $nom_fichier = 'custom-header.' . pathinfo($_FILES['header']['name'], PATHINFO_EXTENSION);
            // Chemin réel du fichier
            $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'images/site/';
            move_uploaded_file($_FILES['header']['tmp_name'], $chemin . $nom_fichier);
            // copy();

            // insertion en bdd
            $header_existant = sql("SELECT * FROM bloginfo WHERE nom=:nom", array(
                'nom' => 'header'
            ));
            if ($header_existant->rowCount() == 0) {
                sql("INSERT INTO bloginfo (nom,valeur) VALUES (:nom,:valeur)", array(
                    'nom' => 'header',
                    'valeur' => $nom_fichier
                ));
            } else {
                $nom_fichier_to_delete = $header_existant->fetch()['valeur'];
                if ($nom_fichier_to_delete != $nom_fichier && file_exists($chemin . $nom_fichier_to_delete)) {
                    // supression de l'ancien fichier
                    unlink($chemin . $nom_fichier_to_delete);
                }
                sql("UPDATE bloginfo SET valeur=:valeur  WHERE nom=:nom;", array(
                    'nom' => 'header',
                    'valeur' => $nom_fichier
                ));
            }
            add_flash('L\'image a été mis à jours', 'success');
        }

    }

    header('location:' . $_SERVER['PHP_SELF']);
    exit();
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
                    <input type="text" name="title" id="title" class="form-control"
                           value="<?php echo (defined
                           ('BLOGTITLE')) ? BLOGTITLE : 'ANONYMOUS' ?>">
                </div>

                <div class="mb-3">
                    <label for="slogan" class="form-label">Slogan du blog</label>
                    <input type="text" name="slogan" id="slogan" class="form-control"
                           value="<?php echo (defined
                           ('BLOGSLOGAN')) ? BLOGSLOGAN : 'Le blog de l\'extreme' ?>">
                </div>

                <div class="mb-3">
                    <label for="header" class="form-label">Image d'entête de la page d'accueil</label>
                    <input type="file" id="header" name="header" class="form-control"
                           accept="image/jpeg,image/png">
                    <label>Image actuelle</label>
                    <div id="preview" class="position-relative">
                        <img src="<?php echo (defined('BLOGHEADER')) ? URL . 'images/site/' . BLOGHEADER : URL . 'images/site/default-header.jpg'; ?>"
                             class="img-fluid w-50">
                        <?php if (defined('BLOGHEADER')) : ?>
                            <a href="?action=supimg" class="px-2"><i class="fa fa-times"></i></a>
                        <?php endif ?>
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Mettre à jours</button>
                </div>

            </form>
        </div>
    </div>

<?php
require_once '../includes/footer.php';