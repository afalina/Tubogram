<?
require 'init.php';
$username = $_GET['search'];
$user_id = get_user_id_by_username($username);

if ($user_id) {
    redirect(APP_URL . '/user.php?id=' . $user_id);
}
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<?
if (get_current_user_id()) {
    page_header('logged');
} else {
    page_header('not_logged');
}
?>
<div class="container">
    <div class="jumbotron not-found">
        <h1><? echo escape_html($username) ?></h1>
        <p>
            Такого користувача не знайдено :(
        </p>
    </div>
</div>

<? page_footer() ?>

