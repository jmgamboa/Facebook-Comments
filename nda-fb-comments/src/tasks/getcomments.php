<?php 

class GetComments {

	// get permalink and format permalink
	function get_permalink($postid){
		$thepermalink = get_permalink($postid);
		return $thepermalink;
	}

	// get comment obj based on pid; Main Func()
	function get_post_comments($postid){
		$postobj = new stdClass();
		$postobj->ID = $postid;
		$postobj->permalink = $this->get_permalink($postid);
		$postobj->fb_endpoint = $this->format_endpoint($postobj->permalink);
		$this->save_comment($postobj, $postobj->fb_endpoint);
	}
	// format api
	function format_endpoint($thepermalink){
		$url = $thepermalink;
		$endpoint = 'http://graph.facebook.com/comments?id=' .  $url . '&filter=stream&limit=200';
		return $endpoint;	
	}
	// save comment from responses
	function save_comment($post_obj, $endpoint){
		$controller = new CommentController;
		$fb_comments_obj = $controller->curl_and_jdecode($endpoint);
		if(!empty($fb_comments_obj->data)){
			$this->model_and_save($post_obj, $fb_comments_obj);
			// If pagination is set, recrusively call the function
			if(isset($fb_commentents_obj->paging->next)){
				$endpoint = $fb_comments_obj->paging->next;
				$this->save_new_comments($post_obj, $endpoint);
			}	
		}
	}

	// model each response then save
	function model_and_save($post_obj, $fb_comments_obj){
		$fb_comments_data = $fb_comments_obj->data;
		// Save each comment and update thread
		foreach ($fb_comments_data as $fb_comment){
			$comment = new FBComment($fb_comment, $post_obj);
			$comment->save_new_comment($comment);
		}
	}

}

