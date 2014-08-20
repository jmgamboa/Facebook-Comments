<?php 

class GetComments {

	// may take post id? 
	function perform(){
		$batch = 0;
		while ($batch<2000){
			// Get ids
			$pids = $this->get_pids($batch);	
			// Loop, curl, and save each
			foreach ($pids as $pid){
				$post_obj = $this->get_post_and_link($pid->id);
				$endpoint = $this->format_endpoint($post_obj);
				$this->save_comment($post_obj, $endpoint);
			}
			// Increase batch limit
			$batch += 50;
			echo $batch;
		}
	}
	// get post obj from Post ID*
	function get_post_and_link($pid){
		// get post
		$post = get_post($pid);
		// get permalink
		$url = get_the_permalink($pid);
		$post->permalink = $url;
		return $post;
	}
	// format api
	function format_endpoint($post_obj){
		$url = $post_obj->permalink;
		$endpoint = 'http://graph.facebook.com/comments?id=' .  $url . '&filter=stream&limit=200';
		return $endpoint;	
	}
	// save comment from responses
	function save_comment($post_obj, $endpoint){
		$controller = new CommentController;
		$fb_comments_obj = $controller->curl_and_jdecode($endpoint); // takes endpoint
		if(!empty($fb_comments_obj->data)){
			// set data
			$fb_comments_data = $fb_comments_obj->data;
			// Save each comment and update thread
			foreach ($fb_comments_data as $fb_comment){
				$comment = new FBComment($fb_comment, $post_obj);
				echo '<pre>';
				print_r($comment);
				$comment->save_new_comment($comment);
			}
			// If pagination is set, recrusively call the function
			if(isset($fb_commentents_obj->paging->next)){
				$endpoint = $fb_comments_obj->paging->next;
				$this->save_comments($post_obj, $endpoint);
			}	
		}
	}
	// Get ids depending on batch
	function get_pids($batch){
		global $wpdb;
		$pids = $wpdb->get_results("SELECT id FROM `wp_posts` WHERE post_status='publish' LIMIT $batch, 50");
		return $pids;
	}
}

