<?php 
require('scripts/names.php'); 
#setup the field name mapping for error message output
$fieldMap = [
    'source' => 'Source',
    'gender' => 'Gender',
    'middle' => 'Generate middle name',
    'alliterative' => 'Generate alliterative names',
    'startLetter' => 'Start with letter',
    'surname' => 'Surname',
];
?>

<!DOCTYPE html>
<html>
<head>
	<title>Name Generator</title>
	<script src="https://use.fontawesome.com/623053ff70.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="main-container">
		<h1>Name Generator</h1>

		<hr>

		<form method="GET">
			<div class="input">
				<label for="source">Name list to use: </label>
				<select name="source" id="source">
					<option value="any" <?php if ($form->prefill('source', 'any') == 'any') echo 'selected'; ?>>Any</option>
					<?php foreach ($sourceList as $index => $sourceValue) : ?>
						<option value="<?=$sourceValue?>" <?php if ($form->prefill('source', 'any') == $sourceValue) echo 'selected'; ?>><?= $dictionary->filenameExternal($sourceValue)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="input">
				<input type="radio" name="gender" id="neutral" value="neutral" <?php if ($form->prefill('gender', 'neutral') == 'neutral') echo 'checked'; ?>>
				<label for="neutral">Neutral</label>
				<input type="radio" name="gender" id="male" value="male" <?php if ($form->prefill('gender', 'neutral') == 'male') echo 'checked'; ?>>
				<label for="male">Male</label>
				<input type="radio" name="gender" id="female" value="female" <?php if ($form->prefill('gender', 'neutral') == 'female') echo 'checked'; ?>>
				<label for="female">Female</label>
			</div>
			<div class="input">
				<input type="checkbox" name="middle" id="middleName" <?php if ($generateMiddle) echo 'checked'; ?>>
				<label for="middleName">Generate middle name</label>
			</div>
			<div class="input">
				<input type="checkbox" name="alliterative" id="alliterative" <?php if ($form->prefill('alliterative', false)) echo 'checked'; ?>>
				<label for="alliterative">Generate alliterative names</label>
			</div>
			<div class="input">
				<label for="startLetter">Start with letter: </label>
				<input type="text" name="startLetter" id="startLetter" maxlength="1" size="1" value="<?=$form->prefill('startLetter')?>">
			</div>
			<div class="input">
				<label for="surname">Surname: </label>
				<input type="text" name="surname" id="surname" value="<?=$form->prefill('surname')?>">
			</div>
			<div class="input button">
				<input type="submit" value="Generate!" class="btn">
			</div>
		</form>

		<hr>

		<?php if (isset($errors)) {
			foreach ($errors as $errKey => $errMsg) { ?>
			 	<div class="error display">
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
					The field <strong><?=$fieldMap[$errKey]?></strong><?=$errMsg?>
				</div>
			<?php } 
		} ?>

		<?php if (isset($nameGen->firstLetterErr)) : ?>
			<div class="error display">
				<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
				No names in the given criteria begin with <strong><?=$form->sanitize($nameGen->firstLetterErr)?></strong>.
			</div>
		<?php endif; ?>

		<?php if (isset($nameGen->middleNameErr)) : ?>
			<div class="error display">
				<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
				No unique middle names in the given criteria begin with <strong><?=$form->sanitize($nameGen->middleNameErr)?></strong>.
			</div>
		<?php endif; ?>

		<?php if (isset($nameGen->noNamesErr)) : ?>
			<div class="error display">
				<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
				No names fit the given criteria.
			</div>
		<?php endif; ?>

		<?php if (isset($nameGen->firstName)) : ?>
			<div class="name display">
				<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
				<?php 
					echo $nameGen->firstName." ";
					if (isset($nameGen->middleName)) : echo $nameGen->middleName." "; endif;
					echo $form->sanitize($surname);
				?>
			</div>
		<?php endif; ?>
		
	</div>
</body>
</html>