<?php

use http\Header;

require_once 'includes/init.php';

//Si je suis connecté et que ke tente de rentrer l'URL de la page d'inscription, je suis redirigé
// vers ma page profil
if (isConnected()) {
    // avant la function header, aucun echo, aucune balise html
    header('Location:' . URL . 'profil.php');
    // Stoppe le script php
    exit();
}

if (!empty($_POST)) {
    $errors = 0;

    if (empty($_POST['login'])) {
        $errors++;
        add_flash('Merci de choisir un login', 'danger');
    } else {
        $user = getUserByLogin($_POST['login']);
        if ($user) {
            $errors++;
            add_flash('le login choisi est indisponible. Merci d\'en choisir un autre', 'warning');
        }
    }

    if (empty($_POST['password'])) {
        $errors++;
        add_flash('Merci de saisir un mot de passe', 'danger');
    } else {
        $pattern = '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\S]{8,20}$#';
        if (!preg_match($pattern, $_POST['password'])) {
            $errors++;
            add_flash('Le mot de passse doit être composé de 8 a 20 caratères comprenant au moins une minuscule, une majuscule et un chiffre', 'danger');
        }
    }

    if (empty($_POST['confirmPassword'])) {
        $errors++;
        add_flash('Merci de confirmer votre mot de passe', 'danger');
    } else {
        if (!empty($_POST['password']) && $_POST['confirmPassword'] !== $_POST['password']) {
            $errors++;
            add_flash('La confirmation ne concorde pas avec le mot de passe', 'danger');
        }
    }

    if (empty($_POST['email'])) {
        $errors++;
        add_flash('Merci de saisir votre adresse email', 'danger');
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors++;
            add_flash('Adresse mail invalide', 'danger');
        }
    }

    if ($errors === 0) {
        sql("INSERT INTO users VALUES (NULL, :login, :password, :email, 0)", array(
            'login' => $_POST['login'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'email' => $_POST['email']
        ));
        add_flash('Inscription réussie, vous pouvez vous connecter', 'success');
            header('Location:' .URL. 'connexion.php');
        exit();
    }


}

$title = 'Inscription';
require_once 'includes/header.php';
?>

    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-4 border border-dark padding p-5 rounded">
            <h1>Inscription</h1>
            <hr class="mb-3">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" id="login" name="login" class="form-control"
                           value="<?php echo $_POST['login'] ?? '' ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirmez le mot de passe</label>
                    <input type="password" id="confirmPassword" name="confirmPassword"
                           class="form-control">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">email</label>
                    <input type="text" id="email" name="email" class="form-control"
                           value="<?php echo $_POST['email'] ?? '' ?>">
                </div>
                <div class="mb-3 text-end">
                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row col text-center mt-4">
        <P>Déjà inscrit ? Vous pouvez vous connecter en <a href="<?php echo URL ?>connexion.php">cliquant
                ici</a></P>
    </div>
<?php
require_once 'includes/footer.php';
