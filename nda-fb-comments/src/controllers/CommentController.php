<?php 

class CommentController{

  // save as wordpress comments
  function save_new_comment($fbcomment) {
    global $wpdb;
    $duplicate = $this->check_duplicate($fbcomment);
    if (!$duplicate){
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
      $this->save_meta_fbid($fbcomment, $last_id);
      $this->save_meta_user($fbcomment, $last_id);
     }
  }
  
  // save meta properties according to primary id of the last inserted comment
  function save_meta_fbid($fbcomment, $last_id) {
    global $wpdb;
    $wpdb->insert("wp_commentmeta", array(
       'comment_id' => $last_id,
       'meta_key' => 'fb_comment_id',
       'meta_value' => $fbcomment->meta_fbid
      ));
  }
	
  // save meta property fb user id according to primary id of the last inserted comment
  function save_meta_user($fbcomment, $last_id) {
    global $wpdb;
    $wpdb->insert("wp_commentmeta", array(
       'comment_id' => $last_id,
       'meta_key' => 'fb_user_id',
       'meta_value' => $fbcomment->meta_fb_user_id
      ));
  }
	
  // check for duplicate by getting count of results
  function check_duplicate($fbcomment) {
    global $wpdb;
    $fbid = $fbcomment->meta_fbid;
    $results = $wpdb->get_var("SELECT COUNT(*) FROM wp_commentmeta WHERE meta_key='fb_comment_id' AND meta_value='$fbid'");
    return $results;
  }

	// get response and format into php obj
	function curl_and_jdecode($endpoint) {
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

	function get_fbids_comment($postid) {
            global $wpdb;
	    $fbids = $wpdb->get_results("SELECT wp_commentmeta.meta_value, wp_commentmeta.comment_id
					 FROM wp_comments
					 JOIN wp_commentmeta
					 ON wp_comments.comment_ID = wp_commentmeta.comment_id
					 WHERE comment_post_ID = '$postid'
					 AND  wp_commentmeta.meta_key = 'fb_comment_id';");
            return $fbids;
	}

	function update_reply($fbcomment_id, $wp_comment_id) {
	    global $wpdb;
	    $wpdb->query("UPDATE wp_comments 
	                 JOIN wp_commentmeta
	                 ON wp_comments.comment_ID = wp_commentmeta.comment_id
	                 SET wp_comments.comment_parent = '$wp_comment_id'
	                 WHERE wp_commentmeta.meta_key = 'fb_comment_id'
	                 AND wp_commentmeta.meta_value = '$fbcomment_id';");
        }

	// Get ids depending on batch
	function get_smaller_post($postid=null) {
	    global $wpdb;
	    if ($postid) {
			$pid = $wpdb->get_results("SELECT ID FROM `wp_posts` WHERE  post_status='publish' AND post_type='post' AND ID < '$postid' ORDER BY ID DESC LIMIT 1");
		} else {
			$pid = $wpdb->get_results("SELECT ID FROM `wp_posts` WHERE  post_status='publish' AND post_type='post' ORDER BY ID DESC LIMIT 1");
		}
		$postid = $pid[0];
		return $postid->ID;
	}
}


