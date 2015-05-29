<?php

(new Core\Route('/videos/add'))->auth(true)->run(function(\Core\Controller $Controller) {
	$Request = new Core\Request();
	$Url = new Core\Url();

	if ($Request->isPost()) {
		$val = $Request->getArray('val');
		if (empty($val['url'])) {
			throw new Exception('Provide a URL.');
		}

		$parsed = Link_Service_Link::instance()->getLink($val['url']);
		$id = Link_Service_Process::instance()->add([
			'status_info' => '',
			'link' => [
				'url' => $val['url'],
				'embed_code' => 1,
				'image' => $parsed['default_image'],
				'title' => $parsed['title'],
				'description' => $parsed['description'],
				'privacy' => 0,
				'privacy_comment' => 0,
				'embed_code' => $parsed['embed_code']
			]
		]);

		return [
			'redirect' => $Url->make('/videos/' . $id)
		];
	}

	$Controller->title('Share a Video')->h1('Share a Video', '/videos/add');

	return $Controller->render('add.html');
});

(new Core\Route('/videos/:id'))->where([':id' => '([0-9]+)'])->run(function(\Core\Controller $Controller, $id) {
	$video = (new Api\Feed())->get($id);

	$response = Link_Service_Link::instance()->getLink($video->external_url);
	$video->html = $response['embed_code'];

	$Controller->title($video->title)
		->section('Videos', '/videos')
		->h1($video->title, '/videos/' . $video->id);

	Phpfox_Template::instance()->setActionMenu([
		'Create a Page' => [
			'custom' => 'data-custom-class="js_box_full"',
			'class' => 'popup',
			'url' => 'asd'
		]
	]);

	return $Controller->render('view.html', [
		'video' => $video
	]);
});

(new Core\Route('/videos'))->run(function(\Core\Controller $Controller) {

	$Controller->title('Videos')
		->section('Videos', '/videos')
		->asset('@static/jquery/plugin/jquery.mosaicflow.min.js');

	return $Controller->render('index.html', [
		'videos' => (new Api\Feed())->get(['type_id' => ['link', 'video']])
	]);
});