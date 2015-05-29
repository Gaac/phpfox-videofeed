
$Ready(function() {
	var v = $('#pf-video-player');

	if (!v.length) {
		$('body').prepend('<div id="pf-video-layer"></div><div id="pf-video-player"></div>');
	}

	$(document).keypress(function(e) {
		if (e.keyCode == 27 && v.length) {
			$('#pf-video-layer, #pf-video-player').fadeOut();
		}
	});

	$('#pf-video-layer').click(function() {
		$('#pf-video-layer, #pf-video-player').fadeOut();
	});

	$('.pf-videos a, .pf-videos-click').click(function() {
		var t = $(this);

		$('#pf-video-layer').show();
		$('#pf-video-player').html('').css({
			top: ($(window).scrollTop() + 20)
		}).show();

		$.ajax({
			url: t.attr('href'),
			contentType: 'application/json',
			success: function(e) {
				$('#pf-video-player').html(e.content);
				$Core.loadInit();
			}
		});

		return false;
	});

	$('._app_link:not(.built)').each(function() {
		var t = $(this), url = PF.url.make('/videos/' + t.data('feed-id'));

		t.addClass('built');
		t.find('.play_link').attr('onclick', '').attr('href', url).addClass('pf-videos-click').addClass('no_ajax');
		t.find('.activity_feed_content_link_title').attr('href', url).addClass('pf-videos-click').addClass('no_ajax');
	});

	// breadcrumbs
	var t = $('#page_route_videos'), html;
	if (t.length && !t.hasClass('video-menu-added')) {
		t.addClass('video-menu-added');

		html = '<div class="breadcrumbs_menu"><ul><li><a href="' + PF.url.make('/videos/add') + '" class="popup">Share a Video</a></li></ul></div>';
		$('.breadcrumbs').append(html);
		$Core.loadInit();
	}
});