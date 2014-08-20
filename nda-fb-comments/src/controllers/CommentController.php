<?php 

class CommentController{

  // save as wordpress comments
  function save_new_comment($fbcomment){
    global $wpdb;
    $duplicate = $this->check_duplicate($fbcomment);
    echo 'checking duplicate';
    print_r($duplicate);
    if (!$duplicate){
      echo 'not duplicate';
      print_r($duplicate);
      $wpdb->insert("wp_comments", array(
      'comment_post_ID' => $fbcomment->comment_post_ID,
      'comment_author' => $fbcomment->comment_author,
      'comment_author_url' => $fbcomment->comment_author_url,                
      'comment_content' => $fbcomment->comment_content,
      'comment_type' => $fbcomment->comment_type,
      'comment_parent' => $fbcomment->comment_parent,
      'user_id' => $fbcomment->user_id,
      'comment_date' => $fbcomment->comment_date,
      'comment_date_gmt' => $fbcomment->comment_date_gmt
      ));
      // get id of last inserted comment
      $last_id = $wpdb->insert_id;
      // save meta values using last id
      echo 'saved comment';
      $this->save_meta_fbid($fbcomment, $last_id);
      $this->save_meta_user($fbcomment, $last_id);
    } else {
      echo 'duplicate entry based on fbid';
    }
  }


  // save meta properties according to primary id of the last inserted comment
  function save_meta_fbid($fbcomment, $last_id){
    global $wpdb;
    $wpdb->insert("wp_commentmeta", array(
       'comment_id' => $last_id,
       'meta_key' => 'fb_comment_id',
       'meta_value' => $fbcomment->meta_fbid
      ));
    echo 'saved meta fbid';
  }
  // save meta property fb user id according to primary id of the last inserted comment
  function save_meta_user($fbcomment, $last_id){
    global $wpdb;
    $wpdb->insert("wp_commentmeta", array(
       'comment_id' => $last_id,
       'meta_key' => 'fb_user_id',
       'meta_value' => $fbcomment->meta_fb_user_id
      ));
    echo 'saved meta user';
  }
  // check for duplicate by getting count of results
  function check_duplicate($fbcomment){
    global $wpdb;
    $fbid = $fbcomment->meta_fbid;
    $results = $wpdb->get_var("SELECT COUNT(*) FROM wp_commentmeta WHERE meta_key='fb_comment_id' AND meta_value='$fbid'");
    return $results;

  }
   // Update comment as a reply by setting parent id
   function save_reply($comment){
      // Get responses fbid; child fbid
      global $wpdb;
      print_r($comment);
      $comment_id = $comment->comment_ID;
      $parent_id = $comment->parent_comment_id;

      $wpdb->update(
        'wp_comments',
        array(
            'comment_parent' => $parent_id
          ),
        array('comment_ID' => $comment_id)
        );
      echo 'saved';
   }

// get response and format into php obj
	function curl_and_jdecode($endpoint){
		// Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$endpoint);
		// Execute
		$result=curl_exec($ch);
		// Closing
		curl_close($ch);
		// Format into obj
		$fb_comments_obj = json_decode($result);
		return $fb_comments_obj;
	}

}
