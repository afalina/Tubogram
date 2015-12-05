<? require 'init.php' ?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<?
if (get_current_user_id()) {
    page_header('logged', 'best_videos');
} else {
    page_header('not_logged', 'best_videos');
}
?>
<div class="container">
    <div class="page-header">
        <h1>Найбільш коментуємі</h1>
    </div>
    <? post_list(get_best_posts_by_comments(10)) ?>
    <div class="page-header">
        <h1>Найбільше лайків</h1>
    </div>
    <? post_list(get_best_posts_by_likes(10)) ?>
</div>

<? page_footer() ?>