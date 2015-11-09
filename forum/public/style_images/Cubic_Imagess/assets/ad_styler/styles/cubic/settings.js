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
        name                : 'Ocean',

        html_bg_color       : '#0a5669',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/noise_2.png)',
		
		main_color      	: '#165c8a',
		main_link_color		: '#1aabb0'
    }
	
    // Preset 2
    sb_preset[1] = {
        name                : 'Forest',

        html_bg_color       : '#425230',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/noise_1.png)',
 
		main_color      	: '#b0601a',
		main_link_color		: '#81b01a'
    }
	
    // Preset 3
    sb_preset[2] = {
        name                : 'Love',

        html_bg_color       : '#9c0000',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/dots_1.png)',

		main_color      	: '#c11111',
		main_link_color		: '#ed2323'
    }
	
    // Preset 4
    sb_preset[3] = {
        name                : 'Velvet',

        html_bg_color       : '#9e1d4c',
        html_bg_pattern     : 'url(public/style_images/cubic/assets/ad_styler/imgs/bgs/vertical_1.png)',
 
		main_color      	: '#333333',
		main_link_color		: '#db5582'
    }

    window.sb_settings = [ sb_layout, sb_preset, sb_gfonts, sb_prefix ];

})();