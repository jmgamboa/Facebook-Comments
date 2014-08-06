<?php 

class CommentController{

  function save_comment($comment, $con){
    // Insert comments
  	$insert_query = "INSERT INTO fbcommenttest (fb_comment_id, post_id, post_permalink, fb_user_name, fb_user_id, message, like_count, fb_created_time, parent_comment_id, comment_thread_id, comment_item_id) 
  	VALUES ('$comment->fb_comment_id', '$comment->post_id', '$comment->post_permalink', '$comment->fb_user_name', '$comment->fb_user_id', '$comment->message', '$comment->like_count', '$comment->fb_created_time', '$comment->parent_comment_id', '$comment->comment_thread_id', '$comment->comment_item_id')";
  	$insertion = $con->query($insert_query);

  }


   // Update comment as a reply by setting parent id
   function save_reply($comment, $con, $fb_comment_id){
      // Get responses fbid; child fbid
      $fbid = $comment->fb_comment_id;
      echo 'saved as reply';
      // Set now var for update 
      $now = date('Y-m-d H:i:s');
      // Sql to check if child fbid exists 
      $sql = "SELECT count(fb_comment_id) FROM fbcommenttest WHERE fb_comment_id='$fbid'";
      $result = $con->query($sql);
      // If exists update with parent fbid
      $exists = $result->num_rows;
    	if ($exists > 0){
        // Update parent comment field if the comment is a child
    		$update = "UPDATE fbcommenttest SET parent_comment_id='$fb_comment_id', check_count=check_count+1, check_date='$now' WHERE fb_comment_id='$fbid'";
    		$con->query($update);
  	  }  
   }

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
