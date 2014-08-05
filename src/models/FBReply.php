<?php

class FBReply extends FbComment {
  var $parent_comment_id; // Varchar

  function __construct($fbcomment, $fb_comment_id){
    // Run parent attributes
    parent::__construct('', $fbcomment);
    // Set the parent id to fbid originally queried
  	$this->set_parent_id($fb_comment_id);
   }
   // Set self parent id to the original queried fbid
   function set_parent_id($fb_comment_id){
    $this->parent_comment_id = $fb_comment_id;
   }

   // Update comment as a reply by setting parent id
   function save_reply($comment, $con, $fb_comment_id){
      $controller = new CommentController();
      $controller->save_reply($comment, $con, $fb_comment_id);
   }

  // run parent function
  function update_thread($comment, $con){
    parent::update_thread($comment, $con);
  }
}
?>  
