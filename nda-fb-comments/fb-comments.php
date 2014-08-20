<?php
/*
 * @package Facebook Comments
 * @author Joaquin Gamboa
 * @version 1.0
 */
/*
  Plugin Name: Facebook Comments
  Plugin URI: http://localhost.com
  Description: This plugin allows you to save comments originally on facebook.
  Author: Joaquin Gamboa
  Version: 1.0
  Author URI: http://localhost.com
 */

require_once('src/models/FBComment.php');
require_once('src/models/FBReply.php');
require_once('src/tasks/getcomments.php');
require_once('src/tasks/getreplies.php');
require_once('src/controllers/CommentController.php');
require_once('src/api/fbcomments.php');


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