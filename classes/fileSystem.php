<?php

class FileSystem
{
	//	Note
	//	Absolute path is divide into 2 things
	//		(a) System Absolute path is "//" because if root url was `/root/` instead of `/`
	//		then we would have an absolute path like `/root/usr/yellow` or `//usr/yellow`
	//		(b) User-friendly Absolute path converts "//" into "/" for simplificity

	// .. and .........n+1 are treated the same

	// For rmdir
	//	Given you have /usr/fun/john
	//	if your on /usr/ and attempt rmdir(`/usr/fun`) it will fail bc your on /usr/
	//	if your not on /usr/ and attempt rmdir(`/usr/fun`) it will only remove /fun/ and /fun/john/ but not /usr/


	const ERROR_DIRECTORY_ALREADY_EXIST = "Error: '%s' directory already exist \n";
	const ERROR_PATH_DOES_NOT_EXIST = "Error: '%s' path does not exist in the file system \n";
	const ERROR_CANNOT_REMOVE_SAME_DIRECTORY_YOUR_IN = "Error: cannot remove directory that you are currently in \n";
	const ERROR_CANNOT_REMOVE_NON_EXISTANCE_DIRECTORY = "Error: cannot remove directory that doesn't exist \n";

	const NOT_SUPPORTED_MIXING_BACKTRACE_WITH_NON_BACKTRACE = "Error: we current support either '..' (backtrace) or non-backtracing, we do not support mixing of these 2 \n";

	private $file_system = [];
	private $current_path = '';

	public function __construct()
	{
		//	Initialize root path

		/**
			for path: /usr/share/info/doc.txt
			$file_system = [
				"/" => [
					"usr" => [
						"share" => [
							"info" => [
								"doc.txt"
							]
						]
					]
				]
			];
		**/
		//	NOTE: do NOT change this variable name
		//	Because we use it in the eval() function
		$this->file_system = [
			'/' => []
		];

		//	Initialie current path to root
		$this->current_path = '/';
	}

	public function cd($path) 
	{
		if (strpos($path, '..') !== false) {
			//	Check if we are doing back-tracing
			//	Also check if its doing a mix of back-tracing and not-backtracing
			preg_match('/(.*[a-zA-Z].*)/', $path, $matches);

			if (count($matches) >= 1) {
				echo self::NOT_SUPPORTED_MIXING_BACKTRACE_WITH_NON_BACKTRACE;
				return false;
			}

			//	If path is backtracing (..) then adjust the new path

			//	Adjust url for '..' (back)
			//	the "-1" fix the issue where explode creates an empty index at the start
			$number_of_back_trace = count(explode('..', $path)) - 1;

			$current_path_array = explode('/', $this->current_path);

			for($i=0; $i<=$number_of_back_trace; $i++) {
				array_pop($current_path_array);
				$number_of_back_trace--;

				if ($number_of_back_trace === 0 || count($current_path_array) === 0) {
					break;
				}
			}

			if (count($current_path_array) === 0) {
				//	Were back to the root because we back-traced beyond the level of current directories
				return $this->current_path = '/';
			}

			$path = implode("/", $current_path_array);
		}

		$path = $this->getAbsolutePath($path);

		//	Check if path exist;
		$array_index_for_path = $this->getArrayIndexFromPath($path);

		$code = '$path_exist = isset($this->file_system'.$array_index_for_path.')? true:false;';
		eval($code);

		if (!$path_exist) {
			echo sprintf(self::ERROR_PATH_DOES_NOT_EXIST, substr($path, 1));
			return false;
		}

		$this->current_path = $path;
	}

	public function pwd()
	{
		//	Don't show root (used substr)
		return substr($this->current_path, 1);
	}

	public function mkdir($path)
	{	
		$absolute_path = $this->getAbsolutePath($path);
		$is_exist = $this->doesPathExist($absolute_path);

		//	Check if directory exist
		if ($is_exist) {
			echo sprintf(self::ERROR_DIRECTORY_ALREADY_EXIST, $path);
			return false;
		}

		$array_index_for_path = $this->getArrayIndexFromPath($absolute_path);
		$code = '$this->file_system'.$array_index_for_path.'=[];';
		eval($code);
	}

	public function rmdir($path)
	{
		$absolute_path = $this->getAbsolutePath($path);
		$is_exist = $this->doesPathExist($absolute_path);

		//	Check if directory exist
		if (!$is_exist) {
			echo sprintf(self::ERROR_CANNOT_REMOVE_NON_EXISTANCE_DIRECTORY, $directory_array[0]);
			return false;
		}

		//	Check if your on the directory that your trying to delete
		if ($this->current_path === $absolute_path) {
			echo self::ERROR_CANNOT_REMOVE_SAME_DIRECTORY_YOUR_IN;
			return false;
		}

		$array_index_for_path = $this->getArrayIndexFromPath($absolute_path);
		$code = 'unset($this->file_system'.$array_index_for_path.');';
		eval($code);
	}

	public function dumpFileSystem()
	{
		print_r($this->file_system);
	}

	private function getAbsolutePath($path)
	{
		//	Trim any ending slash
		$path = rtrim($path, '/');
		
		//	The substr handles the case where the path being passed in is the system absolute path
		if (substr($path, 0,2) !== '//' && $path[0] === '/') {
			//	Translate "/" into our system root path "//"
			$path = '/'. $path;
		}

		if ($path[0] !== '/') {
			//	Check if the path given is a relative path
			//	If so then convert to absolute path
			$path = $this->current_path . "/". $path;
		}

		return $path;
	}

	private function doesPathExist($path) 
	{
		$array_index_for_current_path = $this->getArrayIndexFromPath($path);

		$code = '$is_exist = isset($this->file_system'.$array_index_for_current_path.')? true: false;';
		eval($code);

		return $is_exist;
	}

	private function getArrayIndexFromPath($path)
	{
		if ($path === '/') {
			return '["/"]';
		}

		$path_without_root = substr($path, 2);
		$path_array = explode("/", $path_without_root);
		return '["/"]["'.implode('"]["', $path_array).'"]';
	}
}

?>