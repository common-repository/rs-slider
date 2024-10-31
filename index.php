<?php
/**
 * @package rs_slider
 * @version 1.0
 */
/*
Plugin Name: Renklisayfa Slider
Plugin URI: http://renklisayfa.com
Description: Renklisayfa.com Slider Plugini.
Author: Renklisayfa.com
Version: 1.0
Author URI: http://renklisayfa.com
*/
//<!--rs_slider-->
define( 'RS_SLIDER_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'RS_SLIDER_PLUGIN_URL', plugin_dir_url(__FILE__));
add_filter('publish_post','rs_slider'); 
add_filter('edit_post','rs_slider');
add_action('wp_head', 'set_css');
add_action('wp_enqueue_scripts', 'set_js');

function set_js() {
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'rs_functions', RS_SLIDER_PLUGIN_URL.'js/functions.js');
    wp_enqueue_script( 'rs_functions' );
}    
 
function set_css(){
	global $wp_query;
	$select = "SELECT guid,post_title FROM wp_posts WHERE post_parent = '".$wp_query->post->ID."' AND post_type='attachment' AND(post_mime_type = 'image/png' OR post_mime_type='image/gif' OR post_mime_type='image/jpeg') ORDER BY menu_order ASC,ID DESC LIMIT 5";
	$sorgu = mysql_query($select);
	$toplam_imaj = mysql_num_rows($sorgu);
	if( $toplam_imaj > 2 ){
		echo '<link href="'.RS_SLIDER_PLUGIN_URL.'css/rs_slider.css" rel="stylesheet" type="text/css" />';
		$select = "SELECT COUNT(*) AS SAYI FROM wp_posts WHERE post_parent = '".$wp_query->post->ID."' AND post_type='attachment' AND(post_mime_type = 'image/png' OR post_mime_type='image/gif' OR post_mime_type='image/jpeg')";
	$sorgu = mysql_query($select);
	$al = mysql_fetch_assoc($sorgu);
	$toplam  = $al['SAYI'];
	if($toplam > 2){
		if($toplam > 5){
			$toplam = 5;
		}
		$js = '<script type="text/javascript"> jQuery(document).ready(function() {
	if(! jQuery(\'#rs_akindil_galeri\').data(\'key\')){
		jQuery(\'#rs_akindil_galeri\').data(\'key\',1);
		jQuery(\'#resim_1\').css(\'display\',\'block\');
	}
    %s
	oto_hareket('.$toplam.');
});</script>';
		$js_uretec = ' jQuery(\'#alt_yazi_%s\').click(function(){fkapat(%s,'.$toplam.');});';
		$son_js = "";
		$css_birimi = round(880 / $toplam);
		$css = ' #alt_yazi_%s {
	position: absolute;
	left: %spx;
	top: 0;
}';
		$genel_css = sprintf(' .alt_yazi_genel{
	width: %spx;
	height: 30px;
	cursor: pointer;
	z-index: 10;
	background-color: #222222;
	font-size: 18px;
	color: #FFF;
	font-family: Arial, Helvetica, sans-serif;
	padding-top: 10px;
	padding-left: 10px;
}
.alt_yazi_genel_secili{
	width: %spx;
	height: 30px;
	cursor: pointer;
	z-index: 10;
	background-color: #FEC005;
	font-size: 18px;
	color: #FFF;
	font-family: Arial, Helvetica, sans-serif;
	padding-top: 10px;
	padding-left: 10px;
}
.alt_yazi_genel:hover{
	background-color: #FEC005;
	color: #222222;
}
.alt_yazi_genel_secili:hover{
	background-color:#FEC005;
	color: #222222;
}',$css_birimi-10,$css_birimi-10);		
		$son_css = "";
		for($i= 0;$i<$toplam;$i++){
			$son_css .= sprintf($css,$i+1,$css_birimi*($i));
			$son_js .= sprintf($js_uretec,$i+1,$i+1);
		}
		echo sprintf($js,$son_js);
		echo '<style type="text/css">'.$son_css.$genel_css.'</style>';
	}else{
		return NULL;
	}
	}else{
		return NULL;
	}
}

function rs_slider($post_id){
	$ic = "SELECT post_content FROM wp_posts WHERE ID='$post_id' LIMIT 1";
	$s = mysql_query($ic);
	$al = mysql_fetch_assoc($s);
	$content = $al['post_content'];
	$select = "SELECT guid,post_title FROM wp_posts WHERE post_parent = '$post_id' AND post_type='attachment' AND(post_mime_type = 'image/png' OR post_mime_type='image/gif' OR post_mime_type='image/jpeg') ORDER BY menu_order ASC,ID DESC LIMIT 5";
	$sorgu = mysql_query($select);
	$toplam = mysql_num_rows($sorgu);
	if($toplam > 2){
		$cikti = '<div id="rs_akindil_galeri">
  <div id="resimler">%s </div>
  <div id="rs_akindil_galeri_2">%s</div>
</div>';
		$resim_div ='<div class="resim" id="resim_%s"><img src="%s" width="880" height="340" /></div>';
		$alt_yazi_div = '<div class="alt_yazi_genel" id="alt_yazi_%s">%s</div>';
		$resimler = "";
		$yazilar = "";
		$c=0;
		while($al = mysql_fetch_assoc($sorgu)){
			$c++;
			$resimler .= sprintf($resim_div,$c,$al['guid']);
			$yazilar .= sprintf($alt_yazi_div,$c,$al['post_title']);			
		}		
		$content = 	mysql_real_escape_string(preg_replace("/<!--rs_slider-->/",sprintf($cikti,$resimler,$yazilar),$content));
		$update = "UPDATE wp_posts SET post_content='$content' WHERE ID ='$post_id' LIMIT 1";
		mysql_query($update);
		return true;
	}
}
?>