<?php

//// ---- Add MetaBox : Cron Job ---- ////
add_action( 'add_meta_boxes', 'mc_meta_boxes_cron_job' );
function mc_meta_boxes_cron_job() {
    add_meta_box( 'mc-meta-box', __( 'Verification of links in the post_content - Cron Job', 'cyb_textdomain' ), 'mc_meta_box_callback', 'post' );
}

function mc_meta_box_callback( $post ) { 
    wp_nonce_field( 'mc_meta_box', 'mc_meta_box_noncename' );
    $url_error = get_post_meta( $post->ID, 'url_error', true  );
    $post_checked = get_post_meta($post->ID, 'id_cron_post_checked', true);
    if(empty($post_checked)){
        $checked = '';
        $post_checked = 'unchecked';
    }else{
        $checked = 'checked';
    }
    if($post_checked == 'unchecked' && !empty($post_checked)){ $checked = ''; }
    ?>
    <p>Post checked by Cron : <?php // echo $checked;?>
        <input type="checkbox" name="post_meta_post_checked" id="id_cron_post_checked" value="<?php echo $post_checked;  ?>" <?php echo $checked;  ?> />
    </p> 
    <?php
    $display_error = 'none';
    $post_checked_error = get_post_meta($post->ID, 'id_cron_post_links_error', true);
    if(empty($post_checked_error)){
        $checked_error = ''; 
    }else{
        $checked_error = 'checked';
        $display_error = 'block';
    }
    if($post_checked_error == 'unchecked' && !empty($post_checked_error)){     
        $display_error = 'none';
        $checked_error = ''; 
    }
    ?>
    <p>Links Errors : <?php // echo $post_checked_error;?>
        <input type="checkbox" name="mc_meta_links_error" id="id_cron_post_links_error" value="1" <?php echo $checked_error;  ?> />
    </p>
    <div>
        <input  name="url_error" size="100" id="url_error" type="hidden" value="<?php echo   json_encode($url_error); ?>">
        <textarea rows="5" cols="40" style="display:<?php echo $display_error;?>" ><?php print_r($url_error);?></textarea>
    </div>
    <?php 
}

add_action( 'save_post', 'mc_save_custom_fields', 10, 2 );
function mc_save_custom_fields( $post_id, $post ) {
    
    if ( ! isset( $_POST['mc_meta_box_noncename'] ) || ! wp_verify_nonce( $_POST['mc_meta_box_noncename'], 'mc_meta_box' ) ) {
        return;
    }        
    if(array_key_exists('post_meta_post_checked', $_POST)) {
        update_post_meta( 
            $post_id, 
            'id_cron_post_checked', 
            sanitize_text_field($_POST['post_meta_post_checked'])
        );
    } else {
        update_post_meta( 
            $post_id, 
            'id_cron_post_checked', 'unchecked');
    }
    if(array_key_exists('mc_meta_links_error', $_POST)) {
        update_post_meta( 
            $post_id, 
            'id_cron_post_links_error', 
            sanitize_text_field($_POST['mc_meta_links_error'])
        );
    } else {
        update_post_meta( 
            $post_id, 
            'id_cron_post_links_error', 'unchecked');
    }
}



//// ------ Add MetaBox : Citations Shortcode ----- ////
add_action( 'add_meta_boxes', 'mc_meta_boxes_citations' );              
function mc_meta_boxes_citations() {   
    add_meta_box('adsexcerptid', 'Citations - This content is displayed using the shortcode : [mc_citations post_id=""]', 'mc_output_function');}

function mc_output_function( $post ) {
    $settings = array(
        'quicktags'     => array( 'buttons' => 'em,strong,link' ),
        'textarea_name' => 'ads-excerpt',
        'quicktags'     => true,
        'tinymce'       => true
        );
    wp_editor( htmlspecialchars_decode(get_post_meta($_GET['post'], 'ads_excerpt_content' , true )), 'mettaabox_ID_stylee', $settings );
}

function save_mc_postdata( $post_id ) {                   
    if (!empty($_POST['ads-excerpt']))
        {
        update_post_meta($post_id, 'ads_excerpt_content', htmlspecialchars($_POST['ads-excerpt']) );
        }
}
add_action( 'save_post', 'save_mc_postdata' );  