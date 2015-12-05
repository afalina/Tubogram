<?
require 'init.php';

$username = '';
$password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username == '' || $password == '') {
        $errors[]= "Поля не можуть бути порожніми.";
    }
    if (strlen($username) < 3 || strlen($username) > 20) {
        $errors[]= "Ім'я має бути від 3 до 20 символів.";
    }
    if (!ctype_alnum($username)) {
        $errors[]= "Ім'я може містити тількі латинські літери і цифри.";
    }

    if (get_user_id_by_username($username)) {
        $errors[]= "Таке ім'я вже існує.";
    }
    if (strlen($password) < 3 || strlen($password) > 255) {
        $errors[]= "Пароль має бути від 3 до 255 символів";
    }

    if (!$errors) {
        $user_id = create_user($username, $password);
        login_user($user_id);
        redirect(APP_URL);
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<? page_header('registration', 'registration'); ?>
<div class="container">
    <div class="page-header">
        <h1>Реєстрація</h1>
    </div>
    <? if ($errors) display_errors($errors) ?>
    <div class="well">
        <form method="post" action="<? echo APP_URL ?>/registration.php">
            <div class="form-group">
                <label>Ваше ім'я</label>
                <input class="form-control" type="text" name="username" value="<? echo escape_html($username) ?>">
            </div>
            <div class="form-group">
                <label>Ваш пароль</label>
                <input class="form-control" type="password" name="password" value="<? echo escape_html($password) ?>">
            </div>
            <input class="btn btn-primary btn-lg" type="submit" value="Зареєструватися!">
        </form>
    </div>
</div>

<? page_footer() ?>
