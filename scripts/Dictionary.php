<?php

namespace DWA\Project2;

/**
* 
*/
class Dictionary
{
	
	/**
    * Properties
    */
	private $dictDirectory;
	private $fileType = 'json';
	private $dictList = [];

	/**
    * Constructor
    *  $dir should be the directory path where the dictionary files are kept
    *    example: $_SERVER['DOCUMENT_ROOT'].'/dictionaries/'
    *  $dir should be a valid file type (default of 'json')
    *    example: json
    */
    public function __construct($dir, $type = 'json')
    {
        $this->dictDirectory = $dir;
		$this->dictList = scandir($dir);
		$this->fileType = $type;

		foreach ($this->dictList as $key => $file) {
			if (substr($file, -(strlen($type))) != $type) {
				unset($this->dictList[$key]);
			} else {
				# We need to take one more than the length of the $type to account for the '.'
				$this->dictList[$key] = substr($file, 0, -(strlen($type)+1));
			}
		}
    }

    /**
    * Returns the list of dictionary file names
    */
    public function getDictList()
    {
    	return $this->dictList;
    }

    /**
    * Reads the list of values from the dictionary
    *  If $file is 'any' all the files in the dictionary directory will be read
    * Returns an array of values read from the dictionary.
    */
    public function readFromDict($file, $key)
    {
    	$valueList = array();

    	if ($file == 'any')
    	{
			foreach ($this->dictList as $dictKey => $dictName)
			{
                $valueList = array_merge($valueList,$this->readDictFile($dictName,$key));
			}

            # de-duplicate the list in case multiple files have the same name in them
			$valueList = array_unique($valueList);

		} else {
			$valueList = $this->readDictFile($file,$key);
		}
    	return $valueList;
    }

    /*
    * Reads a single file from the dictionary directory.
    * Returns an array of values read from the dictionary, if the file does not exist it returns an empty array
    */
    private function readDictFile($file,$key)
    {
        $filePath = $this->dictDirectory.$file.'.'.$this->fileType;
        if (!file_exists($filePath))
        {
            return array();
        } else {
            $dictionaryFile = file_get_contents($filePath);
            # currently only json is supported, but other file types might be added in the future
            if ($this->fileType == 'json')
            {
                $dictionary = json_decode($dictionaryFile,true);
            }

            return $dictionary[$key];
        }
    }

    /**
    * Outputs the file name in a user friendly manner.
    * Replaces the delimiter (default is '_') with spaces and upper cases each word.
    *  Example: game_of_thrones -> Game Of Thrones
    */
    public function fileNameExternal($filename, $del = '_')
    {
		$filename = str_replace($del, " ", $filename);
		return ucwords($filename);
	}

    /**
    * Outputs the file name for a given key
    * Optionally converts to external form
    */
    public function getFileName($key, $external = false)
    {
        if ($external)
        {
            return $this->fileNameExternal($this->dictList[$key]);
        } else {
            return $this->dictList[$key];
        }
    }
}
