<?php

class FileSystem {
  //  Note
  //  Absolute path is divide into 2 things
  //    (a) System Absolute path is "//" because if root url was `/root/` instead of `/`
  //    then we would have an absolute path like `/root/usr/yellow` or `//usr/yellow`
  //    (b) User-friendly Absolute path converts "//" into "/" for simplificity

  // .. and .........n+1 are treated the same

  //  Note:
  //  Symlink takes priority over hard link
  //  -- Given /usr/ming/y2k
  //  --- If I have symlink('/usr/ming/y2k', 'ming')
  //  ---- If I am at root and cd('usr'), then it will go to '/usr/ming/y2k' instead of '/usr/ming'
  //  ----- Please use removeSymLink to undo it
  // For rmdir
  //  Given you have /usr/fun/john
  //  if your on /usr/ and attempt rmdir(`/usr/fun`) it will fail bc your on /usr/
  //  if your not on /usr/ and attempt rmdir(`/usr/fun`) it will only remove /fun/ and /fun/john/ but not /usr/

  //  not supported mkdir with symlink because it gets strange in a way of
  //  Given symlink of ('usr/share/want', 'foo') and if we cd into `/usr/share` and we do mkdir('foo')
  //  (a) do we create /usr/share/foo? or
  //  (b) do try to create /usr/share/want which is already created so it will throw an error?

  const ERROR_DIRECTORY_ALREADY_EXIST = "Error: '%s' directory already exist";
  const ERROR_PATH_DOES_NOT_EXIST = "Error: '%s' path does not exist in the file system";
  const ERROR_CANNOT_REMOVE_SAME_DIRECTORY_YOUR_IN = "Error: cannot remove directory that you are currently in";
  const ERROR_CANNOT_REMOVE_NON_EXISTANCE_DIRECTORY = "Error: cannot remove directory that doesn't exist";
  const ERROR_INVALID_DIRECTORY_NAME = "Error: directory path can only contain alphabetic and '/' characters";
  const ERROR_SYMLINK_SOURCE_DIRECTORY_NOT_FOUND = "Error: unable to symlink due to source directory not found"; 
  const ERROR_DUPLICATE_SYMLINK = "Error: symlink already exist";
  const ERROR_CANNOT_REMOVE_SYMLINK_NOT_EXIST = "Error: unable to remove symlink due to it can't be found";

  const NOT_SUPPORTED_MIXING_BACKTRACE_WITH_NON_BACKTRACE = "Error: we current support either '..' (backtrace) or non-backtracing, we do not support mixing of these 2";


  const SYMLINK_ALIAS = 'alias';
  const SYMLINK_ABSOLUTE_PATH = 'absolute_path';

  /**
      example for path: /usr/share/info/doc.txt
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
  //  NOTE: do NOT change this variable name
  //  Because we use it in the eval() function
  private $file_system = [];
  private $current_path = '';


  /**
    example for symlink: (/usr/share, bin)
    $sym_links = [
      "bin" => [
        "alias" = /usr/share",
        "absolute_path" = //usr/share
      ]
    ];
  **/
  private $sym_links = [];

  public function __construct() {
    //  Initialize root path
    $this->file_system = [
      '/' => []
    ];

    //  Initialize current path to root
    $this->current_path = '/';
  }

  public function cd($path) {

    //  Try loading path from symlink
    $path = $this->loadFromSymlink($path);

    //  Check if we are doing a back-tracing
    //  notice: were using !== instead of !=
    //  this is because strpos can return 0 if `..` is found in the [zero-th index]
    //  0 == false but 0 === false (is false)
    if (strpos($path, '..') !== false) {
      //  Check if we are doing back-tracing
      //  Also check if its doing a mix of back-tracing and not-backtracing
      if (preg_match('/(.*[a-zA-Z].*)/', $path)) {
        throw new Exception(self::NOT_SUPPORTED_MIXING_BACKTRACE_WITH_NON_BACKTRACE);
      }

      //  If path is backtracing (..) then adjust the new path

      //  Adjust url for '..' (back)
      //  the "-1" fix the issue where explode creates an empty index at the start
      $number_of_back_trace = count(explode('..', $path)) - 1;

      $current_path_array = explode('/', $this->current_path);

      //  Need array_shift 2x here because explode generates 2 empty string at [0] and [1]
      //  if current_path => '/' means were in root, [0] & [1] will be empty string
      //  if current_path => '//usr', [0] & [1] will be empty while [2] will be usr
      array_shift($current_path_array);
      array_shift($current_path_array);

      for($i=0; $i<=$number_of_back_trace; $i++) {
        array_pop($current_path_array);
        $number_of_back_trace--;

        if ($number_of_back_trace === 0 || count($current_path_array) === 0) {
          break;
        }
      }

      if (count($current_path_array) === 0) {
        //  Were back to the root because we back-traced beyond the level of current directories
        return $this->current_path = '/';
      }

      $path = $this->getAbsolutePath("/".implode("/", $current_path_array));

      return $this->current_path = $path;
    }

    $path = $this->getAbsolutePath($path);

    //  Check if path exist;
    $array_index_for_path = $this->getArrayIndexFromPath($path);

    $code = '$path_exist = isset($this->file_system'.$array_index_for_path.')? true:false;';
    eval($code);

    if (!$path_exist) {
      throw new Exception(sprintf(self::ERROR_PATH_DOES_NOT_EXIST, substr($path, 1)));
    }

    $this->current_path = $path;
  }

