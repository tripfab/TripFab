/*
 * jQuery Image Blur plugin 0.1
 *
 * Copyright (c) 2010 Dharmveer Motyar
 * http://motyar.blogspot.com
 * http://motyar.com
 *
 * $Id$
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Creates a blur effect over images.
 *
 * @example $.imageblur();
 *
 * @name imageblur
 * @type jQuery
 * @cat Plugins/imageblur
 */
(function ($) {
/*
	 * Plugin defaults 
	 */
    var defaults = {
        blurer: ''
    };

    $.imageblur = function (settings) {
        $.imageblur.settings = $.extend({}, defaults, settings);
        $('img#blur').wrap('<div class="image_holder"></div>');

            $.imageblur.blur();
            return;
	}

     /*
	 * Public Functions
	 */
        

	$.imageblur.create = function () {
           $(".image_holder").each(function () {
            var blurer, margin_left = '';
            if ($.browser.webkit || $.browser.opera) {
                margin_left = 0;
            }
            else if ($.browser.msie || $.browser.mozilla) {
                if (jQuery.browser.version == '8.0') {
                    margin_left = 0;
                }
                else {
                    margin_left = $(".image_holder").children().width() / 2;
                }
            }

            $(".image_holder").width($(".image_holder").children().width());
            $.imageblur.settings.blurer = '<div class="blurer"><span style="position:absolute; margin-left:-' + margin_left + 'px; margin-top:-' + $(".image_holder").children().height() + 'px;filter:alpha(opacity=50);-khtml-opacity: 0.5; opacity:0.5;" >';
            $.imageblur.settings.blurer+= '<img src=' + $(".image_holder").children().attr('src') + ' width="' + $(this).children().width() + '" height="' + $(".image_holder").children().height() + '" vspace="2" hspace="2"/></span></div>';
            
           });
        };

        $.imageblur.blur = function () {
			$.imageblur.create();
            $(".image_holder").append($.imageblur.settings.blurer);
        };
        


        })(jQuery);