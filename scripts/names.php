<?php include ('helpers.php');

function generateName (&$list) {
	$key = array_rand($list);
	$name = $list[$key];
	unset($list[$key]);
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

function filenameExternal ($filename) {
	$filename = str_replace("_", " ", $filename);
	return ucwords($filename);
}

# Setup the sourceList from the dictionaries in the directory

$dictDirectory = $_SERVER['DOCUMENT_ROOT'].'/dictionaries/';
$sourceList = scandir($dictDirectory);

foreach ($sourceList as $index => $file) {
	if (substr($file, -5) != '.json') {
		unset($sourceList[$index]);
	} else {
		$sourceList[$index] = substr($file, 0, -5);
	}
}

# setup variables from $_GET or defaults on initial page load

if (isset($_GET['source'])) {
	$source = $_GET['source'];
} else {
	$source = 'any';
}


if (isset($_GET['gender'])) {
	$gender = $_GET['gender'];
} else {
	$gender = 'neutral';
}

if (isset($_GET['middle'])) {
	$generateMiddle = isset($_GET['middle']);
} else {
	$generateMiddle = false;
}

$alliterative = isset($_GET['alliterative']);

if (isset($_GET['startLetter'])) {
	if (ctype_alpha($_GET['startLetter'])) {
		$startLetter = $_GET['startLetter'];
	} else {
		if ($_GET['startLetter'] != '') {
			$error = 'Invalid character: <strong>'.$_GET['startLetter'].'</strong> Must be a valid letter to start with. Start with letter ignored.';
		}
		$startLetter = '';
	}
} else {
	$startLetter = '';
}

if (isset($_GET['surname'])) {
	$surname = $_GET['surname'];
} else {
	$surname = '';
}

# default generate middle name as checked on intial load
if (empty($_GET)) {
	$generateMiddle = true;
	return;
}

# setup the nameList array we will be using to pull names from
$nameList = array();
if ($source == 'any') {
	foreach ($sourceList as $dictKey => $dictName) {
		$dictionaryJson = file_get_contents($dictDirectory.$dictName.'.json');
		$dictionary = json_decode($dictionaryJson,true);
		$nameList = array_merge($nameList,$dictionary[$gender]);
	}
	$nameList = array_unique($nameList);

} else {
	$dictionaryJson = file_get_contents($dictDirectory.$source.'.json');
	$dictionary = json_decode($dictionaryJson,true);
	$nameList = $dictionary[$gender];
}

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
	if (count($nameList) == 0) {
			$error = 'No names fit the given criteria.';
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
				$error = 'No unique middle names start with the letter <strong>'.sanitize($aLetter).'</strong>. No middle name could be generated.';
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
