<?php
    // set content type to css
	header('Content-type: text/css');

	// get values
    function get_value( $name ) {
        $name = $_GET['prefix'] . $name;
        echo ( isset( $_GET[$name] ) ) ? $_GET[$name] : '';
    }
?>
/* <style> - for syntax highlighting */

/* disable CSS3 transitions on styler elements */
#styleBar ,#styleBar * {
	-webkit-transition:0s;
	-moz-transition:0s;
	-o-transition:0s;
	-ms-transition:0s;
	transition:0s;
}
.pagination .pages li.active, .mcolor,#board_stats .value {background-color:<?php get_value( 'main_color' ); ?>}
/* html dynamic css */
html {
	background:<?php get_value( 'html_bg_color' ); ?> <?php get_value( 'html_bg_pattern' ); ?>;
	height: 100%;
}

/* apply select linkcolor */
a {
color:<?php get_value( 'main_link_color' ); ?>;
}
/* apply sleceted main color to some elements */
a:hover, .post_body a{
color:<?php get_value( 'main_color' ); ?>;
}

/* apply the selected main color to elements */
.post_controls > li > a,.post_controls .ipsLikeButton, #community_app_menu > li.active > a, .topic_buttons li a:hover, .ipsButton:hover, .maintitle, .cke_dialog_title,#community_app_menu > li > a:hover, #community_app_menu > li > a.menu_active {
	background-color:<?php get_value( 'main_color' ); ?> !important;
	color:white;
}
h1,h2,h3,h4 {
	font-family: <?php get_value( 'html_font' ); ?>, sans-serif !important;
}
.pageContent a:link,.pageContent a:active,.pageContent a:visited {
	color:<?php get_value( 'main_link_color' ); ?>;
}
.topic_buttons li.non_button a {background:none !important;}