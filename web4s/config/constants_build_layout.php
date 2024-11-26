<?php
use Cake\Core\Configure;

define('HEADING_TITLE_ELEMENT', 'heading_title');
define('IMAGE_ELEMENT', 'image');



Configure::write('CATEGORIES_ELEMENT',
	[
		'basic' => [
			'title' => 'Basic',
			'elements' => [
				HEADING_TITLE_ELEMENT => [
					'title' => 'Heading',
					'icon' => 'eicon-t-letter'
				],
				IMAGE_ELEMENT => [
					'title' => 'Image',
					'icon' => 'eicon-image'
				],
			]
		],
		'data' => [
			'title' => 'DATA',
			'elements' => [
			]
		]
	]
);