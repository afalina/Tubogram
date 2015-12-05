<?
require 'init.php';

$user_id = $_GET['id'];
$page_url = APP_URL . '/user.php?id=' . $user_id;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['follow']) {
        follow_user($user_id, get_current_user_id());
        redirect($page_url);
    }
    if ($_POST['unfollow']) {
        unfollow_user($user_id, get_current_user_id());
        redirect($page_url);
    }
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
    <div class="page-header">
        <h1>
            Відео користувача <? echo get_username_by_user_id($user_id) ?>
            <? if (get_current_user_id() && get_current_user_id() != $user_id): ?>
                <? if (is_following(get_current_user_id(), $user_id)): ?>
                    <form class="inline" method="POST" action="<? echo escape_html($page_url) ?>">
                        <input class="btn btn-lg btn-danger" type="submit" name="unfollow" value="Відписатися">
                    </form>
                <? else: ?>
                    <form class="inline" method="POST" action="<? echo escape_html($page_url) ?>">
                        <input class="btn btn-lg btn-primary" type="submit" name="follow" value="Підписатися">
                    </form>
                <? endif ?>
            <? endif ?>
        </h1>
    </div>

    <? post_list(get_user_posts_by_user_id($user_id)) ?>
</div>
<? page_footer() ?>
