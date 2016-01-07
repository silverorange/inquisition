<?php

namespace Silverorange\Autoloader;

$package = new Package('silverorange/inquisition');

$package->addRule(
	new Rule(
		'dataobjects',
		'Inquisition',
		array(
			'Inquisition',
			'InquisitionQuestionBinding',
			'QuestionGroup',
			'QuestionHint',
			'QuestionImage',
			'QuestionOptionImage',
			'QuestionOption',
			'Question',
			'Response',
			'ResponseUsedHintBinding',
			'ResponseValue',
			'Wrapper'
		)
	)
);
$package->addRule(new Rule('exceptions', 'Inquisition', 'Exception'));
$package->addRule(new Rule('views', 'Inquisition', 'View'));
$package->addRule(new Rule('', 'Inquisition'));

Autoloader::addPackage($package);

?>
