<? require 'init.php' ?>

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
    <div class="jumbotron page-not-found">
        <h1>404</h1>
        <p>
            Такої сторінки не існує :(
        </p>
    </div>
</div>

<? page_footer() ?>