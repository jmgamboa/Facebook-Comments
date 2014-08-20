<?php 

class GetReplies{

	function perform(){
		$batch = 0;
		while ($batch<50){
			 // get the fbid
			$fbids = $this->get_fbids($batch);
			foreach ($fbids as $fbid){
				// format url
				$endpoint = $this->format_endpoint($fbid);
				// when you get the url you see if that is the parent of any other comment
				$this->get_replies($endpoint, $fbid);
			}
			$batch += 50;
		}
	}
	// get facebook meta ids
	function get_fbids($batch){
		global $wpdb;
		$fbids = $wpdb->get_results("SELECT * FROM `wp_commentmeta` WHERE meta_key='fb_comment_id' LIMIT $batch, 1");
		return $fbids;
	}
	// format api urls
	function format_endpoint($fbid){
		$fbid = $fbid->meta_value;
		$endpoint = 'http://graph.facebook.com/' . $fbid . '/comments';
		return $endpoint;
	}
	// save commenets as replies from response
	function get_replies($endpoint, $fbid){
		$controller = new CommentController;
		$fb_comments_obj = $controller->curl_and_jdecode($endpoint); // takes endpoint
		if(!empty($fb_comments_obj->data)){
	 		// set data
			$fbcomments = $fb_comments_obj->data;
	 		// Save each comment and update thread
			foreach ($fbcomments as $fbcomment){
				$comment = new FBReply($fbcomment, $fbid);
				$comment->save_reply($comment);
			}
			// If pagination is set, recrusively call the function
			if(isset($fb_commentents_obj->paging->next)){
				$endpoint = $fb_comments_obj->paging->next;
				$this->get_replies($endpoint, $fbid);
			}	
		}
	}
}

