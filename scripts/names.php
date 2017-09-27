<?php 
require('Dictionary.php');
require('Form.php');

use DWA\Project2\Dictionary;
use DWA\Form;

function generateName(&$list)
{
	$key = array_rand($list);
	$name = $list[$key];
	unset($list[$key]);
	return $name;
}

function removeFromList(&$list, $value)
{
	$key = array_search($value, $list);
	array_splice($list, $key, 1);
}

function filterList($list, $letter)
{
	foreach ($list as $key => $val) {
		if (substr($val, 0, 1) != ucfirst($letter)) {
			unset($list[$key]);
		}
	}
	$list = array_merge($list);
	return $list;
}

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

# setup the nameList array we will be using to pull names from
$nameList = $dictionary->readFromDict($source, $gender);

# Actually generate a name
if ($alliterative) {
	# If alliterative, we will just filter the entire name list up front
	# determine the start letter, so we know how to filter
	if ($startLetter != '') {
		$aLetter = $startLetter;
	} elseif (ctype_alpha(substr($surname, 0, 1))) {
		$aLetter = substr($surname, 0, 1);
	} else {
		$aLetter = '';
	}
	
	# as long as we have a letter to filter by, go ahead and filter it
	if ($aLetter != '') {
		$nameList = filterList($nameList,$aLetter);
	}

	if (count($nameList) == 0) {
			$firstLetterErr = $aLetter;
			return;
		}
	$name = generateName($nameList);
	
} elseif ($startLetter != '') {
	# else, if a start letter is defined, just filter the first name list
	$fnameList = filterList($nameList,$startLetter);

	if (count($fnameList) == 0) {
		$firstLetterErr = $startLetter;
		return;
	}
	$name = generateName($fnameList);
	removeFromList($nameList,$name);
} else {
	if (count($nameList) == 0) {
			$noNamesErr = true;
			return;
		}
	$name = generateName($nameList);
}

if (!$generateMiddle) {
	if ($surname != '') {
		$name .= " ".$surname;
	}
	return;
} elseif ($alliterative) {
		if ($aLetter != '') {
			if (count($nameList) == 0) {
				$middleNameErr = $aLetter;
				# if we have a surname, just output it with the first name
				if ($surname) {
					$name .= " ".$surname;
				}
				return;
			}
			$name .= " ".generateName($nameList);
		} else {
			$nameList = filterList($nameList,substr($name, 0, 1));
			if (count($nameList) == 0) {
				$middleNameErr = substr($name, 0, 1);
				# if we have a surname, just output it with the first name
				if ($surname) {
					$name .= " ".$surname;
				}
				return;
			}
			$name .= " ".generateName($nameList);
		}
} else {
	$name .= " ".generateName($nameList);
}

if ($surname) {
	$name .= " ".$surname;
}
