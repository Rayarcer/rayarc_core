<script type="text/javascript">
console.log(fapOpened);
if (fapOpened === null)
	var fapOpened=true;
	
jQuery(document).ready(function(){
	console.log("jQuery ready");
	
if ( jQuery.fn.fullwidthAudioPlayer ) {
	$ = jQuery.noConflict();
	jQuery('#fap').fullwidthAudioPlayer({
		keyboard: false,
		autoPlay:false,
		howlOptions: {html5: true},
		wrapperPosition: 'bottom',
		playNextWhenFinished: true,
		opened:fapOpened,
		popup : true,
		popupUrl: "<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/musicband/fwap/html/popup.php",
		htmlURL:"<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/musicband/fwap/html/fwap.php",
		openLabel: '<i class="fa fa-music fa-fw"></i>', //1.6.1 - the label for the close button
		closeLabel: '<i class="fa fa-angle-down fa-fw"></i>', //1.6.1 - the label for the open button,
		socials: false,

	});
	console.log("fap initialize");

	 jQuery('#fap').bind('onFapReady', function(evt, trackData) {
        jQuery.fullwidthAudioPlayer.volume(0.7);
    });
	jQuery('.fap-single-track').on('click',function(){
		jQuery.fullwidthAudioPlayer.setPlayerPosition('open', true );
	});


	selectedPlayButton = null;
	jQuery('#fap').on('onFapTrackSelect', function(evt, trackData, playState) {
			currentTrackUrl = null;
			if(trackData.duration == null) {
				currentTrackUrl = trackData.stream_url;
	
				if(currentTrackUrl.search('official.fm') != -1) {
	
					currentTrackUrl = trackData.permalink_url;
	
				}
			}
			else {
				//soundcloud
				currentTrackUrl = trackData.permalink_url;
	
			}
			if( 0 ) {
				currentTrackUrl = currentTrackUrl;
			}
			else {
				currentTrackUrl = currentTrackUrl.replace(/.*?:\/\//g, "").replace(/^www./, "");
			}
	
			selectedPlayButton = jQuery('.fap-single-track[href*="'+currentTrackUrl+'"]');
			if(playState) {
				console.log(".fap-single-track in play state:"+currentTrackUrl);
				jQuery('.selected').removeClass('selected');
				jQuery('ul').children().removeClass('selected');
				jQuery(selectedPlayButton).closest('li').addClass('selected');
				jQuery(selectedPlayButton).addClass('selected').parents('.hover_type').addClass('selected');
				console.log("select play title="+jQuery(selectedPlayButton).closest('li').children().first().attr("title"));
				console.log("select play data-id="+jQuery(selectedPlayButton).closest('li').children().first().attr("data-id"));
				key=jQuery(selectedPlayButton).closest('li').children().first().attr("data-id");
				title=jQuery(selectedPlayButton).closest('li').children().first().attr("title");
				domain=jQuery(selectedPlayButton).closest('li').children().first().attr("data-domain");
				var jqxhr = jQuery.ajax(musicCentralHost+"/api/setview.php?key="+key+"&domain="+domain+"&type=S")
				 .done(function( data ) {	
						var jqxhr = jQuery.ajax(musicCentralHost+"/api/getview.php?key="+key+"&domain="+domain+"&type=S" )
 				 		.done(function( data ) {
  							jQuery( ".ivaplay-"+key ).html( data );
							console.log( "success: new play count=" + data );
							 if(noghost)
								ga('send', 'event', 'play_start', 'Click', key+":"+title);
							 
						})
  						.fail(function() {
   							console.log( "error" );
   							if(noghost)
								ga('send', 'event', 'play_error', 'Click', key+":"+title);

  						})
  						.always(function() {
   							console.log( "complete" );
  						});
				 });
			}
			else {
				jQuery('.selected').removeClass('selected');
				jQuery(this).closest('li').addClass('selected');
				jQuery(this).addClass('selected').parents('.hover_type').addClass('selected');

			}
	})
	.on('onFapPlay', function() {
							if(selectedPlayButton != null) {
								jQuery('.selected').removeClass('selected');
								jQuery('ul').children().removeClass('selected');
								jQuery(selectedPlayButton).closest('li').addClass('selected');
								jQuery(selectedPlayButton).addClass('selected').parents('.hover_type').addClass('selected');
								//jQuery('.djmix-content').removeClass('selected');
								//jQuery(selectedPlayButton).closest('div').addClass('selected');
							;
							}
						})
						.on('onFapPause', function() {
							if(selectedPlayButton != null) {
							jQuery('.selected').removeClass('selected');
							jQuery('ul').children().removeClass('selected');

							}
						});

	jQuery('#playAll').on('click',function(){
		key=jQuery(this).attr("data-id");
		title=jQuery(this).attr("data-title");
		console.log("play all on "+key+":"+title);
		 if(noghost)
			ga('send', 'event', 'playAll_start', 'Click', key+":"+title);
	});
	}
});
</script>
<div id="fap"> 
	
	
	

</div>	

<script> 
var centralHost="<?php echo CENTRALHOST?>";
var coreHost="<?php echo COREHOST?>";
var musicCentralHost="<?php echo MUSICCENTRALHOST?>";
var sessionId="<?php echo session_id();?>";
</script> 

<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/comment-reply.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.easing.1.3.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/countdown.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/superfish.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.preloadify.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.prettyPhoto.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.fitvids.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/isotope.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/sys_custom.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.flexslider.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/waypoints.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/jquery/ui/core.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/jquery/ui/widget.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/jquery/ui/mouse.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/jquery/ui/draggable.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/jquery/ui/sortable.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jquery.jplayer.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/history.js'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var aws_data = {"rootUrl":"http:\/\/<?php echo RAWCOREHOST;?>\/cms\/wp\/","ThemeDir":"http:\/\/<?php echo RAWCOREHOST;?>\/cms\/wp\/wp-content\/themes\/musicplay\/","choose_player":"album","preloader_image":""};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/ajaxify.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/jplayer.playlist.min.js'></script>

<!--  
<script type='text/javascript' src='//connect.soundcloud.com/sdk/sdk-3.1.2.js'></script>
<script>
SC.initialize({
	
	<?php if(strrpos($_SERVER['SERVER_NAME'],"localhost")===false){?>
	//server side app credentials
    client_id: "fadc3bd85e73df887ac188465b2230ae",
	<?php } else {?>
	//localhost app credentials
	client_id: "310c74cb7877a7d863298cbc18eeb2c2",
	<?php } ?>
	redirect_uri: "<?php echo CENTRALHOST;?>/apps/soundcloud/callback.php"
  });
  
</script>
-->

<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/musicband/fwap/js/jquery.fullwidthaudioplayer.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-content/themes/musicplay/js/owl.carousel.min.js'></script>
<script type='text/javascript' src='<?php echo COREHOST;?>/cms/wp/wp-includes/js/wp-embed.min.js'></script>
	<div id="back-top"><a href="#header"><span class="fadeInUp"></span></a></div>
