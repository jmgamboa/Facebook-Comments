<?php 

class GetReplies{

	// save commenets as replies from response
	function get_post_replies($postid){
		// get that comments' fbid and comment ID based on postid
		$wp_fb_comments = $this->get_post_fbids($postid);
		foreach ($wp_fb_comments as $wp_fb_comment){
			$endpoint = $this->format_endpoint($wp_fb_comment->meta_value);
			$this->save_reply($endpoint, $wp_fb_comment->comment_id);
		}
	}

	// get facebook meta ids
	function get_post_fbids($postid){
		$controller = new CommentController();
		$postid = intval($postid);
		$fbids = $controller->get_fbids_comment($postid);
		return $fbids;
	}

	// format_endpoint
	function format_endpoint($fbid){
		$endpoint = 'http://graph.facebook.com/' . $fbid . '/comments';
		return $endpoint;
	}

	// save reply
	function save_reply($endpoint, $wp_comment_id){
		$controller = new CommentController;
		$fbcomment = $controller->curl_and_jdecode($endpoint); 
		if(!empty($fbcomment->data)){
			foreach ($fbcomment->data as $fbcomment) {
				$controller->update_reply($fbcomment->id, $wp_comment_id);
			}
			if(isset($fbcomment->paging)){
				$endpoint = $fbcomment->paging->next;
				$this->save_reply($endpoint, $wp_comment_id);
			}

		}
	}
}

