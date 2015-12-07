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
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#most-likes" data-toggle="tab"><h4>Найбільше лайків</h4></a>
        </li>
        <li>
            <a href="#most-comments" data-toggle="tab"><h4>Найбільш коментуємі</h4></a>
        </li>
    </ul>
    <br>
    <div class="tab-content">
        <div class="tab-pane active" id="most-likes">
            <? post_list(get_best_posts_by_likes(12)) ?>
        </div>
        <div class="tab-pane" id="most-comments">
            <? post_list(get_best_posts_by_comments(12)) ?>
        </div>
    </div>
</div>

<? page_footer() ?>
