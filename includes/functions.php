<?php

function sql(string $request, array $params = array()): PDOStatement {
    global $pdo;
    $statement = $pdo->prepare($request);

    if(!empty($params)) {
        foreach($params as $key => $value) {
            $statement->bindValue($key, htmlspecialchars($value), PDO::PARAM_STR);
        }
    }
    $statement->execute();

    return $statement;
}

//function utilisateur
function isConnected(): bool
{
    return isset($_SESSION['user']); //indicateur d'une connection
}
function isAdmin(): bool
{
    return (isConnected() && ($_SESSION['user']['droits'] == 1));
}

function getUserByLogin(string $login) {
    $request = sql("SELECT * FROM users WHERE login=:login", array(
        'login' => $login
    ));
    if($request->rowCount() > 0 ) {
        return $request->fetch();
    } else {
        return false;
    }
}

// Function message Flash
/**
 * @description add messages
 * @param string $message
 * @param string $class
 */
function add_flash(string $message, string $class) {
    if(!isset($_SESSION['messages'][$class])) {
        $_SESSION['messages'][$class] = array();
    }
    $_SESSION['messages'][$class][] = $message;
}

/**
 * @description show message
 * @param null $option
 * @return string
 */
function show_flash($option=null): string
{
    $messages = '';
    if(isset($_SESSION['messages'])) {
        foreach(array_keys($_SESSION['messages']) as $keyName) {
            $messages .= '<div class="alert alert-' . $keyName . '">' . implode('<br>', $_SESSION['messages'][$keyName]) . '</div>';
        }
    }

    if($option == 'reset') {
        // Je détruit les message pour les afficher 1 fois
        unset($_SESSION['messages']);
    }

    return $messages;
}
