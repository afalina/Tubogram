<?
require 'init.php';

$post_id = $_POST['post_id'];
add_dislike_to_post($post_id, get_current_user_id());
echo get_count_of_likes_by_post_id($post_id);
?>