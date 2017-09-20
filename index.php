<?php require('scripts/names.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Name Generator</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="main-container">
		<h1>Name Generator</h1>

		<hr>

		<form method="GET" action="\">
			<div class="input">
				<label for="source">Name list to use: </label>
				<select name="source" id="source">
					<option value="any" <?php if ($source == 'any') echo 'selected'; ?>>Any</option>
					<?php foreach ($sourceList as $index => $sourceValue) : ?>
						<option value="<?=$sourceValue?>" <?php if ($source == $sourceValue) echo 'selected'; ?>><?= filenameExternal($sourceValue)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="input">
				<input type="radio" name="gender" id="neutral" value="neutral" <?php if ($gender == 'neutral') echo 'checked'; ?>>
				<label for="neutral">Neutral</label>
				<input type="radio" name="gender" id="male" value="male" <?php if ($gender == 'male') echo 'checked'; ?>>
				<label for="male">Male</label>
				<input type="radio" name="gender" id="female" value="female" <?php if ($gender == 'female') echo 'checked'; ?>>
				<label for="female">Female</label>
			</div>
			<div class="input">
				<input type="checkbox" name="middle" id="middleName" <?php if ($generateMiddle) echo 'checked'; ?>>
				<label for="middleName">Generate middle name</label>
			</div>
			<div class="input">
				<input type="checkbox" name="alliterative" id="alliterative" <?php if ($alliterative) echo 'checked'; ?>>
				<label for="alliterative">Generate alliterative names</label>
			</div>
			<div class="input">
				<label for="startLetter">Start with letter: </label>
				<input type="text" name="startLetter" id="startLetter" maxlength="1" size="1" value="<?=$startLetter?>">
			</div>
			<div class="input">
				<label for="surname">Surname: </label>
				<input type="text" name="surname" id="surname" value="<?=sanitize($surname)?>">
			</div>
			<div class="input">
				<input type="submit" value="Generate!" class="btn">
			</div>
		</form>

		<hr>

		<?php if (isset($error)) : ?>
			<div class="error display">
				<?=$error?>
			</div>
		<?php endif; ?>

		<?php if (isset($name)) : ?>
			<div class="name display">
				<?=$name?>
			</div>
		<?php endif; ?>
		
	</div>
</body>
</html>