<?php
/**
 * This is the configuration for generating message translations
 * for the Yii framework. It is used by the 'yiic message' command.
 */
return array(
	'sourcePath'=>__DIR__.'/../../..',
	'messagePath'=>__DIR__.'/../../../messages',
	'languages'=>array('template'),
	'fileTypes'=>array('php'),
	'overwrite'=>true,
	'exclude'=>array(
		'.git',
		'.gitignore',
		'.gitkeep',
		'/demo',
		'/messages',
	),
);
