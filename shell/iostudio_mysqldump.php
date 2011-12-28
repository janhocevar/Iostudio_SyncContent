<?php

require 'iostudio_abstract.php';

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

class Iostudio_Shell_Mysqldump extends Iostudio_Shell_Abstract
{

  /**
   * mysqldump command options
   *
   * @var string
   */
  protected $_mysqldumpOptions = '--skip-opt --add-drop-table --create-options --disable-keys --extended-insert --set-charset';

  /**
   *
   */
  public function run()
  {
    /* @var $db Mage_Core_Model_Resource_Resource */
    $db = Mage::getResourceSingleton('core/resource');
    $config = $db->getReadConnection()->getConfig();
    $cmd = sprintf('mysqldump %s -u %s -p%s -h "%s" "%s" %s',
      $this->_mysqldumpOptions,
      $config['username'],
      $config['password'],
      $config['host'],
      $config['dbname'],
      $this->getTablesToDump()
    );

    $this->log(sprintf("You are about to run:\n\t%s",$cmd));

    if ($this->confirm(sprintf('Are you sure you want to dump the database?[y]'), true))
    {
      system($cmd,$return_var);
    }
  }

  /**
   * Will get the tables that need to be synced
   *
   * @return string
   */
  protected function getTablesToDump()
  {
    
  }

  /**
   * Retrieve Usage Help Message
   *
   * @return string
   */
  public function usageHelp()
  {
    $usage = <<<USAGE
Usage:  php -f iostudio_mysqldump.php -- [options]

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

$shell = new Iostudio_Shell_Mysqldump();
$shell->run();