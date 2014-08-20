<?php

class FBReply {
  var $parent_comment_id; // Varchar
  var $comment_ID;

  // Init function
  function __construct($fbcomment, $fbid_obj){
    $this->set_parent_id($fbid_obj);
    $this->set_comment_id($fbcomment);
   }

   // Set self parent id to the original queried fbid
   function set_parent_id($fbid_obj){
    global $wpdb;
    $fbid = $fbid_obj->meta_value;
    $comments_meta_obj = $wpdb->get_results("SELECT * FROM `ed_commentmeta` WHERE meta_key='fb_comment_id' AND meta_value='$fbid'");
    $this->parent_comment_id = $comments_meta_obj[0]->comment_id;
   }
   // Set comment ID from meta table
   function set_comment_id($fbcomment){
    global $wpdb;
    $fbid = $fbcomment->id;
    $comment_meta_obj = $wpdb->get_results("SELECT * FROM `ed_commentmeta` WHERE meta_key='fb_comment_id' AND meta_value='$fbid'");
    $this->comment_ID = $comment_meta_obj[0]->comment_id;
   }
   // Update comment as a reply by setting parent id
   function save_reply($comment){
      $controller = new CommentController();
      $controller->save_reply($comment);
   }
}
?>  
