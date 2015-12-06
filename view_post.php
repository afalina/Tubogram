<?
require 'init.php';

$post_id = $_GET['id'];
$post = get_post_by_id($post_id);
$comments = get_post_comments($post_id);
$comment_text = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $comment_text = $_POST['comment'];
    if ($comment_text == '') {
        $errors[] = "Коментар не може бути порожнім.";
    }
    if (!$errors) {
        $user_id = get_current_user_id();
        add_comment($post_id, $user_id, $comment_text);
        redirect(APP_URL . '/view_post.php?id=' . $post_id);
    }
}?>

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

<div class="video-player">
    <? echo create_youtube_iframe(get_video_id_from_youtube_link($post['link'])) ?>
</div>
<div class="container" style="width: 845px">
    <br>
    <h4>
        Запощено
        <a href="<? echo APP_URL . '/user.php?id=' . $post['user_id'] ?>">
            <? echo get_username_by_user_id($post['user_id']) ?></a>
        <? echo format_date($post['created_at']) ?>
        <div class="pull-right">
            <? like_button($post_id, get_current_user_id()) ?>
        </div>
    </h4>
</div>
<div class="container">

    <h2>Коментарі <span class="text-muted">
        (<? echo get_count_of_comments_by_post_id($post_id) ?>)
    </span></h2>
    <? if (get_current_user_id()): ?>
        <? if ($errors) display_errors($errors) ?>
        <div class="well">
            <form method="POST" action="<? echo APP_URL ?>/view_post.php?id=<?echo escape_html($post_id) ?>">
                <div class="form-group">
                    <textarea class="form-control" rows="3" name="comment" placeholder="Уведіть ваш коментар"></textarea>
                </div>
                <input class="btn btn-default" type="submit" value="Додати коментар">
            </form>
        </div>
    <? else: ?>
        <div class="alert alert-info">
            Для того, щоб залишати коментарі та ставити лайк 
            <a href="<? echo APP_URL . '/login.php' ?>">увійдіть</a> або 
            <a href="<? echo APP_URL . '/registration.php' ?>">зареєструйтеся</a>.
        </div>
    <? endif ?>

    <? display_comments($comments) ?>
</div>

<? page_footer() ?>

<script>
    $('.like-button.logged-in').on('click', function() {
        var $count = $(this).find('.count');
        var postId = $(this).data('post');
        var liked = $(this).hasClass('liked');
        var url = liked ? 'dislike_post.php' : 'like_post.php';

        $(this).toggleClass('liked');
        $.post(url, {post_id: postId}, function (data) {
            $count.text(data);
        });
    });
    $('.like-button.logged-out').popover({
        content: '<a href="<? echo APP_URL . '/login.php' ?>">Увійдіть</a> або <a href="<? echo APP_URL . '/registration.php' ?>">зареєструйтеся</a>',
        placement: 'left',
        trigger: 'focus',
        html: true
    });
</script>
