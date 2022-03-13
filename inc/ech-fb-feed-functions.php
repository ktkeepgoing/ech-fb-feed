<?php
/**
 * Include file - Used in ECH Facebook Feed plugin
 * 
 * Contains: 
 * 
 * 
 * 
 * @link       https://www.vivideyecentre.com/
 * @since      1.0.0 *
 * @package    ECH_Facebook_Feed
 * 
 */

//require_once('load-template.php');


function register_ech_fb_feed_styles(){
	wp_register_style( 'fb_feed_custom_style', plugins_url('/assets/css/ech-fb-feed.css', __DIR__), false, '1.1.0', 'all');

	wp_register_script( 'fb_feed_custom_script', plugins_url('/assets/js/ech-fb-feed.js', __DIR__), array('jquery'), '1.0.0', true);
}

function enqueue_ech_fb_feed_styles() {
	wp_enqueue_style( 'fb_feed_custom_style' );
	wp_enqueue_script( 'fb_feed_custom_script');
}




$GLOBALS['perm_access_token'] = "EABDU3y7bu3oBAOZCvypuPpCcWf5tS8kGcRVTiRX2uGECJrzJyZCycu7VR5O8pgn6KiRZBjQqEd8wKPjH2S7QqOJWqZAi1bH5eIEUWUZCn1AJcmsJx6MYyZAFWZCZBZAgCY0dIl8tvlsDohI654ENJ9vOqIX5xFjKmQvIryD9z8vVUPlUMXR2wXGJ4";
$GLOBALS['fb_page_id'] = "100499611301662";




/*************************************************************
 * Plugin main function
 *************************************************************/
function ech_fb_feed_fun($atts){

	$paraArr = shortcode_atts( array(					
		'fbpage_id' => '100499611301662',
		'limit' => 12				
	), $atts );

	if ($paraArr['fbpage_id'] == null) {
		return "<h4>Error - fbpage_id not specified</h4>";
	}
	
	$limit = (int)$paraArr['limit'];


	$fb_graph_link = "https://graph.facebook.com/v12.0/".$paraArr['fbpage_id']."?date_format=U&fields=posts.limit(".$limit.")%7Bcreated_time%2Cmessage%2Cis_published%2Cattachments%7Bmedia%2Cmedia_type%7D%2Cpermalink_url%7D%2Cname%2Cpicture&access_token=".$GLOBALS['perm_access_token'];

	$get_fb_json = get_fb_json($fb_graph_link);
	
	$fb_json_arr = json_decode($get_fb_json, true);
	

	$output = '<div class="ech_fb_feed_container">';
		$output .= load_feed_template($fb_json_arr);
	$output .= '</div>'; //ech_fb_feed_container



	/***** Load More ****/
	$output .= '<div class="fb_feed_btn_container"><div id="fb_load_more_btn" data-url="'.get_admin_url(null, 'admin-ajax.php').'" data-fb-limit = "'.$limit.'" data-fb-feed-after="'.$fb_json_arr['posts']['paging']['cursors']['after'].'">更多貼文</div></div>';
	/***** (END)Load More ****/



	/**** Overlay ****/
	$output.= '<div class="fb_feed_overlay">';
		$output .= '<div class="fb_overlay_inner">';
				$output .= '<div class="fb_overlay_close"><i class="fas fa-times"></i></div>';
				$output .= '<div class="fb_video_container">';
					$output .= '<video controls>';
					$output .= '<source src="" type="video/mp4">';
					$output .= '</video>';
				$output .= '</div>'; //fb_video_container
			$output .= '</div>'; //fb_overlay_inner
	$output.= '</div>'; //fb_feed_overlay
	/**** (END)Overlay ****/

	return $output;
}





/*************************************************************
 * Replace "\n" returned from FB message data to <br>
 * in order to create line break
 *************************************************************/
function ech_content_parse($text) {
    // JSON requires new line characters be escaped
    $text = str_replace("\n", "<br>", $text);

    return $text;
}




/****************************************
 * Get FB Feed JSON from FB Server
 ****************************************/
function get_fb_json($fb_graph_link){
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $fb_graph_link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);

	return $result;
}




/****************************************
 * Load Feed Template
 ****************************************/
