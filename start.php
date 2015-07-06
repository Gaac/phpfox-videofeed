<?php

/**
 * Adding a video route. We use JavaScript to add a link in the main video section to reach this route via a modal.
 */
(new Core\Route('/videos/add'))->auth(true)->run(function(\Core\Controller $Controller) {

	// Check to see if the user posted a URL
	if ($Controller->request->isPost()) {
		// Grab the array just for the post "val"
		$val = $Controller->request->getArray('val');
		if (empty($val['url'])) {
			throw new Exception('Provide a URL.');
		}

		// Cheat and use the Link service module to grab the HTML data for us
		$parsed = Link_Service_Link::instance()->getLink($val['url']);

		$Feed = (new \Api\Feed())->post([
			'type_id' => 'PHPfox_VideoFeed',
			'content' => [
				'url' => $val['url'],
				'image' => $parsed['default_image'],
				'title' => $parsed['title'],
				'description' => $parsed['description'],
				'embed_code' => $parsed['embed_code']
			]
		]);

		// Return a JSON redirect to the browser. This will send the user to the new video they just added
		return [
			'redirect' => $Controller->url->make('/videos/' . $Feed->id)
		];
	}

	// Set the pages title and h1 tag
	$Controller->title('Share a Video')->h1('Share a Video', '/videos/add');

	// Render the page
	return $Controller->render('add.html');
});

/**
 * Route to view a video
 */
(new Core\Route('/videos/:id'))->where([':id' => '([0-9]+)'])->run(function(\Core\Controller $Controller, $id) {
	// Get the feed based on the ID#
	$video = (new Api\Feed())->get($id);

	// Use the Link service to get the current HTML embed code
	$response = Link_Service_Link::instance()->getLink($video->content->url);
	$video->html = $response['embed_code'];

	// Set the pages section, title and h1 based on the video details
	$Controller->title($video->content->title)
		->section('Videos', '/videos')
		->h1($video->content->title, '/videos/' . $video->id);

	// Render the page
	return $Controller->render('view.html', [
		'video' => $video
	]);
});

/**
 * Load all the videos
 */
(new Core\Route('/videos'))->run(function(\Core\Controller $Controller) {

	$Controller->title('Videos')
		->section('Videos', '/videos')
		->asset('@static/jquery/plugin/jquery.mosaicflow.min.js');

	$videos = (new Api\Feed())->get(['type_id' => 'PHPfox_VideoFeed', 'limit' => 20]);


	return $Controller->render('index.html', [
		'videos' => $videos
	]);
});