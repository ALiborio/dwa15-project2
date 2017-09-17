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
	$origin = 'american';
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
			$error = 'Must be a valid letter to start with.';
		}
		$startLetter = '';
	}
} else {
	$startLetter = '';
}



$dictionaryJson = file_get_contents($dictDirectory.$origin.'.json');
$dictionary = json_decode($dictionaryJson,true);

$name = '';

if (empty($_POST)) {
	return;
}
# Generate first name
if ($startLetter != '') {
	foreach ($dictionary[$gender] as $dictKey => $dictName) {
		if (substr($dictName, 0, 1) == ucfirst($startLetter)) {
			array_push($fnameList, $dictionary[$gender][$dictKey]);
		}
	}
	# find out if we have enough names
	if (count($fnameList) == 0) {
		$error = 'No names start with the given letter.';
		return;
	} 

	$name = $fnameList[array_rand($fnameList)];

} else {
	$name = $dictionary[$gender][array_rand($dictionary[$gender])];
}

if (!$generateMiddle) {
	if ($surname) {
		$name .= " ".$surname;
	}
	return;
} elseif (alliterative) {
	foreach ($dictionary[$gender] as $dictKey => $dictName) {
		if (substr($dictName, 0, 1) == substr($name, 0, 1)) {
			array_push($fnameList, $dictionary[$gender][$dictKey]);
		}
	}
}

# Generate middle name
$middleName = $name;

while ($name == $middleName) {
	if (!$alliterative) {
		$middleName = $dictionary[$gender][array_rand($dictionary[$gender])];
	} else {
		$middleName = $fnameList[array_rand($fnameList)];
	}
}

$name .= " ".$middleName;
if ($surname) {
	$name .= " ".sanitize($surname);
}

