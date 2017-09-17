<?php include ('helpers.php');

# Setup the originList from the dictionaries in the directory

$dictDirectory = $_SERVER['DOCUMENT_ROOT'].'/dictionaries/';
$originList = scandir($dictDirectory);

foreach ($originList as $index => $file) {
	if (substr($file, -5) != '.json') {
		unset($originList[$index]);
	} else {
		$originList[$index] = substr($file, 0, -5);
	}
}

# setup variables from $_POST or defaults on initial page load

if (isset($_POST['surname'])) {
	$surname = $_POST['surname'];
} else {
	$surname = '';
}

if (isset($_POST['origin'])) {
	$origin = $_POST['origin'];
} else {
	$origin = 'any';
}


if (isset($_POST['gender'])) {
	$gender = $_POST['gender'];
} else {
	$gender = 'neutral';
}

if (isset($_POST['middle'])) {
	$generateMiddle = isset($_POST['middle']);
} else {
	$generateMiddle = false;
}

$alliterative = isset($_POST['alliterative']);

if (isset($_POST['startLetter'])) {
	if (ctype_alpha($_POST['startLetter'])) {
		$startLetter = $_POST['startLetter'];
	} else {
		if ($_POST['startLetter'] != '') {
			$error = 'Invalid character: <strong>'.$_POST['startLetter'].'</strong> Must be a valid letter to start with. Start with letter ignored.';
		}
		$startLetter = '';
	}
} else {
	$startLetter = '';
}

if (empty($_POST)) {
	# default middle checked
	$generateMiddle = true;
	return;
}

$nameList = array();
if ($origin == 'any') {
	foreach ($originList as $dictKey => $dictName) {
		$dictionaryJson = file_get_contents($dictDirectory.$dictName.'.json');
		$dictionary = json_decode($dictionaryJson,true);
		$nameList = array_merge($nameList,$dictionary[$gender]);
	}
	$nameList = array_unique($nameList);

} else {
	$dictionaryJson = file_get_contents($dictDirectory.$origin.'.json');
	$dictionary = json_decode($dictionaryJson,true);
	$nameList = $dictionary[$gender];
}

function generateName (&$list) {
	$key = array_rand($list);
	$name = $list[$key];
	array_splice($list, $key, 1);
	return $name;
}

function removeFromList(&$list, $value) {
	$key = array_search($value, $list);
	array_splice($list, $key, 1);
}

function filterList($list, $letter) {
	foreach ($list as $key => $val) {
		if (substr($val, 0, 1) != ucfirst($letter)) {
			unset($list[$key]);
		}
	}
	$list = array_merge($list);
	return $list;
}

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
			$error = 'No names start with the letter <strong>'.sanitize($aLetter).'</strong>.';
			return;
		}
	$name = generateName($nameList);
	
} elseif ($startLetter != '') {
	# else, if a start letter is defined, just filter the first name list
	$fnameList = filterList($nameList,$startLetter);

	if (count($fnameList) == 0) {
		$error = 'No names start with the letter <strong>'.sanitize($startLetter).'</strong>.';
		return;
	}
	$name = generateName($fnameList);
	removeFromList($nameList,$name);
} else {
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
				$error = 'No unique middle names start with the letter <strong>'.sanitize($aLetter).'</strong>.';
				# if we have a surname, just output it with the first name
				if ($surname) {
					$name .= " ".sanitize($surname);
				}
				return;
			}
			$name .= " ".generateName($nameList);
		} else {
			$nameList = filterList($nameList,substr($name, 0, 1));
			if (count($nameList) == 0) {
				$error = 'No unique middle names start with the letter <strong>'.substr($name, 0, 1).'</strong>.';
				# if we have a surname, just output it with the first name
				if ($surname) {
					$name .= " ".sanitize($surname);
				}
				return;
			}
			$name .= " ".generateName($nameList);
		}
} else {
	$name .= " ".generateName($nameList);
}

if ($surname) {
	$name .= " ".sanitize($surname);
}
