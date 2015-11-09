/** * Cookie plugin * * Copyright (c) 2006 Klaus Hartl (stilbuero.de) * Dual licensed under the MIT and GPL licenses: * http://www.opensource.org/licenses/mit-license.php * http://www.gnu.org/licenses/gpl.html * */jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options=$.extend({},options);options.expires=-1;}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}expires='; expires='+date.toUTCString();}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}return cookieValue;}};

jQuery.noConflict();
jQuery(document).ready(function(){

/* Color manipulation Script */
function getRGB(color) {
    var result;
    if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color)) return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];
    if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color)) return [parseFloat(result[1]) * 2.55, parseFloat(result[2]) * 2.55, parseFloat(result[3]) * 2.55];
    if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color)) return [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)];
    if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color)) return [parseInt(result[1] + result[1], 16), parseInt(result[2] + result[2], 16), parseInt(result[3] + result[3], 16)];
}
function darkenColor(color) {
    rgb = getRGB(color);
    for(var i = 0; i < rgb.length; i++){
        rgb[i] = Math.max(0, rgb[i] - 40);
    }
    newColor = 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')';
    return newColor
};

if ( document.width < 1000 ) { 
   var user_nav = jQuery('#user_navigation'); 
   user_nav.css('marginRight','36px'); 
}

function lightenColor(color) {
    rgb = getRGB(color);
    for(var i = 0; i < rgb.length; i++){
        rgb[i] = Math.max(0, rgb[i] + 30);
    }
    newColor = 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')';
return newColor
};

mainColor = jQuery('.mcolor').css('background-color');
mainColor_light = lightenColor(mainColor);
mainColor_dark = darkenColor(mainColor);
mainColor_darker = darkenColor(mainColor_dark);

jQuery('.ipsSideMenu ul li.active a').css('background-color',mainColor_dark);

//Change topic view based on last user selection
var currentTopicView = jQuery.cookie("current_topicview");
if(currentTopicView == 'compact') {
jQuery('#viewSwitch_compact').addClass('viewSwitch_active');
jQuery('#ips_Posts').addClass('topicView_compact');
jQuery('#viewSwitch_normal').removeClass('viewSwitch_active');
}

/* Topic view switching buttons */
jQuery('#viewSwitch_normal').click(function(){
jQuery(this).addClass('viewSwitch_active');
jQuery('#ips_Posts').addClass('topicView_normal');
jQuery('#viewSwitch_compact').removeClass('viewSwitch_active');
jQuery('#ips_Posts').removeClass('topicView_compact');
jQuery.cookie("current_topicview", "normal");
return false;
});

jQuery('#viewSwitch_compact').click(function(){
jQuery(this).addClass('viewSwitch_active');
jQuery('#ips_Posts').addClass('topicView_compact');
jQuery('#viewSwitch_normal').removeClass('viewSwitch_active');
jQuery('#ips_Posts').removeClass('topicView_normal');
jQuery.cookie("current_topicview", "compact");
return false;
});

/* Search button */
jQuery('.aud_search').click(function(){
	jQuery(this).hide();
	jQuery('.aud_search_box').show();
	return false;
});
jQuery('body').bind('click',function(e){
	if(
     jQuery('.aud_search_box').css('display','block') && !jQuery(e.target).is('.aud_search_box, .aud_search_box *') 
	) {
		jQuery('.aud_search_box').hide();
		jQuery('.aud_search').show();
	}
});
/* postbit buttons hover darken */
jQuery('head').append("<style type='text/css'>.post_controls li a:hover {background-color:" + mainColor_dark + "!important }</style>");

/* scroll to top animation */
jQuery('a[href=#top], a[href=#ipboard_body]').click(function(){
		jQuery('html, body').animate({scrollTop:0}, 900);
        return false;
	});

/* extra padding on links with norification */
jQuery('#user_navigation a:has(.ipsHasNotifications)').each(function(){
    jQuery(this).addClass('link_hasNotifications');
});

/* remove useless title tooltips */
jQuery('#primary_nav a').each(function(){
jQuery(this).attr('title','');
});
jQuery('.copyright_row > a').attr('title','');

/* disable default behaviour of ckeditor resize handle */
jQuery('.cke_resizer').click(function(e) {
    e.preventDefault();
});

});