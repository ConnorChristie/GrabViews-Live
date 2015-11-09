(function() {

    var sb_layout = [],
        sb_preset = [],
        sb_gfonts = '',
		sb_prefix = 'cubic_';

    // html
    sb_layout[0] = [ 'General', {
        html_bg_color        : 'color',
        html_bg_pattern      : [
            'background',
            [
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/diagonal_1.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/diagonal_2.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/diagonal_3.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/dots_1.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/hash_1.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/horizontal_1.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/noise_1.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/noise_2.png)',
                'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/vertical_1.png)'
            ]
        ],
        main_color			: 'color',
		main_link_color		: 'color'
    }];


    // Default settings
    sb_preset['default'] = {
        html_bg_color   	: '#F8F8F8',
        html_bg_pattern 	: 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/noise_1.png)',
		
		main_color      	: '#64C1EC',
		main_link_color     : '#6B6B6B'
    }

    // Preset 1
    sb_preset[0] = {
        name                : 'Cliff Edge',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/cliff2.jpg) 50% 50% no-repeat fixed',
		
		main_color      	: '#484848',
		main_link_color		: '#484848'
    }
	
    // Preset 2
    sb_preset[1] = {
        name                : 'Majestic',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/cliff.jpg) 50% 50% no-repeat fixed',
 
		main_color      	: '#2a3479',
		main_link_color		: '#2a3479'
    }
	
    // Preset 3
    sb_preset[2] = {
        name                : 'Forest',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/forest.jpg) 50% 50% no-repeat fixed',

		main_color      	: '#673e28',
		main_link_color		: '#673e28'
    }
	
    // Preset 4
    sb_preset[3] = {
        name                : 'Dungeon',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/dungeon.jpg) 50% 50% no-repeat fixed',
 
		main_color      	: '#3a4a26',
		main_link_color		: '#3a4a26'
    }
	
    // Preset 5
    sb_preset[4] = {
        name                : 'Nether',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/nether.jpg) 50% 50% no-repeat fixed',
 
		main_color      	: '#612928',
		main_link_color		: '#612928'
    }
	
    // Preset 6
    sb_preset[5] = {
        name                : 'Tundra',

        html_bg_color       : '#000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/minecraft/tundra.jpg) 50% 50% no-repeat fixed',
 
		main_color      	: '#7b91b6',
		main_link_color		: '#7b91b6'
    }

    window.sb_settings = [ sb_layout, sb_preset, sb_gfonts, sb_prefix ];

})();

jQuery(document).ready(function(){ 
    jQuery("html").css('background-size','cover'); 
    jQuery("html").css('background-attachment','fixed'); 
});