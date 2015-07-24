<?php
/**
 * Title: Application for download (with auth) files from Bitrix sites
 * Author: Aleksandr Novikov
 * Email: pwsdotru@gmail.com
 * WWW: http://pwsdotru.com/
 * GIT: https://github.com/pwsdotru/bitrix_downloader
 */
define("VERSION", "1.0");
error_reporting(E_ALL);

$params = array(
  "V|version" => "Display version and exit",
  "h|help" => "Display this help message"
);
$script_filename = basename($argv[0]);

//Run without parameters
if ($argc == 1) {
  show_banner($script_filename);
} else {
  $arguments = parse_arguments($argv, $keys = parse_keys($params));
  if ($arguments && count($arguments) > 0) {
    if (isset($arguments["h"]) || isset($arguments["help"])) {
      show_banner($script_filename, false);
    }
  } else {
    show_banner($script_filename);
  }
}
exit();

/**
 * FUNCTIONS
 */

/**
 * Show information text about application
 * @param $appname - current script file name
 * @param bool $short - show short info or with details
 */
function show_banner($appname, $short=true) {
  echo("Bitrix Downloader " . VERSION . "\n");
  echo("\nUsage: " . $appname . " [OPTIONS] [URL]\n");
  if ($short) {
    echo("Try '" . $appname . " --help' for more information\n");
  } else {
    echo("Options:\n\n");
  }
}

/**
 * Parse arguments of command line
 * @param $cmd - arguments from command line
 * @param $keys - list of possible keys
 * @return array - key is param name, value - param value (if used)
 */
function parse_arguments($cmd, $keys) {
  $out = array();

  return $out;
}

/**
 * Build array with keys for parse arguments list
 * @param $params - list of possible params
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