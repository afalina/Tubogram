<?
require 'init.php';

$username = '';
$password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (check_user_password($username, $password)) {
        login_user(get_user_id_by_username($username));
        redirect(APP_URL);
    } else {
        $errors[]= "Не співпадає ім'я-пароль 😞";
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<? page_header('login', 'login'); ?>
<div class="container">
    <div class="page-header">
        <h1>Вхід</h1>
    </div>
    <? if ($errors) display_errors($errors) ?>
    <div class="well">
        <form method="post" action="<? echo APP_URL ?>/login.php">
            <div class="form-group">
                <label>Ваше ім'я</label>
                <input class="form-control" type="text" name="username" value="<? echo escape_html($username) ?>">
            </div>
            <div class="form-group">
                <label>Ваш пароль</label>
                <input class="form-control" type="password" name="password" value="<? echo escape_html($password) ?>">
            </div>
            <input class="btn btn-primary btn-lg" type="submit" value="Увійти!">
        </form>
    </div>
</div>

<? page_footer() ?>