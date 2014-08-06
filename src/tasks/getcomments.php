<?php 

class GetComments{

	// Runs on init for resque
	function perform(){
		// Establish conenction and controller
		$con = new mysqli();
		$controller = new CommentController();

		// Batch requests
		$batch_limit = 0;
		while($batch_limit<5000){
			// Get batch of posts
			$posts = $this->get_posts($batch_limit, $con);
			// Send to api endpoint
			foreach ($posts as $post){
				$url = $post['url'];
				$endpoint = 'http://graph.facebook.com/comments?id=' .  $url . '&filter=stream&limit=200';
				$this->get_comments($endpoint, $post, $con);
			}
			// Increment and save batch key
			$batch_limit += 500;
		}
		// Close connection thn enque
		$con->close();
		Resque::enqueue('durrr', 'GetComments');
	}

	function get_posts($batch_limit, $con){
		// Get old url articles within batch range
		$post_sql = "SELECT * FROM firm_article LIMIT $batch_limit, 500";
		$posts = $con->query($post_sql);
		return $posts;
	}

	// send request, save it, and check for pagination 
	function get_comments($endpoint, $post_obj, $con){
		$fb_comments_obj = $this->curl_and_jdecode($endpoint);
		// loop through responsesd
		$controller = new CommentController;
		if(!empty($fb_comments_obj->data)){
			// set data
			$fb_comments_data = $fb_comments_obj->data;
			// Save each comment and update thread
			foreach ($fb_comments_data as $fb_comment){
				$comment = new FBComment($post_obj, $fb_comment);
				$comment->save($comment, $con);
			}
			// If pagination is set, recrusively call the function
			if(isset($fb_commentents_obj->paging->next)){
				$endpoint = $fb_comments_obj->paging->next;
				$this->get_comments($endpoint, $post_obj, $con);
			}	
		}
	}
}

?>
