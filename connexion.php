<?php


require_once 'includes/init.php';

if(isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['user']);
    add_flash('Vous vous êtes déconnecté', 'success');
    header('location:'. URL);
    exit();
}

if(isConnected()) {
    header('Location:'.URL. 'profil.php');
    exit();
}

if(!empty($_POST)) {
    $errors = 0;

    if(empty($_POST['login'])) {
        $errors++;
        add_flash('Merci de saisir login', 'danger');
    }

    if(empty($_POST['password'])) {
        $errors++;
        add_flash('Merci de saisir votre mot de passe', 'danger');
    }

    if($errors == 0) {
        $user = getUserByLogin($_POST['login']);
        if($user) {
            if(password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                add_flash('Connexion réussie', 'success');
                header('location:'.URL.'profil.php');
                exit();
            } else {
                add_flash('Erreur sur les identifiants', 'danger');
            }
        } else {
            add_flash('Erreur sur les identifiants', 'danger');
        }
    }
}

$title= 'Connexion';
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
                <div class="mb-3 text-end">
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row col text-center mt-4">
        <P>Déjà encore de compte ? Vous pouvez créer un compte <a href="<?php echo URL ?>inscription.php">cliquant ici</a></P>
    </div>

<?php
require_once 'includes/footer.php';
