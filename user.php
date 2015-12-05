<?
require 'init.php';

$user_id = $_GET['id'];
$page_url = APP_URL . '/user.php?id=' . $user_id;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['follow'])) {
        follow_user($user_id, get_current_user_id());
        redirect($page_url);
    } else if (isset($_POST['unfollow'])) {
        unfollow_user($user_id, get_current_user_id());
        redirect($page_url);
    }
}

$follower_count = get_follower_count($user_id);

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
            <? echo ucfirst(get_username_by_user_id($user_id)) ?>
            <? if (get_current_user_id() && get_current_user_id() != $user_id): ?>
                <? if (is_following(get_current_user_id(), $user_id)): ?>
                    <form class="inline btn-group" method="POST" action="<? echo escape_html($page_url) ?>">
                        <button class="btn btn-danger" type="submit" name="unfollow">
                            <span class="glyphicon glyphicon-minus"></span>
                            Відписатися
                        </button>
                        <button class="btn btn-default" style="background: transparent">
                            <? echo $follower_count ?>
                        </button>
                    </form>
                <? else: ?>
                    <form class="inline btn-group" method="POST" action="<? echo escape_html($page_url) ?>">
                        <button class="btn btn-primary" type="submit" name="follow">
                            <span class="glyphicon glyphicon-plus"></span>
                            Підписатися
                        </button>
                        <button class="btn btn-default" style="background: transparent">
                            <? echo $follower_count ?>
                        </button>
                    </form>
                <? endif ?>
            <? else: ?>
                <small><? echo $follower_count ?> підписчиків</small>
            <? endif ?>
        </h1>
    </div>

    <? post_list(get_user_posts_by_user_id($user_id)) ?>
</div>
<? page_footer() ?>
