<?php

require "classes/fileSystem.php";

class VirtualTerminal {

	const INPUT_HELP = 'help';
	const INPUT_PWD = 'pwd';
	const INPUT_CD = 'cd';
	const INPUT_MKDIR = 'mkdir';
	const INPUT_RMDIR = 'rmdir';
	const INPUT_SYMLINK = 'symli';
	const INPUT_REMOVE_SYMLINK = 'remov';
	const INPUT_ADD_FILE = 'addfi';
	const INPUT_DELETE_FILE = 'delfi';
	const INPUT_LIST_FILE_BY_FOLDER = 'listf';
	const INPUT_LIST_FILE_IN_CURRENT_DIRECTORY = 'list';
	const INPUT_DUMP_FILE_SYSTEM = 'dump';
	const INPUT_QUIT = 'quit';

	const ERROR_INVALID_COMMAND = "Invalid Command: %s";
	const ERROR_INVALID_SYNTAX_FOR_CD = 'Invalid Syntax for `cd`, correct syntax is cd(path)';
	const ERROR_INVALID_SYNTAX_FOR_MKDIR = 'Invalid Syntax for `mkdir`, correct syntax is mkdir(path)';
	const ERROR_INVALID_SYNTAX_FOR_RMDIR = 'Invalid Syntax for `rmdir`, correct syntax is rmdir(path)';
	const ERROR_INVALID_SYNTAX_FOR_SYMLINK = 'Invalid Syntax for `symlink`, correct syntax is symlink(source, dest)';
	const ERROR_INVALID_SYNTAX_FOR_REMOVE_SYMLINK = 'Invalid Syntax for `removeSymlink`, correct syntax is removeSymLink(link)';
	const ERROR_INVALID_SYNTAX_FOR_ADD_FILE = 'Invalid Syntax for `addfile`, correct syntax is addfile(file)';

	const DIRECTOR_CREATED = "Directory created %s";
	const DIRECTOR_REMOVED = "Directory removed %s";
	const SYMLINK_SUCCESS = "Symlink %s is successfully set";
	const SYMLINK_REMOVED = "Symlink %s is successfully removed";
	const CURRENT_PATH_CHANGED = "Current Path successfully changed";
	const FILE_ADDED = "File %s successfully added";

	private $file_system = null;

	public function __construct() {
		$this->file_system = new FileSystem();
	}

	public function read($input) {
		$input = trim($input);
		
		if (substr($input, 0, 2) === self::INPUT_CD) {
			return $this->executeCd($input);
		}

		$first_five_character_of_input = substr($input, 0, 5);

		switch ($first_five_character_of_input) {
			case self::INPUT_HELP:
				$this->executeHelp();
				break;
			case self::INPUT_PWD:
				$this->executePwd();
				break;
			case self::INPUT_MKDIR:
				$this->executeMkdir($input);
				break;
			case self::INPUT_RMDIR:
				$this->executeRmdir($input);
				break;
			case self::INPUT_SYMLINK:
				$this->executeSymlink($input);
				break;
			case self::INPUT_REMOVE_SYMLINK:
				$this->executeRemoveSymlink($input);
				break;
			case self::INPUT_ADD_FILE:
				$this->executeAddFile($input);
				break;
			case self::INPUT_DUMP_FILE_SYSTEM:
				$this->executeDumpFileSystem();
				break;
			case self::INPUT_QUIT:
				$this->executeQuit();
				break;
			default:
				$this->displayMessage(sprintf(self::ERROR_INVALID_COMMAND, $input));
				break;
		}
	}

	public function executeHelp() {
		$this->displayMessage('The following command are supported:');
		$this->displayMessage('pwd : show current path');
		$this->displayMessage('mkdir(directory) : create new directory');
		$this->displayMessage('rmdir(directory) : remove existing directory');
		$this->displayMessage('cd(path) : change location to specified path');
		$this->displayMessage('symlink(source, dest) : add symlink');
		$this->displayMessage('removeSymlink(link) : remove symlink');
		$this->displayMessage('addfile(file) : add file to current directory');
		$this->displayMessage('dump : For debugging, dump the current file_system memory');
		$this->displayMessage('quit : Exit virtual terminal');
	}

	public function executePwd() {
		$this->displayMessage($this->file_system->pwd());
	}

	public function executeCd($input) {
		$argument = $this->getArgument($input);

		if(count($argument) !== 1 || $argument[0] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_CD);
		}

		try {
			$this->file_system->cd($argument[0]);
		} catch(Exception $e) {
			return $this->displayMessage($e->getMessage());
		}

		$this->displayMessage(self::CURRENT_PATH_CHANGED);
	}

	public function executeMkdir($input) {
		$argument = $this->getArgument($input);
		
		if(count($argument) !== 1 || $argument[0] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_MKDIR);
		}

		try {
			$this->file_system->mkdir($argument[0]);
		} catch(Exception $e) {
			return $this->displayMessage($e->getMessage());
		}
		
		$this->displayMessage(sprintf(self::DIRECTOR_CREATED, $argument[0]));
	}

	public function executeRmdir($input) {
		$argument = $this->getArgument($input);
		
		if(count($argument) !== 1 || $argument[0] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_RMDIR);
		}

		try {
			$this->file_system->rmdir($argument[0]);
		} catch(Exception $e) {
			return $this->displayMessage($e->getMessage());
		}
		
		$this->displayMessage(sprintf(self::DIRECTOR_REMOVED, $argument[0]));
	}

	public function executeSymlink($input) {
		$argument = $this->getArgument($input);
		
		if(count($argument) !== 2 || $argument[0] === '' || $argument[1] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_SYMLINK);
		}

		try {
			$this->file_system->symlink($argument[0], $argument[1]);
		} catch(Exception $e) {
			return $this->displayMessage($e->getMessage());
		}
		
		$this->displayMessage(sprintf(self::SYMLINK_SUCCESS, $argument[1]));
	}

	public function executeRemoveSymlink($input) {
		$argument = $this->getArgument($input);
		
		if(count($argument) !== 1 || $argument[0] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_REMOVE_SYMLINK);
		}

		try {
			$this->file_system->removeSymLink($argument[0]);
		} catch(Exception $e) {
			return $this->displayMessage($e->getMessage());
		}
		
		$this->displayMessage(sprintf(self::SYMLINK_REMOVED, $argument[0]));
	}

	public function executeAddFile($input) {
		$argument = $this->getArgument($input);
		
		var_dump($argument);
		if(count($argument) !== 1 || $argument[0] === '') {
			return $this->displayMessage(self::ERROR_INVALID_SYNTAX_FOR_ADD_FILE);
		}

		$this->file_system->addFile($argument[0]);
		$this->displayMessage(sprintf(self::FILE_ADDED, $argument[0]));
	}

	public function executeDumpFileSystem() {
		print_r($this->file_system->dumpFileSystem());
	}

	public function executeQuit() {
		$this->displayMessage("Goodbye");
		exit;
	}

	private function getArgument($input) {
		$input_array = explode("(", $input);
		$argument_string = $input_array[1];

		$argument_string = preg_replace('/[^a-zA-Z0-9,.\/]/', '', $argument_string);
		
		return explode(",", $argument_string);
	}

	private function displayMessage($message) {
		echo ">> {$message} \n";
	}
}
?>
