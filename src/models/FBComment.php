<?php

class FbComment {

  var $fb_comment_id; // Varchar 
  var $post_id;  // Big Int
  var $post_permalink; // Varchar 
  var $fb_user_name; // Varchar
  var $fb_user_id; // Varchar
  var $message; // Long Text
  var $like_count; // Int
  var $fb_created_time; // Datetime
  var $parent_comment_id; // Varchar
  var $comment_thread_id; // Int
  var $comment_item_id;  // Int
  
  // Function on construct
  function __construct($post='', $fbcomment=''){
      // Set self params
  		$this->import_fb_comment($post, $fbcomment);
   }
   
   // Set model attrs
   function import_fb_comment($post, $fbcomment){
    // Set model attributes if set
  		$this->fb_comment_id = $fbcomment->id;
      // Set post info
      $this->set_post_info($post);
      $this->set_fbuser_info($fbcomment);
      // set message after decoding
  		$this->message = addslashes($fbcomment->message);
  		$this->like_count = $fbcomment->like_count;
  		$this->fb_created_time = $fbcomment->created_time;
  	  // Split underscore and set thread and item id
    	list($thread_id, $item_id) = explode('_', $fbcomment->id);
    	$this->comment_thread_id = $thread_id;
    	$this->comment_item_id = $item_id;
   }
  // set post info
   function set_post_info($post){
      if (!empty($post)){
        $this->post_id = $post['id'];
        $this->post_permalink = $post['url']; 
      }
   }
  // set fb user info
   function set_fbuser_info($fbcomment){
      if (isset($fbcomment->from->name) && ((isset($fbcomment->from->id)))){
        $this->fb_user_name = $fbcomment->from->name;
        $this->fb_user_id = $fbcomment->from->id; 
      }
   }
 // Save
  function save($comment, $con){
   $controller = new CommentController();
   $controller->save_comment($comment, $con);

  }

  // Update meta table 
  function update_thread($comment, $con){ 
    $controller = new CommentController();
    $controller->update_thread($comment, $con);
  }

}
