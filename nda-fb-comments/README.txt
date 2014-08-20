Adrians Demo on WP

// ADRIANS DEMO CODEEEEEEEEEEEEEEEEEEEEEEE

//  function get_fb_comments(){
//  	global $wpdb;

//  	$table_name = $wpdb->prefix . "commentmeta";

//  	// get_var is a returns a single row
	// $results = $wpdb->get_var('SELECT COUNT(*) FROM ' . $table_name . ' WHERE meta_key = "fb_comment"');

// 	// get_results is a returns an array of rows
// 	$results = $wpdb->get_results('SELECT * FROM ' . $table_name . ' WHERE meta_key = "fb_comment"');
// 	if (!$results){
// 		$results = 0;
// 	}
// 	return $results;
//  }

// function get_wp_post_ids(){
// 	global $post;

// 	$args = array(
// 		'fields' => 'ids',
// 		'orderby' => 'ID', 
// 		'order' => 'DESC',
// 		'posts_per_page' => 50,
// 		'type' => 'post',
// 		'status' => 'publish'
// 		//'nopaging' => true
// 	);
// 	$the_query = new WP_Query( $args );
// 	return $the_query;
// }

// $jackson = get_wp_post_ids();

// foreach ($jackson->posts as $jack) {
// 	$thepermalink = get_permalink($jack, false);
// 	echo '<pre>';



// 	$url = preg_replace( '/\/[0-9]+\/?$/', '/', $url, 1);
// 	$url = preg_replace( '/http:\/\/localhost:8888/', 'http://elitedaily.com', $url, 1);

// 	print_r($thepermalink);
// 	echo '</pre>';
// }

// echo '<pre>';
// print_r($jackson->posts);
// echo '</pre>';


// $jackson = get_fb_comments();
// $jackson  = get_comment_meta( 789, "fb_comment", false );


// foreach ($jackson as $jack) {
// 	echo '<pre>';
// 	//$theobj = unserialize($jack->meta_value);
// 	//$theobj = json_encode($theobj);
// 	print_r($jack);
// 	echo '</pre>';
// }