<?
require 'config.php';

function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
}

function get_user_id_by_username($username) {
    global $db;
    $query = $db->prepare('SELECT id FROM users WHERE username=:username');
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    return $query->fetchColumn();
}

function get_username_by_user_id($user_id) {
    global $db;
    $query = $db->prepare('
        SELECT username
        FROM users
        WHERE id=:user_id
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchColumn();
}

function create_user($username, $password) {
    global $db;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $query = $db->prepare('
        INSERT users SET
            username=:username,
            password_hash=:password_hash
    ');
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
    $query->execute();
    return (int) $db->lastInsertId();
}

function login_user($user_id) {
    $_SESSION['user_id'] = $user_id;
}

function logout_user() {
    $_SESSION['user_id'] = 0;
}

function get_current_user_id() {
    return (int) $_SESSION['user_id'];
}

function require_login() {
    if (!get_current_user_id()) {
        redirect(APP_URL . '/login.php');
    }
}

function check_user_password($username, $password) {
    global $db;
    $query = $db->prepare('SELECT password_hash FROM users WHERE username=:username');
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $hash = $query->fetchColumn();
    return password_verify($password, $hash);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function format_date($date) {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    return "$day.$month.$year";
}

function create_youtube_iframe($video_id) {
    return '<iframe width="854" height="480" src="https://www.youtube.com/embed/' .
           escape_html($video_id) . '" frameborder="0" allowfullscreen></iframe>';
}

function youtube_thumb_url($video_id) {
    return 'http://img.youtube.com/vi/' . escape_html($video_id) . '/0.jpg';
}

function create_youtube_thumbnail($video_id) {
    return '<img width="200" src="' . youtube_thumb_url($video_id) . '">';
}

function get_video_id_from_youtube_link($url) {
    $url_parts = parse_url($url);
    if ($url_parts['host'] == 'www.youtube.com') {
        parse_str($url_parts['query'], $query_parts);
        return $query_parts['v'];
    } else if ($url_parts['host'] == 'youtu.be') {
        return substr($url_parts['path'], 1);
    }
}

function create_post($user_id, $video_link) {
    global $db;
    $query = $db->prepare('
        INSERT posts SET
        user_id=:user_id,
        link=:video_link
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':video_link', $video_link, PDO::PARAM_STR);
    $query->execute();
    return (int) $db->lastInsertId();
}

function get_post_by_id($post_id) {
    global $db;
    $query = $db->prepare('
        SELECT id, user_id, created_at, link
        FROM posts
        WHERE id=:post_id
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function get_latest_posts($limit) {
    global $db;
    $query = $db->prepare('
        SELECT
            posts.id AS id,
            posts.user_id AS user_id,
            posts.created_at AS created_at,
            posts.link AS link,
            users.username AS username,
            COUNT(post_likes.post_id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)
                AS comment_count
        FROM posts
        INNER JOIN users ON
            users.id = posts.user_id
        LEFT JOIN post_likes ON
            post_likes.post_id = posts.id
        GROUP BY posts.id
        ORDER BY posts.id DESC
        LIMIT :limit
    ');
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function add_comment($post_id, $user_id, $text) {
    global $db;
    $query = $db->prepare('
        INSERT comments SET
        post_id=:post_id,
        user_id=:user_id,
        text=:text');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':text', $text, PDO::PARAM_STR);
    $query->execute();
    return (int) $db->lastInsertId();
}

function get_post_comments($post_id) {
    global $db;
    $query = $db->prepare('
        SELECT
            comments.id,
            comments.user_id,
            comments.post_id,
            comments.text,
            comments.created_at,
            users.username
        FROM comments
        INNER JOIN users ON users.id = comments.user_id
        WHERE post_id=:post_id
        ORDER BY comments.id
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function js_and_css() {
    ?>
    <link rel="stylesheet" href="styles/bootstrap.css">
    <link rel="stylesheet" href="styles/bootstrap-theme.css">
    <link rel="stylesheet" href="styles/global.css">
    <script src="scripts/jquery.js"></script>
    <script src="scripts/bootstrap.js"></script>
    <?
}

function page_header($header_type, $active_menu_item=null) {
    ?>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="<? echo APP_URL ?>">
                    <img alt="Tubogram" src="images/tubogram-logo.png" height="30">
                </a>
            </div>

            <ul class="nav navbar-nav">
                <li <? if ($active_menu_item == 'new_videos'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>">Нові відео</a></li>
                <li <? if ($active_menu_item == 'best_videos'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>/best_videos.php">Популярні</a></li>
                <? if ($header_type == 'logged'): ?>
                    <li <? if ($active_menu_item == 'feed'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>/feed.php">Мої підписки</a></li>
                <? endif ?>
                <li <? if ($active_menu_item == 'user_rating'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>/user_rating.php">Рейтинг користувачів</a></li>
            </ul>

            <form class="navbar-form navbar-left" method="get" action="<? echo APP_URL ?>/search_user.php">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="search" placeholder="Пошук користувачів">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </form>

            <? if ($header_type == 'logged'): ?>
                <ul class="nav navbar-nav navbar-right">
                    <? if ($active_menu_item != 'create_post'): ?>
                        <li>
                            <p class="navbar-btn">
                                <a href="<? echo APP_URL ?>/create_post.php" class="btn btn-default"><span class="glyphicon glyphicon-film"></span> Додати відео</a>
                            </p>
                        </li>
                    <? endif ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span>
                            <? echo get_username_by_user_id(get_current_user_id()) ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<? echo APP_URL ?>/user.php?id=<? echo get_current_user_id() ?>">Моя сторінка</a></li>
                            <li><a href="<? echo APP_URL ?>/logout.php">Вихід</a></li>
                        </ul>
                    </li>
                </ul>
            <? else: ?>
                <ul class="nav navbar-nav navbar-right">
                    <li <? if ($active_menu_item == 'registration'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>/registration.php">Реєстрація</a></li>
                    <li <? if ($active_menu_item == 'login'): ?>class="active"<? endif ?>><a href="<? echo APP_URL ?>/login.php">Вхід</a></li>
                </ul>
            <? endif ?>
        </div>
    </nav>
    <?
}

function jumbotron() {
    $logged_in = get_current_user_id();
    ?>
    <div class="jumbotron <? if ($logged_in): ?>welcome-logged-in<? else: ?>welcome<? endif ?>">
        <h1>Тюбограм</h1>
        <? if ($logged_in): ?>
            <p>Найкращі відео YouTube, що обрали саме ви.<br>
               Найгарніші звірята, найтепліші коменти, найщиріші лайки.<br>
               Додавайте відео та запрошуйте друзів!
            </p>
            <p>
                <a href="<? echo APP_URL ?>/create_post.php" class="btn btn-default btn-lg">
                    <span class="glyphicon glyphicon-plus"></span>
                    Додати відео
                </a>
            </p>

        <? else: ?>
            <p>Наполовину YouTube, наполовину Instagram. Це краще, що траплялося в інтернеті за останні 80 років. Усі гарні котики вже тут, приєднуйтеся і ви.
            </p>
            <p>
                <a class="btn btn-primary btn-lg" href="<? echo APP_URL ?>/registration.php">Зареєструватися</a> або
                <a class="btn btn-default btn-lg" href="<? echo APP_URL ?>/login.php">Увійти</a>
            </p>
        <? endif ?>
    </div>
    <?
}

function display_errors($errors) { ?>
    <div class="alert alert-danger" role="alert">
        <? echo implode("<br>", $errors) ?>
    </div>
    <?
}

function display_comments($comments) {
    ?>
    <? foreach ($comments as $comment): ?>
        <? $user_link = APP_URL . '/user.php?id=' . $comment['user_id'] ?>
        <a href="<? echo $user_link ?>"><strong><? echo $comment['username'] ?></strong></a>
        <small class="text-muted"><? echo format_date($comment['created_at']) ?></small>
        <p>
            <? echo escape_html($comment['text']) ?>
        </p>
        <hr>
    <? endforeach ?>
    <?
}

function post_list($posts) { ?>
    <div class="row">
        <? foreach ($posts as $post): ?>
            <div class="col-sm-4 col-md-4 col-lg-3">
                <div class="thumbnail">
                    <a href="<? echo APP_URL . '/view_post.php?id=' . $post['id'] ?>">
                        <img class="img-responsive" 
                            src="<? echo youtube_thumb_url(get_video_id_from_youtube_link($post['link'])) ?>">
                    </a>
                    <div class="caption">
                        <a href="<? echo APP_URL . '/user.php?id=' . $post['user_id'] ?>"><? echo $post['username'] ?></a>
                        <small class="text-muted"><? echo format_date($post['created_at']) ?></small>
                        <small class="text-muted pull-right">
                            <span class="glyphicon glyphicon-heart"></span> 
                            <? echo $post['like_count'] ?> &nbsp;
                            <span class="glyphicon glyphicon-comment"></span> 
                            <? echo $post['comment_count'] ?>
                        </small>
                    </div>
                </div>
            </div>
        <? endforeach ?>
    </div>
    <?
}

function page_footer() { ?>
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-muted">&#x1F42C; 2015 Мария Бройде</div>
        </div>
    </footer>
    <?
}

function get_user_posts_by_user_id($user_id) {
    global $db;
    $query = $db->prepare('
        SELECT
            posts.id AS id,
            posts.user_id AS user_id,
            posts.created_at AS created_at,
            posts.link AS link,
            users.username AS username,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)
                AS comment_count,
            (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id)
                AS like_count
        FROM posts
        INNER JOIN users ON
            users.id = posts.user_id
        WHERE
            posts.user_id=:user_id
        ORDER BY posts.created_at DESC
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function get_feed_posts($user_id) {
    global $db;
    $query = $db->prepare('
        SELECT
            posts.id AS id,
            posts.user_id AS user_id,
            posts.created_at AS created_at,
            posts.link AS link,
            users.username AS username,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)
                AS comment_count,
            (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id)
                AS like_count
        FROM posts
        INNER JOIN users ON
            users.id = posts.user_id
        LEFT JOIN followers ON
            followers.user_id=posts.user_id AND
            followers.follower_id=:user_id
        WHERE
            posts.user_id=:user_id OR
            followers.user_id IS NOT NULL
        ORDER BY posts.created_at DESC
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function is_following($follower_id, $user_id) {
    global $db;
    $query = $db->prepare('
        SELECT 1
        FROM followers
        WHERE user_id=:user_id AND follower_id=:follower_id
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
    $query->execute();
    return (bool) $query->fetchColumn();
}

function follow_user($user_id, $follower_id) {
    global $db;
    $query = $db->prepare('
        INSERT followers SET
        user_id=:user_id,
        follower_id=:follower_id
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
    $query->execute();
}

function unfollow_user($user_id, $follower_id) {
    global $db;
    $query = $db->prepare('
        DELETE FROM followers
        WHERE
        user_id=:user_id AND
        follower_id=:follower_id
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
    $query->execute();
}

function is_liked($post_id, $user_id) {
    global $db;
    $query = $db->prepare('
        SELECT 1
        FROM post_likes
        WHERE post_id=:post_id AND user_id=:user_id
        ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return (bool) $query->fetchColumn();
}

function add_like_to_post($post_id, $user_id) {
    global $db;
    $query = $db->prepare('
        INSERT post_likes SET
        post_id=:post_id,
        user_id=:user_id
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
}

function add_dislike_to_post($post_id, $user_id) {
    global $db;
    $query = $db->prepare('
        DELETE FROM post_likes
        WHERE
        post_id=:post_id AND
        user_id=:user_id
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
}

function get_count_of_likes_by_post_id($post_id) {
    global $db;
    $query = $db->prepare('
        SELECT COUNT(post_id) 
        FROM post_likes 
        WHERE post_id=:post_id;
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->execute();
    return (int) $query->fetchColumn();
}

function get_count_of_comments_by_post_id($post_id) {
    global $db;
    $query = $db->prepare('
        SELECT COUNT(*) 
        FROM comments 
        WHERE post_id=:post_id;
    ');
    $query->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $query->execute();
    return (int) $query->fetchColumn();
}

function get_follower_count($user_id) {
    global $db;
    $query = $db->prepare('
        SELECT COUNT(*)
        FROM followers
        WHERE user_id=:user_id
    ');
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return (int) $query->fetchColumn();

}

function like_button($post_id, $user_id) {
    $like_count = get_count_of_likes_by_post_id($post_id);
    $class = 'btn like-button';
    if (is_liked($post_id, $user_id)) {
        $class .= ' liked';
    }
    if ($user_id) {
        $class .= ' logged-in';
    } else {
        $class .= ' logged-out';
    }
    ?>
        <button class="<? echo $class ?>" data-post="<? echo escape_html($post_id) ?>">
            Мені подобається
            <span class="glyphicon glyphicon-heart heart"></span>
            <span class="count"><? echo escape_html($like_count) ?></span>
        </button>
    <?
}

function get_best_posts_by_comments($limit) {
    global $db;
    $query = $db->prepare('
        SELECT
            posts.id AS id,
            posts.user_id AS user_id,
            posts.created_at AS created_at,
            posts.link AS link,
            users.username AS username,
            COUNT(comments.post_id) AS comment_count,
            (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id)
                AS like_count
        FROM posts
        INNER JOIN users ON
            users.id = posts.user_id
        LEFT JOIN comments ON
            comments.post_id = posts.id
        GROUP BY posts.id
        ORDER BY comment_count DESC
        LIMIT :limit
    ');
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function get_best_posts_by_likes($limit) {
    global $db;
    $query = $db->prepare('
        SELECT
            posts.id AS id,
            posts.user_id AS user_id,
            posts.created_at AS created_at,
            posts.link AS link,
            users.username AS username,
            COUNT(post_likes.post_id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)
                AS comment_count
        FROM posts
        INNER JOIN users ON
            users.id = posts.user_id
        LEFT JOIN post_likes ON
            post_likes.post_id = posts.id
        GROUP BY posts.id
        ORDER BY like_count DESC
        LIMIT :limit
    ');
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function get_user_raiting() {
    global $db;
    $query = $db->prepare('
        SELECT 
            users.id, users.username,
            (SELECT COUNT(*) FROM followers WHERE followers.user_id = users.id) AS follower_count
        FROM users
        LEFT JOIN followers ON
            followers.user_id = users.id
        GROUP BY users.id
        ORDER BY follower_count DESC;
    ');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function display_user_raiting($users) {
    $i = 1;
    ?>
    <table class="table" style="width: 380px;">
        <? foreach ($users as $user): ?>
            <tr>
                <td><? echo $i . '. ' ?></td>
                <td>
                    <a href="<? echo APP_URL . '/user.php?id=' . $user['id'] ?>">
                    <? echo $user['username']?></a>
                </td>
                <td> <? echo $user['follower_count']?> підписників </td>
                <? $i++ ?>
            </tr>
        <? endforeach ?>
    </table><?
}

$db = new PDO(
    'mysql:host=' . DATABASE_HOST .
    ';dbname=' . DATABASE_NAME .
    ';charset=' . DATABASE_CHARSET,
    DATABASE_USER,
    DATABASE_PASSWORD
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

session_start();

?>