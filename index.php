<?
require 'init.php';
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<?
if (get_current_user_id()) {
    page_header('logged', 'new_videos');
} else {
    page_header('not_logged', 'new_videos');
}
?>

<div class="container">
    <? jumbotron() ?>

    <div class="page-header">
        <h1>Нові відео</h1>
    </div>

    <? post_list(get_latest_posts(10)) ?>

</div>

<? page_footer() ?>
