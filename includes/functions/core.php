<?php
if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

//// ---- Check links in post_content ---- ////
function mc_check_links_post($args){

    $query = new WP_Query( $args );    
    if ( $query->have_posts() ) {  
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $res = mc_find_url_content( get_the_content() );
            $response = json_encode($res);
            if (str_contains($response, 'empty')) {
                update_post_meta( $post_id, 'url_error', 'No links found');
                update_post_meta( $post_id, 'id_cron_post_links_error', '0');
                update_post_meta( $post_id, 'id_cron_post_checked', '0');
            }else{             
                if(empty($res)){
                    update_post_meta( $post_id, 'id_cron_post_links_error', '0');
                }else{
                    update_post_meta( $post_id, 'id_cron_post_links_error', '1');
                }
                update_post_meta( $post_id, 'url_error', $res);
                update_post_meta( $post_id, 'id_cron_post_checked', '1');       
            }
        }     
    } 
    wp_reset_postdata();
}

function mc_find_url_content( $post_content ) {
    if(preg_match_all('/<a\s+href=["\']([^"\']+)["\']/i', $post_content, $links, PREG_PATTERN_ORDER)){
        $all_hrefs = $links[1];
        if(empty($all_hrefs)){
            return json_encode('empty');
        }else{          
            $responses = array();
            $index = 0;
            foreach ($all_hrefs as $key => $href) {         
               $href_state =  mc_test_https_protocol($href);       
               if($href_state != 'https-ok'){
                  $responses[$index]['url'] = $href;
                  $responses[$index]['state'] = $href_state;
                  $index++;
               }
            }
            return $responses;
        }
    }     
}

//// ---- Link validation ---- ////
function mc_test_https_protocol( $url_ref){
    $url = parse_url($url_ref );
    if($url['scheme'] == 'https'){
        $space = preg_match('/\s/',$url['host']);
        if($space ){ return 'URL malformed';}
        $response =  mc_curl_request($url['host']);
        if($response == 404){return json_encode('404');}
        if($response == 200 || $response == 301){
            return 'https-ok';
        }else{
            return json_encode($response);
        }
    }else{
        if($url['scheme'] == 'http'){
            return 'Insecure protocol http';
        }else{
            return 'Protocol is missing';
        }
    }
}

function mc_curl_request ($url){
    $ch = curl_init($url);   
    curl_setopt($ch, CURLOPT_HEADER, true);    
    curl_setopt($ch, CURLOPT_NOBODY, true);   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT,30);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);  
    $output = curl_exec($ch);
    if (empty($output)) {  
        if (str_contains(curl_error($ch), 'Protocol not supported')) {
            return 'Protocol not supported';
        }
        if (str_contains(curl_error($ch), 'Could not resolve host:')) {
            return 'Could not resolve host:';//  echo "ERROR 40X";
        }else{
            return curl_error($ch) ;
        }
        curl_close($ch); // close cURL handler
    }else{
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;   
    }
}