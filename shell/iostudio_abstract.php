<?php

require 'abstract.php';

/**
 * Description
 *
 * @author
 * @copyright
 * @package
 * @subpackage
 * @version
 *
 */
abstract class Iostudio_Shell_Abstract extends Mage_Shell_Abstract
{

  /**
   * Display a message on the command line for the user, if you pass the
   * argument --quite when you run a script, then it will not output any message
   *
   * @param string $message
   */
  protected function log($message)
  {
    if (!$this->getArg('quite'))
    {
      echo $message . PHP_EOL;
    }
  }
  
  /**
   * Will ask a user a question and based on the users response will return true
   * or false. True means the user answered yes, false means the user replied with
   * no.
   *
   * If you pass the option --no-confirmation then it will use the default
   *
   * @param string $message
   * @return boolean
   */
  protected function confirm($message='', $default=false)
  {
    if (!$this->getArg('no-confirmation'))
    {
      echo $message . PHP_EOL;
      $response = trim(fgets(STDIN));

      if (in_array($response, array('y', 'yes')))
      {
        return true;
      }

      if (in_array($response, array('n', 'no')))
      {
        return false;
      }
    }
    
    return $default;
  }

  /**
   * Retrieve Usage Help Message
   *
   * @return string
   */
  public function usageHelp()
  {
    $usage = <<<USAGE
Usage:  php -f iostudio_SCRIPTNAME.php -- [options]

  -h            Short alias for help
  help          This help

Options
=======

  --quite
      Do not spit out debug/info statements

  --no-confirmation
      Use the default answer for confirmation questions

USAGE;
    return $usage . PHP_EOL;
  }
}