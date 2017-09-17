# Project 2 for Dynamic Web Applications Fall 2017

## Name Generator

This page will generate a name based on the input entered. 

The surname field will be appended to the end of the generated name so the whole name will be displayed. If no surname is entered, only the generated name(s) will be displayed.

The origin field determines which origin dictionary we will use to generate the names. Each choice here corresponds to a json file in the dictionaries folder. See [Dictionary Files](#dictionary-files) heading below for more details on the format of these files.

The gender radio buttons allow switching between gender specific names. Neutral will return names generally considered unisex, while male and female will only return names generally considered to be associated with those genders respectively. The name lists are separated out by these genders. See [Dictionary Files](#dictionary-files) for details on how these are structured.

The generate middle name checkbox determines whether a middle name will also be generated. If this is unchecked, only the first name will be generated. If it is checked, it will also generate a middle name, ensuring it is different from the first name.

The generate alliterative names will generate a name that is alliterative in nature. For example: *John Jacob*. If a surname is entered, the first letter will be used to attempt to generate an alliterative first name. However, if the start with letter is filled out, it will take precedence over the surname.

The start with letter field accepts a single character. Only alphabetic characters will be allowed, if a non-alphabetic character is entered, an error will be displayed and it will be ignored. When a letter is entered in this field, it will only generate a first name that starts with that letter (case insensitive.)

### Dictionary Files

The dictionaries of names used for generation can be extended by adding additional JSON files to the dictionaries folder. The name of the json file will be used in the origin dropdown. If a space is desired in the name in the dropdown, underscores should be used in the file name.

The file should be setup with three keys corresponding to the gender selections: "neutral", "male", and "female". The value for these keys should be an array of names: `["name1","name2","name3"]`.

Example of a valid JSON file that can be used as a dictionary:
```json
{
	"neutral" : ["neutralname1","neutralname2","neutralname3"],
	"male" : ["malename1","malename2","malename3"],
	"female" : ["femalename1","femalename2","femalename3"]
}
```