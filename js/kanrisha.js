var kanrisha_method = {
	
	showTooltip: function (x, y, contents) {
		$('<div class="charts_tooltip">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5
		}).appendTo("body").fadeIn('fast');
	},

}

var km = kanrisha_method;

$(function () {
	
/* Opera Fix ========================================= */

	if ( $.browser['opera'] ) {
		$("aside").addClass('onlyOpera');
	}
	
	$(".confDialogBtn").live("click", function() {
		$(".confDialog").dialog("open");
		return false;
	});
	
/* Tables ============================================ */
	// Set the DataTables
	
	$(".datatable").dataTable({
		"bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
        	$(".dtShowPer select").first().uniform();
        	$(".dtFilter input").addClass("simple_field").css({
        		"width": "auto",
        		"margin-left": "15px"
        	});
        }
    });

    $(".data_table").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            //$(".dtShowPer select").uniform();
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });

    $(".dDatatable").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            $(".dtShowPer select").uniform();
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });

    $(".subTable").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            $(".dtTables").css({
                "overflow-x": "hidden"
            });
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });

    $(".buyTable").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            $(".dtTables").css({
                "overflow-x": "hidden"
            });
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });

    $(".userTable").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            $(".dtShowPer select").uniform();
            $(".dtTables").css({
                "overflow-x": "hidden"
            });
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });
	
	$(".videoTable").dataTable({
        "bSort": false,
        "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
        "oLanguage": {
            "sLengthMenu": "Show entries _MENU_"
        },
        "sPaginationType": "full_numbers",
        "fnInitComplete": function(){
            $(".dtShowPer select").uniform();
            $(".dtTables").css({
                "overflow-x": "hidden"
            });
            $(".dtFilter input").addClass("simple_field").css({
                "width": "auto",
                "margin-left": "15px"
            });
        }
    });

	// Table with Tabs
	$("#table_wTabs").tabs();
	
/* Forms ============================================= */
	$(".simple_form").uniform(); // Style The Checkbox and Radio
	$(".elastic").elastic();
	$(".twMaxChars").supertextarea({
	   	maxl: 140
	});

/* Spinner =========================================== */
	$(".spinner1").spinner();
	$(".spinner2").spinner({
		min: 0,
		max: 30,
	});
	$(".spinner3").spinner({
		min: 0,
		prefix: '$',
	});
	$(".spinner4").spinner().spinner("disable");
	$(".spinner5").spinner({'step':5});

/* ToolTip & ColorPicker & DatePicker ================ */
	$(".tooltip").tipsy({trigger: 'focus', gravity: 's', fade: true});
	$(".buttonTooltip").tipsy({gravity: 's'});
	$("#btTop").tipsy({gravity: 's'});
	$("#btTopF").tipsy({gravity: 's',fade: true});
	$("#btTopD").tipsy({gravity: 's',delayOut: 2000});
	$("#btLeft").tipsy({gravity: 'e'});
	$("#btRight").tipsy({gravity: 'w'});
	$("#btBottom").tipsy({gravity: 'n'});

	$(".fwColorpicker").ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});	

	$(".pick_date").datepicker();
	
/* Wysiwyg =========================================== */
	
	$(".wysiwyg").cleditor({width:"100%", height:"100%"});

/* Tab Toggle ======================================== */
	
	$(".cwhToggle").click(function(){
		// Get Height
		var wC = $(this).parents().eq(0).find('.widget_contents');
		var wH = $(this).find('.widget_header_title');
		var h = wC.height();

		if (h == 0) {
			wH.addClass("i_16_downT").removeClass("i_16_cHorizontal");
			wC.css('height','auto').removeClass('noPadding');
		}else{
			wH.addClass("i_16_cHorizontal").removeClass("i_16_downT");
			wC.css('height','0').addClass('noPadding');
		}
	})

/* Dialog ============================================ */
	
	$.fx.speeds._default = 400; // Adjust the dialog animation speed
	
	$(".bDialog").dialog({
		autoOpen: false,
		show: "fadeIn",
		modal: true,
	});

	$(".dConf").dialog({
		autoOpen: false,
		show: "fadeIn",
		modal: true,
		buttons: {
			"Yeah!": function() {
				$( this ).dialog( "close" );
			},
			"Never": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$(".bdC").live("click", function(){ /* change click to live */
		$(".bDialog").dialog( "open" );
		return false;
	});

	$(".bdcC").live("click", function(){ /* change click to live */
		$(".dConf").dialog( "open" );
		return false;
	});

/* LightBox ========================================== */
	
	$('.lPreview a.lightbox').colorbox({rel:'gal'});

/* Drop Menu ========================================= */
	
	$(".drop_menu").parent().on("click", function(){
		var status = $(this).find(".drop_menu").css("display");
		if (status == "block"){
			$(this).find(".drop_menu").css("display", "none");
		}else{
			$(this).find(".drop_menu").css("display", "block");
		}
	});

	$(".top_tooltip").parent().on("hover", function(){
		var status = $(this).find(".top_tooltip").css("display");
		if (status == "block"){
			$(this).find(".top_tooltip").css("display", "none");
		}else{
			$(this).find(".top_tooltip").css("display", "block");
		}
	});

/* Inline Dialog ===================================== */

	$(".iDialog").on("click", function(){
		$(this).fadeOut("slow").promise().done(function(){
			$(this).parent().remove();
		});
	});
});