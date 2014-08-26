<button onclick="batch_fb_comments()">Get Comments</button>
<button onclick="batch_fb_replies()">Set Replies</button>

<div class="total-posts"> </div>

<script type="text/javascript" >

	function batch_fb_comments(){
		jQuery(document).ready(function($) {
			var data = {
				'action': 'batch_fb_comments_ajax',
				'whatever': 1234
			};
			$.post(ajaxurl, data, function(response) {
				$( "div.total-posts" ).html( "<p>"+response+"</p>" );
				batch_fb_comments();
			});
		});
	}

	function batch_fb_replies(){
		jQuery(document).ready(function($) {
			var data = {
				'action': 'batch_fb_comments_ajax',
				'whatever': 1234
			};
			$.post(ajaxurl, data, function(response) {
				$( "div.total-posts" ).html( "<p>"+response+"</p>" );
				batch_fb_replies();
			});
		});
	}

</script>