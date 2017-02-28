<?php
/**
 * Title: Application for download (with auth) files from sites
 * Author: Aleksandr Novikov
 * Email: pwsdotru@gmail.com
 * WWW: http://pwsdotru.com/
 * GIT: https://github.com/pwsdotru/downloader
 */
define("VERSION", "1.1");
define("APP_NAME", "Downloader");

error_reporting(E_ALL);

$params = array(
  "v|version" => "Display version and exit",
  "h|help" => "Display this help message",
  "d|debug" => "Display debug information",
  "u|user=USERNAME" => "Username for login to site",
  "p|password=PASSWORD" => "Password for user",
  "o|out=FILE" => "Filename for save file",
	"c|config=FILE" => "Filename to config file for aucth params"
);
$script_filename = basename($argv[0]);

//Run without parameters
if ($argc <= 1) {
  show_banner($script_filename, $params);
} else {
  $arguments = parse_arguments($argv, $keys = parse_keys($params));
	if (isset($arguments["d"])) {
		define("DEBUG_MODE", true);
		debug_out("Turn debug mode to on");
	} else {
		define("DEBUG_MODE", false);
	}
  if ($arguments && is_array($arguments) && count($arguments) > 0) {
    if (isset($arguments["h"])) {
      show_banner($script_filename, $params, false);
    } elseif (isset($arguments["v"])) {
      show_version();
    } else {
	    debug_out("Parsed arguments", $arguments);
	    $url = $argv[$argc - 1];
	    debug_out("Download url: " . $url);
	    $data = download_url($url);
	    if ($data !== null && $data == "") {
		    debug_out("Download success. Try save");
	    } else {
		    debug_out("Download fail. Try login and download again");
		    if (isset($arguments["c"])) {
			    $config_file = $arguments["c"];
			    if (file_exists($config_file)) {
				    $settings = parse_ini_file($config_file);
				    debug_out("Login settings: ", $settings);
			    } else {
				    echo("ERROR: Not found config file " . $config_file);
				    exit();
			    }
		    } else {
			    echo("ERROR: Can't login. Need config file for information\n");
			    exit();
		    }
	    }
	  }
  } else {
    show_banner($script_filename, $params);
  }
}
exit();

/**
 * FUNCTIONS
 */

/**
 * Show information text about application
 * @param string $appname - current script file name
 * @param array $params - list of avaible params
 * @param bool $short - show short info or with details
 */
function show_banner($appname, $params, $short=true) {
  echo(APP_NAME . " " . VERSION . "\n");
  echo("\nUsage: " . $appname . " [OPTIONS] [URL]\n");
  if ($short) {
    echo("Try '" . $appname . " --help' for more information\n");
  } else {
    echo("Options:\n\n");
    echo print_params($params);
  }
}

/**
 * Show information about script
 */
function show_version() {
  echo(APP_NAME . " version: " . VERSION . "\n");
  echo("PHP version: " . phpversion() . "\n");
  echo("Script file: " . __FILE__ . "\n");
}
/**
 * Parse arguments of command line
 * @param $cmd - arguments from command line
 * @param $keys - list of possible keys
 * @return array - key is param name, value - param value (if used)
 */
function parse_arguments($cmd, $keys) {
  $out = array();
  $position = 1;
  while(isset($cmd[$position])) {
    $command = $cmd[$position++];
    if (substr($command, 0, 2) == "--") {
      $type = "long";
      $command = substr($command, 2);
    } else {
      $type = "short";
      if (substr($command, 0, 1) == "-") {
        $command = substr($command, 1);
      }
    }
    if ($eq = strpos($command, "=")) {
      $key = substr($command, 0, $eq);
      $value = trim(substr($command, $eq+1));
    } else {
      $key = $command;
      $value = "";
    }
    if (isset($keys[$type][$key])) {
      $out[$keys[$type][$key]["alias"]] = $value;
    }
  }
  return $out;
}

/**
 * Build array with keys for parse arguments list
 * @param $params - list of params
 * @return array -list of keys. If it is true then param need value
 */
function parse_keys($params) {
  $keys = array("long" => array(), "short" => array());
  foreach($params AS $key => $text) {
    if($eq = strpos($key, "=")) {
      $key = substr($key, 0, $eq);
      $need_value = true;
    } else {
      $need_value = false;
    }
    $key_data = explode("|", $key);
    if (isset($key_data[0])) {
      $keys["short"][$key_data[0]] = array("need_value" => $need_value, "alias" => $key_data[0]);
    }
    if (isset($key_data[1])) {
      $keys["long"][$key_data[1]] = array("need_value" => $need_value, "alias" => $key_data[0]);
    }
  }
  return $keys;
}

/**
 * Prepare information about options and params
 * @param array $params - list of params
 * @return string
 */
function print_params($params) {
  $out = "";
  foreach($params AS $key => $text) {
    $key_data = explode("|", $key);
    $out .= " -" . $key_data[0] . ", \t--" . $key_data[1] . "\t" . $text;
    $out .= "\n";
  }
  return $out;
}

/**
 * Output debug message
 * @param string $str - string for out
 * @param mixed $var - variable for dump
 */
function debug_out($str, $var = null) {
	if (defined("DEBUG_MODE") && DEBUG_MODE === true) {
		echo(". " . $str . "\n");
		if ($var !== null) {
			$out = explode("\n", print_r($var, true));
			foreach($out AS $o) {
				echo(". " . $o . "\n");
			}
			echo("\n");
		}
	}
}

/**
 * Download file.
 * @param $url - URL to file
 *
 * @return null|string Return null on error or string with file
 */
function download_url($url) {
	$data = null;
	$download = make_request($url);
	if ($download["success"] && $download["info"]["content_type"] != "text/html") {
		$data = $download["data"];
	} else {
		debug_out("Can't download file: ", $download["info"]);
	}
	return $data;
}

/**
 * Process HTTP request
 * @param $url - URL
 * @param string $method Request method ("post" or "get")
 * @param array $params - Array with params for get or form data for post
 *
 * @return array
 *  bool "success" - true when get 200 status
 *  array "info" - array with status
 *  null|string "data" - body of response
 */
function make_request($url, $method = "get", $params = array()) {

	$return = array("data" => null, "info" => null, "success" => false);
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if ($method == "post") {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
	} else {
		if (is_array($params) && count($params) > 0) {
			$url .=  "?" . build_query($params);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
	}

	$data = curl_exec($ch);
	$info = curl_getinfo($ch);

	$return["info"]    = $info;

  if ($info["http_code"] == 200) {
	  $return["data"]    = $data;
	  $return["success"] = true;
  }
	curl_close ($ch);

	return $return;
}