<? require 'init.php' ?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<?
if (get_current_user_id()) {
    page_header('logged', 'user_rating');
} else {
    page_header('not_logged', 'user_rating');
}
?>

<div class="container">
    <div class="page-header">
        <h1>Рейтинг користувачів</h1>
    </div>

    <? $users = get_user_raiting();
    display_user_raiting($users); ?>

</div>

<? page_footer() ?>
