<?php 

class GetReplies{

	// function to be called on init or perform **************************
	function perform(){
		// Establish connection and comment
	    $con = new mysqli();
	    $controller = new CommentController();
	    $batch_limit = 0;
		while($batch_limit<5000){
			// Get fbids
			$fbids = $this->get_fbids($con, $batch_limit);
			// If comment has replies; update records with the parent id
			foreach ($fbids as $fbid){
				$fb_comment_id = $fbid['fb_comment_id'];
				$endpoint = 'http://graph.facebook.com/' . $fb_comment_id . '/comments?limit=1';
				$this->get_replies($endpoint, $fb_comment_id, $con);
			}
			$batch_limit += 500;
		}
		// close connection
		$con->close();
		Resque::enqueue('durrr', 'GetReplies');
	}
	
	// Get existing FBID's
	function get_fbids($con, $batch_limit){
		// LIMIT 10, 20 // where 10 is the range and 20 is the amount, when you pass in the batch limit it will be replaced with 10
		$fbids_sql = "SELECT fb_comment_id FROM fbcommenttest LIMIT $batch_limit, 500";
		$fbids = $con->query($fbids_sql);
		return $fbids;
	}

	// Get, Format, and Save comments which are replies, using Fbid
	function get_replies($endpoint, $fbid, $con){
		// Get and Format api response
		$controller = new CommentController();
		$fb_comments_obj = $controller->curl_and_jdecode($endpoint);
		// If response not empty; create, save, and decode pagination
		if(!empty($fb_comments_obj->data)){
			$fb_comments_data = $fb_comments_obj->data;
			foreach ($fb_comments_data as $fb_comment){
				$comment = new FBReply($fb_comment, $fbid);
				$comment->save_reply($comment, $con, $fbid);		
			}
			// Check for pagination
			if(isset($fb_comments_obj->paging->next)){
				$endpoint = $fb_comments_obj->paging->next;
				$this->get_replies($endpoint, $fbid, $con);
			} else {
				echo 'no paging';
			}
		}
	}
}
?>
