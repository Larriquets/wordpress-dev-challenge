<?php
if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

/////------------ SHORTCODE ----------  ////////
function post_link_shortcode( $atts ) {
    global $post;
	// Attributes
	$atts = shortcode_atts(
		array(
			'post_id' => '',
		),
		$atts,
		'mc_citations'
	);
	if ( isset( $atts['post_id']  ) ) {
       if ( empty($atts['post_id'])){
         $atts['post_id'] = $post->ID;
       }
       return htmlspecialchars_decode(get_post_meta($atts['post_id'], 'ads_excerpt_content' , true ));
	}else{
       return $post->ID;
    }
}
add_shortcode( 'mc_citations', 'post_link_shortcode' );

