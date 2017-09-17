<?php require('scripts/names.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Name Generator</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="main-container">
		<h1>Name Generator</h1>

		<form method="POST" action="\">
			<label>Surname: </label>
			<input type="text" name="surname" value="<?=sanitize($surname)?>">
			<br>
			<label>Origin: </label>
			<select name="origin">
				<?php foreach ($originList as $index => $originValue) : ?>
					<option value="<?=$originValue?>" <?php if ($origin == $originValue) echo 'selected'; ?>><?= ucfirst($originValue)?></option>
				<?php endforeach; ?>
			</select>
			<br>
			<input type="radio" name="gender" value="neutral" <?php if ($gender == 'neutral') echo 'checked'; ?>>
			<label>Neutral</label>
			<input type="radio" name="gender" value="male" <?php if ($gender == 'male') echo 'checked'; ?>>
			<label>Male</label>
			<input type="radio" name="gender" value="female" <?php if ($gender == 'female') echo 'checked'; ?>>
			<label>Female</label>
			<br>
			<input type="checkbox" name="middle" <?php if ($generateMiddle) echo 'checked'; ?>>
			<label>Generate middle name</label>
			<br>
			<input type="checkbox" name="alliterative" <?php if ($alliterative) echo 'checked'; ?>>
			<label>Generate alliterative names</label>
			<br>
			<label>Start with letter: </label>
			<input type="text" name="startLetter" maxlength="1" size="1" value="<?=$startLetter?>">
			<br>
			<input type="submit" value="Generate!">
		</form>

		<?php if (isset($error)) : ?>
			<div>
				<?=$error?>
			</div>
		<?php endif; ?>

		<?php if ($name != '') : ?>
			<div class="name-display">
				<?=$name?>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>