<? require 'init.php' ?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<?
if (get_current_user_id()) {
    page_header('logged', 'feed');
} else {
    page_header('not_logged', 'feed');
}
?>

<div class="container">
    <div class="page-header">
        <h1>Мої підписки</h1>
    </div>
    <? post_list(get_feed_posts(get_current_user_id())) ?>
</div>

<? page_footer() ?>