<?
require 'init.php';

require_login();

$video_link = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_link = $_POST['video_link'];
    if ($video_link == '') {
        $errors[]= "Введіть посилання на відео.";
    } else if (!get_video_id_from_youtube_link($video_link)) {
        $errors[]= "Неможливо розпізнати посилання.";
    }

    if (!$errors) {
        $post_id = create_post(get_current_user_id(), $video_link);
        redirect(APP_URL . '/view_post.php?id=' . $post_id);
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Tubogram</title>
    <? echo js_and_css() ?>
</head>

<? if (get_current_user_id()) {
    page_header('logged', 'create_post');
} else {
    page_header('not_logged', 'create_post');
}?>

<div class="container">
    <div class="page-header">
        <h1>Додати відео</h1>
    </div>

    <? if ($errors) display_errors($errors) ?>

    <div class="well">
        <form method="POST" action="<? echo APP_URL ?>/create_post.php">
            <div class="form-group">
                <label>Посилання на відео з YouTube</label>
                <input class="form-control" type="text" name="video_link" size="40" placeholder="http://youtube.com/?v=..." value="<? echo escape_html($video_link) ?>">
            </div>
            <input class="btn btn-primary" type="submit" value="Додати відео">
        </form>
    </div>
</div>

<? page_footer() ?>
