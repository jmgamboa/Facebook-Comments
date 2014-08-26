<?php
/*
 * @package Facebook Comments
 * @author Joaquin Gamboa
 * @version 1.0
 */
/*
  Plugin Name: Facebook Comments
  Plugin URI: http://lsjdflksjdlfk.com
  Description: This plugin allows you to save comments originally on facebook.
  Author: Joaquin Gamboa
  Version: 1.0
  Author URI: http://nodinosaursallowed.com
 */

require_once('src/models/FBComment.php');
require_once('src/tasks/getcomments.php');
require_once('src/tasks/getreplies.php');
require_once('src/controllers/CommentController.php');

// get post comment and reply
function get_post_comment($postid){
  $comment = new GetComments();
  $comment->get_post_comments($postid);
  // set last processed ID **** 0 never expires
  set_transient( 'fb_comment_last_proccessed_post', $postid, 0);
  return $postid;
}

// the function to be called via ajax
function batch_get_fb_comments() {
  // get last id
  $postid = get_transient( 'fb_comment_last_proccessed_post' );  
  $controller = new CommentController();
  $postid = $controller->get_smaller_post($postid);
  $comment_post_id = get_post_comment($postid);  
  echo $comment_post_id;

  die(); // this is required to return a proper result
}

function get_post_reply($postid){
  $reply = new GetReplies();
  $reply->get_post_replies($postid);
  // set last processed ID, ***** 0 never expires
  set_transient( 'fb_reply_last_processsed_post', $postid, 0);
  return $postid;
}


function batch_get_fb_replies(){

  // get last ID
  $postid = get_transient( 'fb_reply_last_processsed_post');
  $controller = new CommentController();
  $postid = $controller->get_smaller_post($postid);
  $reply_post_id = get_post_reply($postid);
  echo $reply_post_id;
  die();/ this is required to return a proper result

}

// add the function call to the native wordpress ajax: this calls batch get_fbcomemnts
add_action( 'wp_ajax_batch_fb_comments_ajax', 'batch_get_fb_comments' );
add_action( 'wp_ajax_batch_fb_comments_ajax', 'batch_get_fb_replies' );

// admin page source
function nda_fb_comments_menu_render() {
    global $title;
    ?>
        <h2><?php echo $title;?></h2>
        <?php
        include_once('wp/admin/fb-comments-admin.php');
}

// add admin page
function nda_fb_comments_pages(){
  add_menu_page( 'FB Comments', 'FB Comments', 'activate_plugins', 'nda-fb-comments', 'nda_fb_comments_menu_render', '', 21 );
}


// add the admin menus by running the nda_toolbox_pages function 
// on a wordpress callback hook
add_action('admin_menu', 'nda_fb_comments_pages');