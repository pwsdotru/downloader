<?php
/**
 * Title: Application for download (with auth) files from Bitrix sites
 * Author: Aleksandr Novikov
 * Email: pwsdotru@gmail.com
 * WWW: http://pwsdotru.com/
 * GIT: https://github.com/pwsdotru/bitrix_downloader
 */
define("VERSION", "1.0");
define("APP_NAME", "Bitrix Downloader");

error_reporting(E_ALL);

$params = array(
  "v|version" => "Display version and exit",
  "h|help" => "Display this help message",
  "d|debug" => "Display debug information",
  "u|user=USERNAME" => "Username for login to site",
  "p|password=PASSWORD" => "Password for user",
  "o|out=FILE" => "Filename for save file"
);
$script_filename = basename($argv[0]);

//Run without parameters
if ($argc <= 1) {
  show_banner($script_filename, $params);
} else {
  $arguments = parse_arguments($argv, $keys = parse_keys($params));
  if ($arguments && is_array($arguments) && count($arguments) > 0) {
    if (isset($arguments["h"]) || isset($arguments["help"])) {
      show_banner($script_filename, $params, false);
    }elseif (isset($arguments["v"]) || isset($arguments["version"])) {
      show_version();
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
      $out[$key] = $value;
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
      $keys["short"][$key_data[0]] = $need_value;
    }
    if (isset($key_data[1])) {
      $keys["long"][$key_data[1]] = $need_value;
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