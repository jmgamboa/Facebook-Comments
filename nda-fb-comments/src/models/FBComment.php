<?php

class FBComment {

  // shares same attrs as comment table
  var $comment_post_ID;
  var $comment_date; //ln 45
  var $comment_date_gmt; // 46
  var $comment_karma; // ln 38
  var $comment_author; // ln 59
  var $user_id; // ln 61
  var $comment_author_url; //http....=fb_user_id // ln 60
  var $comment_approved; //set to 0 
  var $comment_type;
  var $comment_meta; // fbid, userid, parent comment_id
  var $comment_content;
  var $comment_parent; 
  
  var $meta_fbid; // varchar meta
  var $meta_fb_user_id; // Varchar meta // Varchar meta


  // Function on construct
  function __construct($fbcomment='', $post_obj=''){
  		$this->import_fb_comment($post_obj, $fbcomment);
   }
   
   // Set model attrs
   function import_fb_comment($post_obj, $fbcomment){
      // Set post info
      $this->comment_post_ID = $post_obj->ID;
      $this->set_fbuser_info($fbcomment);
      // set message after decoding
      $this->comment_content = addslashes($fbcomment->message);
      $this->comment_karma = $fbcomment->like_count;
      $this->comment_approved = 1;
      $this->format_date($fbcomment);
      $this->comment_type = "facebook";
      $this->meta_fbid = $fbcomment->id;
      $this->set_meta_info($fbcomment);

   }
   // Set standard and gmt
   function format_date($fbcomment){
      $original_date_time = $fbcomment->created_time;
      $this->comment_date = $original_date_time;
      $this->comment_date_gmt = gmdate('Y-m-d H:i:s', strtotime($original_date_time));
   }
  // set post info
   function set_post_info($post_obj){
    // check if post set 
      if (!empty($post_obj)){
        $pid = $post_obj->id;
        echo $post_obj->id;
      }
   }
     // set fb user info
   function set_meta_info($fbcomment){
      if (isset($fbcomment->from->id)){
        $this->meta_fb_user_id = $fbcomment->from->id;
      }
   }

  // set fb user info
   function set_fbuser_info($fbcomment){
   // author url prefix for facebook, appending user id redirects to user profile
      $author_url_prefix = "http://www.facebook.com/profile.php?id=";
      if (isset($fbcomment->from->name) && ((isset($fbcomment->from->id)))){
        $this->comment_author = $fbcomment->from->name;
        $this->comment_author_url = $author_url_prefix . (string)$fbcomment->from->id;
        $this->user_id = 0;
      }
   }
   // save
  function save_new_comment($comment){
   $controller = new CommentController();
   $controller->save_new_comment($comment);
  }
}