  public function pwd() {
    $path = $this->current_path;
    $current_path_length = strlen($path);

    //  find the symlink that will make our returned pwd path the shortest
    foreach($this->sym_links as $key=>$sym_link) {
      $proposed_path = str_replace($sym_link[self::SYMLINK_ALIAS], $key, $this->current_path);

      if (strlen($proposed_path) < strlen($path)) {
        $path = $proposed_path;
      }
    }

    $result = substr($path, 1);

    //  If result is empty that means were on root path
    //  In that case, just return '/'
    return $result === '' ? '/': $result;
  }

  public function mkdir($path) {  
    //  Check if path contains other characters that not alphabetic nor '/'
    if (preg_match('/[^a-zA-Z\/]/', $path)) {
      throw new Exception(self::ERROR_INVALID_DIRECTORY_NAME);
    }

    $absolute_path = $this->getAbsolutePath($path);

    //  If directory exist, you cannot create the same one again
    if ($this->doesPathExist($absolute_path)) {
      throw new Exception(sprintf(self::ERROR_DIRECTORY_ALREADY_EXIST, $path));
    }

    $array_index_for_path = $this->getArrayIndexFromPath($absolute_path);
    $code = '$this->file_system'.$array_index_for_path.'=[];';
    eval($code);
  }

  public function rmdir($path) {
    //  Try loading path from symlink
    $absolute_path = $this->loadFromSymlink($path);

    //  Need to be called in case there is no symlink found
    $absolute_path = $this->getAbsolutePath($absolute_path);

    //  If directory exist, you can remove it
    if (!$this->doesPathExist($absolute_path)) {
      throw new Exception(sprintf(self::ERROR_CANNOT_REMOVE_NON_EXISTANCE_DIRECTORY, $directory_array[0]));
    }

    //  Check if your on the directory that your trying to delete
    if ($this->current_path === $absolute_path) {
      throw new Exception(self::ERROR_CANNOT_REMOVE_SAME_DIRECTORY_YOUR_IN);
    }

    $array_index_for_path = $this->getArrayIndexFromPath($absolute_path);
    $code = 'unset($this->file_system'.$array_index_for_path.');';
    eval($code);

    //  Remove all symlink related to that directory we removed
    foreach($this->sym_links as $key=>$sym_link) {
      if (strpos($sym_link[self::SYMLINK_ABSOLUTE_PATH], $absolute_path) !== false) {
        unset($this->sym_links[$key]);
      }
    }
  }

  public function symlink($source, $dest) {
    // Trim ending slash
    $source = rtrim($source, '/');

    $absolute_path = $this->getAbsolutePath($source);

    //  If directory does not exist, you can't symlink it
    if (!$this->doesPathExist($absolute_path)) {
      throw new Exception(self::ERROR_SYMLINK_SOURCE_DIRECTORY_NOT_FOUND);
    }

    //  If symlink already exist, don't create another one
    if ($this->doesSymlinkExist($source)) {
      throw new Exception(self::ERROR_DUPLICATE_SYMLINK);
    }

    $this->sym_links[$dest] = [
      self::SYMLINK_ALIAS => $source,
      self::SYMLINK_ABSOLUTE_PATH => $absolute_path
    ];
  }

  public function removeSymLink($link) {
    //  Only remove if the symlink actually exist
    if (!isset($this->sym_links[$link])) {
      throw new Exception(self::ERROR_CANNOT_REMOVE_SYMLINK_NOT_EXIST);
    }

    unset($this->sym_links[$link]);
  }

  public function dumpFileSystem() {
    return $this->file_system;
  }

  private function getAbsolutePath($path) {
    //  Trim any ending slash
    $path = rtrim($path, '/');
    
    //  The substr handles the case where the path being passed in is the system absolute path
    if (substr($path, 0,2) !== '//' && $path[0] === '/') {
      //  Translate "/" into our system root path "//"
      $path = '/'. $path;
    }

    if ($path[0] !== '/') {
      //  Check if the path given is a relative path
      //  If so then convert to absolute path
      $path = $this->current_path . "/". $path;
    }

    return $path;
  }

  private function doesPathExist($path) {
    $array_index_for_current_path = $this->getArrayIndexFromPath($path);

    $code = '$is_exist = isset($this->file_system'.$array_index_for_current_path.')? true: false;';
    eval($code);

    return $is_exist;
  }

  private function getArrayIndexFromPath($path) {
    if ($path === '/') {
      return '["/"]';
    }

    $path_without_root = substr($path, 2);
    $path_array = explode("/", $path_without_root);
    return '["/"]["'.implode('"]["', $path_array).'"]';
  }

  private function doesSymlinkExist($alias) {
    foreach($this->sym_links as $symlink) {
      if ($alias === $symlink[self::SYMLINK_ALIAS]) {
        return true;
      }
    }

    return false;
  }

  private function loadFromSymlink($path) {
    $directory_array = explode("/", $path);
    $first_directory = array_shift($directory_array);

    //  Check if its a symlink
    if (isset($this->sym_links[$first_directory])) {
      $path = $this->sym_links[$first_directory][self::SYMLINK_ABSOLUTE_PATH];
      $path = $path.'/'.implode("/", $directory_array);
    }

    return $path;
  }
}

?>