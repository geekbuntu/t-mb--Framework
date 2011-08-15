// ---------------------------------------------------------
// Slideshow Navigation
// ---------------------------------------------------------

function paginate(idx, slide){
    return '<li><a href="" title="">#</a></li>';
}

// ---------------------------------------------------------
// jQuery
// ---------------------------------------------------------

jQuery.noConflict()(function($){

    $(document).ready(function() {

        // ---------------------------------------------------------
        // Main Menu
        // ---------------------------------------------------------

        $('#menu-wrapper .menu').superfish();

        // ---------------------------------------------------------
        // Contact Form
        // ---------------------------------------------------------

        //Activate $ form validation
        $("#contactform").validate();

        // ---------------------------------------------------------
		// Comments Form
		// ---------------------------------------------------------
		
		$("#commentform .comment-form-author input").addClass('required');
		$("#commentform .comment-form-email input").addClass('required');
		$("#commentform .comment-form-comment textarea").addClass('required');
		$("#commentform").validate();

        // ---------------------------------------------------------
        // Portfolio Thumbnail
        // ---------------------------------------------------------

        $('.portfolio-box a.thumb').each(function(){
            if(this.href.match(/\.(jpe?g|png|bmp|gif|tiff?)$/i)){
                $(this).addClass('image');
            } else {
                $(this).addClass('video');
            }
        });

        $('.portfolio-box a').hover(function() {

            //Show darkenned hover over thumbnail image
            $(this).find('img').stop(true, true).animate({opacity:0.5},400);

        }, function() {

            //Hide darkenned hover over thumbnail image
            $(this).find('img').stop(true, true).animate({opacity:1},400);

        });

        // ---------------------------------------------------------
        // Wordpress Gallery Lightbox Integration
        // ---------------------------------------------------------

        $('.gallery-item a').each(function(){
            if(this.href.match(/\.(jpe?g|png|bmp|gif|tiff?)$/i)){
                $(this).attr('rel','lightbox[gallery]');
            }
        });

        // ---------------------------------------------------------
        // Image Buttons
        // ---------------------------------------------------------

        $('.image-button').css({opacity:.75});

        $('.image-button').hover(function() {

            $(this).stop(true, true).animate({opacity:1},100);

        }, function() {

            $(this).stop(true, true).animate({opacity:.75},100);

        });

        // ---------------------------------------------------------
        // Innititate Pretty Photo
        // ---------------------------------------------------------

        $("a[rel^='lightbox']").prettyPhoto({
                theme: 'light_square', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
                show_title: false
        });

        // ---------------------------------------------------------
        // Tabs
        // ---------------------------------------------------------

        $(".themeblvd-tabs").each(function(){

            $(this).find(".tab").hide();
            $(this).find(".tab-menu li:first a").addClass("active").show();
            $(this).find(".tab:first").show();

        });
		
        $(".themeblvd-tabs").each(function(){
			
            $(this).find(".tab-menu a").click(function() {

                $(this).parent().parent().find("a").removeClass("active");
                $(this).addClass("active");
                $(this).parent().parent().parent().parent().find(".tab").hide();
                var activeTab = $(this).attr("href");
                $(activeTab).fadeIn();
                return false;

            });

        });

        // ---------------------------------------------------------
        // Toggle
        // ---------------------------------------------------------

        $(".themeblvd-toggle").each(function(){

            $(this).find(".box").hide();

        });

        $(".themeblvd-toggle").each(function(){

            $(this).find(".trigger").click(function() {

                $(this).toggleClass("active").next().stop(true, true).slideToggle("slow");

                return false;

            });

        });

    }); // End DOM ready

}); //End noConflict()

//Twitter Widget
(function(jQuery) {
	/*
		jquery.twitter.js v1.5
		Last updated: 08 July 2009

		Created by Damien du Toit
		http://coda.co.za/blog/2008/10/26/jquery-plugin-for-twitter

		Licensed under a Creative Commons Attribution-Non-Commercial 3.0 Unported License
		http://creativecommons.org/licenses/by-nc/3.0/
	*/

	jQuery.fn.getTwitter = function(options) {

		jQuery.fn.getTwitter.defaults = {
			userName: null,
			numTweets: 5,
			loaderText: "Loading tweets...",
			slideIn: true,
			slideDuration: 750,
			showHeading: true,
			headingText: "Latest Tweets",
			showProfileLink: true,
			showTimestamp: true
		};

		var o = jQuery.extend({}, jQuery.fn.getTwitter.defaults, options);

		return this.each(function() {
			var c = jQuery(this);

			// hide container element, remove alternative content, and add class
			c.hide().empty().addClass("twitted");

			// add heading to container element
			if (o.showHeading) {
				c.append("<h2>"+o.headingText+"</h2>");
			}

			// add twitter list to container element
			var twitterListHTML = "<ul id=\"twitter_update_list\"><li></li></ul>";
			c.append(twitterListHTML);

			var tl = jQuery("#twitter_update_list");

			// hide twitter list
			tl.hide();

			// add preLoader to container element
			var preLoaderHTML = jQuery("<p class=\"preLoader\">"+o.loaderText+"</p>");
			c.append(preLoaderHTML);

			// add Twitter profile link to container element
			if (o.showProfileLink) {
				var profileLinkHTML = "<p class=\"profileLink\"><a href=\"http://twitter.com/"+o.userName+"\">http://twitter.com/"+o.userName+"</a></p>";
				c.append(profileLinkHTML);
			}

			// show container element
			c.show();

			jQuery.getScript("http://twitter.com/javascripts/blogger.js");
			jQuery.getScript("http://twitter.com/statuses/user_timeline/"+o.userName+".json?callback=twitterCallback2&count="+o.numTweets, function() {
				// remove preLoader from container element
				jQuery(preLoaderHTML).remove();

				// remove timestamp and move to title of list item
				if (!o.showTimestamp) {
					tl.find("li").each(function() {
						var timestampHTML = jQuery(this).children("a");
						var timestamp = timestampHTML.html();
						timestampHTML.remove();
						jQuery(this).attr("title", timestamp);
					});
				}

				// show twitter list
				if (o.slideIn) {
					// a fix for the jQuery slide effect
					// Hat-tip: http://blog.pengoworks.com/index.cfm/2009/4/21/Fixing-jQuerys-slideDown-effect-ie-Jumpy-Animation
					var tlHeight = tl.data("originalHeight");

					// get the original height
					if (!tlHeight) {
						tlHeight = tl.show().height();
						tl.data("originalHeight", tlHeight);
						tl.hide().css({height: 0});
					}

					tl.show().animate({height: tlHeight}, o.slideDuration);
				}
				else {
					tl.show();
				}

				// add unique class to first list item
				tl.find("li:first").addClass("firstTweet");

				// add unique class to last list item
				tl.find("li:last").addClass("lastTweet");
			});
		});
	};
})(jQuery);