<?php

namespace DWA\Project2;

class NameGenerator
{

	/**
    * Properties
    */
	private $dictionary;
	private $nameList = [];
	private $aLetter;
	public $firstName;
	public $middleName;
	public $firstLetterErr;
	public $middleNameErr;
	public $noNamesErr;


	/**
    * Constructor
    * 	Sets up the nameList from the dictionary provider for the given source and gender
    */
    public function __construct(Dictionary $dict, $source, $gender)
    {
        $this->dictionary = $dict;
		$this->nameList = $dict->readFromDict($source, $gender);
    }

    /**
    * returns the nameList
    */
    public function getNameList()
    {
    	return $this->nameList;
    }

    /**
    * Generates a random name from the given $list
    */
    private function generateName($list)
    {
		$key = array_rand($list);
		$name = $list[$key];
		return $name;
	}

	/**
    * Removes the given $value from the given $list so we don't have duplicate names generated
    */
	private function removeFromList(&$list, $value)
	{
		$key = array_search($value, $list);
		array_splice($list, $key, 1);
	}

	/**
    * Filters the given $list by the given $letter and returns the filtered $list
    */
	private function filterList($list, $letter)
	{
		foreach ($list as $key => $val)
		{
			if (substr($val, 0, 1) != ucfirst($letter))
			{
				unset($list[$key]);
			}
		}
		$list = array_merge($list);
		return $list;
	}

	/**
    * Sets up the list for alliterative generation mode if we have a starting letter
    *	$startLetter takes precedence over $surname for finding the alliterative letter
    * Returns 'ok' if successful or 'error' if an error occurred
    */
    public function setupAlliterativeNames($startLetter = '', $surname = '')
	{
		if ($startLetter != '') {
			$this->aLetter = $startLetter;
		} elseif (ctype_alpha(substr($surname, 0, 1))) {
			$this->aLetter = substr($surname, 0, 1);
		} else {
			$this->aLetter = '';
		}
		
		# as long as we have a letter to filter by, go ahead and filter it
		if ($this->aLetter != '') {
			$this->nameList = $this->filterList($this->nameList,$this->aLetter);
		}

		if (count($this->nameList) == 0) {
				$this->firstLetterErr = $this->aLetter;
				return 'error';
		} else {
			return 'ok';
		}
	}

	/**
    * Generates the first name with the given $startLetter
    * If in alliterative generation mode, $aLetter is already setup and will be used instead
    * Returns 'ok' if successful or 'error' if an error occurred
    */
    public function generateFirstName($startLetter='')
	{
		if (count($this->nameList) == 0) {
			$this->noNamesErr = true;
			return 'error';
		}

		if ($this->aLetter == '' && ($startLetter != '')) {
			# if a start letter is defined, just filter the first name list, preserving the original list
			$fnameList = $this->filterList($this->nameList,$startLetter);

			if (count($fnameList) == 0) {
				$this->firstLetterErr = $startLetter;
				return 'error';
			}
			$this->firstName = $this->generateName($fnameList);
		} else {
			$this->firstName = $this->generateName($this->nameList);
		}
		$this->removeFromList($this->nameList, $this->firstName);
		return 'ok';
	}

	/**
    * Generates the middle name
    * If $alliterative and we don't already have $aLetter we will use the $firstName
    * 	for our alliterative name generation logic.
    * Returns 'ok' if successful or 'error' if an error occurred
    */
    public function generateMiddleName($alliterative = false)
	{
		if (count($this->nameList) == 0) {
			$this->middleNameErr = $this->aLetter;
			return 'error';
		}

		if ($alliterative && ($this->aLetter == '')) {
			$mnameList = $this->filterList($this->nameList,substr($this->firstName, 0, 1));

			if (count($mnameList) == 0) {
				$this->middleNameErr = substr($this->firstName, 0, 1);
				return 'error';
			}

			$this->middleName = $this->generateName($mnameList);
		} else {
			$this->middleName = $this->generateName($this->nameList);
		}

		return 'ok';
	}
	
}