function load_feed_template($json_arr){
	$html = "";

	foreach($json_arr['posts']['data'] as $post ){

		if($post['is_published']){

			$post_date = date('d-m-Y H:i', $post['created_time']);

			$html .= '<div class="single_feed">';
				// Post Thumbnail
				foreach($post['attachments']['data'] as $attach ){
					$media_type = $attach['media_type'];
					//$html .=  $media_type.'<br>';
					if($media_type != "link") {
						if($media_type == "video") {
							$html .= '<div class="post_thumb video_thumb" data-fb-video="'.$attach['media']['source'].'"><div class="video_icon"><i class="fas fa-video"></i></div><img src="'.$attach['media']['image']['src'].'" /></div>';
						} else {
							$html .= '<div class="post_thumb"><img src="'.$attach['media']['image']['src'].'" /></div>';
						}
						
					}
				}

				// Profile info		
				$html .='<div class="fb_profile_container">';
					$html .= '<div class="profile_pic"><img src="'.$json_arr['picture']['data']['url'].'" /></div>';
					$html .= '<div class="profile_name">'.$json_arr['name'].'<div class="post_date">'.$post_date.'</div></div>';

				$html .= '</div>'; //fb_host

				//$html .= '<div class="post_date">'.$post_date.'</div>';

				$html .= '<div class="post_content">'.ech_content_parse($post['message']).'</div>';

				$html .= '<div class="feed_btns">';
					$html .= '<a href="'.$post['permalink_url'].'" target="_blank">在Facebook閱讀</a>'; 
					$html .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.$post['permalink_url'].'" target="_blank" rel="nofollow">分享</a>'; 
				$html .= '</div>'; 
			$html .= "</div>"; //single_feed

		} // if $post['is_published']
	
	} //foreach($json_arr['posts']['data'] as $post )

	return $html;	
}



/****************************************
 * Load more Button
 * used in "load more button" - AJAX
 ****************************************/
function fb_load_more_feed(){
	$fb_after = $_POST['fb_after'];
	$fb_limit = $_POST['fb_limit'];


	$fb_graph_link = "https://graph.facebook.com/v12.0/100499611301662/posts?access_token=EABDU3y7bu3oBAOZCvypuPpCcWf5tS8kGcRVTiRX2uGECJrzJyZCycu7VR5O8pgn6KiRZBjQqEd8wKPjH2S7QqOJWqZAi1bH5eIEUWUZCn1AJcmsJx6MYyZAFWZCZBZAgCY0dIl8tvlsDohI654ENJ9vOqIX5xFjKmQvIryD9z8vVUPlUMXR2wXGJ4&date_format=U&pretty=0&fields=created_time%2Cmessage%2Cis_published%2Cattachments%7Bmedia%2Cmedia_type%7D%2Cpermalink_url&limit=".$fb_limit."&after=".$fb_after;


	$get_fb_json = get_fb_json($fb_graph_link);
	
	$fb_json_arr = json_decode($get_fb_json, true);

	$html = load_more_feed_template($fb_json_arr);

	$next_after = $fb_json_arr['paging']['cursors']['after'];
	
	echo json_encode(array("html"=>$html, "fb_after"=>$next_after), JSON_UNESCAPED_SLASHES);

	
	wp_die();
}



/****************************************
 * Load more feed template
 * used in function fb_load_more_feed
 ****************************************/
function load_more_feed_template($json_arr){
	$html = "";

	$get_fb_name_pic_arr = get_fb_name_pic();


	
	foreach($json_arr['data'] as $post ){

		if($post['is_published']){

			$post_date = date('d-m-Y H:i', $post['created_time']);

			$html .= '<div class="single_feed">';
				// Post Thumbnail
				foreach($post['attachments']['data'] as $attach ){
					$media_type = $attach['media_type'];
					//$html .=  $media_type.'<br>';
					if($media_type != "link") {
						if($media_type == "video") {
							$html .= '<div class="post_thumb video_thumb" data-fb-video="'.$attach['media']['source'].'"><div class="video_icon"><i class="fas fa-video"></i></div><img src="'.$attach['media']['image']['src'].'" /></div>';
						} else {
							$html .= '<div class="post_thumb"><img src="'.$attach['media']['image']['src'].'" /></div>';
						}
						
					}
				}

				// Profile info
				$html .='<div class="fb_profile_container">';
					$html .= '<div class="profile_pic"><img src="'.$get_fb_name_pic_arr['picture']['data']['url'] .'" /></div>';
					$html .= '<div class="profile_name">'.$get_fb_name_pic_arr['name'].'<div class="post_date">'.$post_date.'</div></div>';

				$html .= '</div>'; //fb_host



				$html .= '<div class="post_content">'.ech_content_parse($post['message']).'</div>';

				$html .= '<div class="feed_btns">';
					$html .= '<a href="'.$post['permalink_url'].'" target="_blank">在Facebook閱讀</a>'; 
					$html .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.$post['permalink_url'].'" target="_blank" rel="nofollow">分享</a>'; 
				$html .= '</div>'; 
			$html .= "</div>"; //single_feed

		} // if $post['is_published']
	
	} //foreach($json_arr['posts']['data'] as $post )

	return $html;	
}



/****************************************
 * Get FB Name and Porfile Picture
 ****************************************/
function get_fb_name_pic(){
	$get_fb_json = "https://graph.facebook.com/v12.0/".$GLOBALS['fb_page_id']."/?fields=picture%2Cname&access_token=".$GLOBALS['perm_access_token'];

	$result = get_fb_json($get_fb_json);
	$fb_json_arr = json_decode($result, true);

	return $fb_json_arr;
	
}

