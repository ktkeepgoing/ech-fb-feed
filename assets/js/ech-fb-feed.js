jQuery(document).ready(function(){

    /***** Overlay *****/
    jQuery(document).on("click", ".video_thumb", function(){
        var get_video = jQuery(this).data("fb-video");
        console.log("get_video"+get_video);

        jQuery(".fb_video_container video source").attr("src", get_video);
        jQuery(".fb_video_container video")[0].load();
        jQuery(".fb_feed_overlay").css("display","block");
        jQuery('.fb_video_container video').get(0).play();
        
    });

    jQuery(".fb_overlay_close").click(function(){
        jQuery(".fb_feed_overlay").css("display","none");
        jQuery(".fb_video_container video source").attr("src", "");
        jQuery(".fb_video_container video")[0].load();
    });
    /***** (END)Overlay *****/






    jQuery(document).on('click', '#fb_load_more_btn', function () {
        jQuery("#fb_load_more_btn").prop('disabled', true);
        jQuery("#fb_load_more_btn").html("請稍後");

        var fb_after = jQuery("#fb_load_more_btn").data("fb-feed-after");
        var fb_limit = jQuery("#fb_load_more_btn").data("fb-limit");
        


        var ajaxurl = jQuery(this).data("url");

        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                fb_after: fb_after,
                fb_limit: fb_limit,
                action: 'fb_load_more_feed'
            },
            success: function(response) {
                var jsonObj = JSON.parse(response);
                /*console.log("fb_after: " + jsonObj.fb_after);
                console.log("html: " + jsonObj.html);*/
                jQuery(".ech_fb_feed_container").append(jsonObj.html);
                jQuery("#fb_load_more_btn").data("fb-feed-after", jsonObj.fb_after);
                jQuery("#fb_load_more_btn").attr("data-fb-feed-after", jsonObj.fb_after); // change value in HTML
                
                jQuery("#fb_load_more_btn").prop('disabled', false);
                jQuery("#fb_load_more_btn").html("更多貼文");
                
                /*
                if (topage > totalpage) {
                    jQuery("#fb_load_more_btn").css("display", "none");
                }*/
            },
            error: function (response) {
				console.log(response);
			}
        });
    }); // load button onclick


}); // ready()
