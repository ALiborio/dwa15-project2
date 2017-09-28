<?php 
require('Dictionary.php');
require('NameGenerator.php');
require('Form.php');

use DWA\Project2\Dictionary;
use DWA\Project2\NameGenerator;
use DWA\Form;

# Setup the sourceList from the dictionaries in the directory
$dictionary = new Dictionary($_SERVER['DOCUMENT_ROOT'].'/dictionaries/');
$sourceList = $dictionary->getDictList();

# get form data and validate it
$form = new Form($_GET);

if ($form->isSubmitted()) {
	$errors = $form->validate(
		[
			'surname' => 'alpha',
			'startLetter' => 'alpha'
		]
	);
	$source = $form->get('source');
	$gender = $form->get('gender');
	$generateMiddle = $form->isChosen('middle');
	$alliterative = $form->isChosen('alliterative');
	$startLetter = $form->get('startLetter','');
	$surname = $form->get('surname','');
	if ($form->hasErrors) {
		return;
	}

} else {
	# default the generateMiddle checkbox if not submitted yet
	$generateMiddle = true;
	return;
}

# setup the NameGenerator that will generate our names
$nameGen = new NameGenerator($dictionary, $source, $gender);

# Actually generate names
if ($alliterative) {
	if ($nameGen->setupAlliterativeNames($startLetter, $surname) == 'error') {
		return;
	} elseif ($nameGen->generateFirstName() == 'error') {
		return;
	}
	
} elseif ($nameGen->generateFirstName($startLetter) == 'error') {
	return;
}

if (!$generateMiddle) {
	return;
} else {
	$nameGen->generateMiddleName($alliterative);
}
