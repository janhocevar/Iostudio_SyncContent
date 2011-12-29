#!/usr/bin/php
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

class Iostudio_Shell_Mysqlload extends Iostudio_Shell_Abstract
{
  /**
   * mysqldump command options, only used if taking a backup before an import
   *
   * @var string
   */
  protected $_mysqldumpOptions = '--skip-opt --add-drop-table --create-options --disable-keys --extended-insert --set-charset';

  /**
   * Will take input from someplace else and load it into the database
   * 
   */
  public function run()
  {
    /* @var $db Mage_Core_Model_Resource_Resource */
    $db = Mage::getResourceSingleton('core/resource');
    $config = $db->getReadConnection()->getConfig();
    
    if ($this->getArg('backup'))
    {
      try
      {
        $backupDb = Mage::getModel('backup/db');
        $backup = Mage::getModel('backup/backup')
            ->setTime(time())
            ->setType('db')
            ->setPath(Mage::getBaseDir("var") . DS . "backups");

        Mage::register('backup_model', $backup);

        $backupDb->createBackup($backup);
      }
      catch (Exception $e)
      {
        throw new Mage_Exception($e->getMessage());
      }
    }

    $cmd = sprintf('mysql -u %s -p%s -h "%s" "%s"',
      $config['username'],
      $config['password'],
      $config['host'],
      $config['dbname']
    );
    passthru($cmd,$return_var);

    if ($this->getArg('unsecure-base-url'))
    {
      /* @var $modelConfig Mage_Core_Model_Config */
      $modelConfig = Mage::getModel('core/config');
      $modelConfig->saveConfig('web/unsecure/base_url', $this->getArg('unsecure-base-url'));
    }

    if ($this->getArg('secure-base-url'))
    {
      /* @var $modelConfig Mage_Core_Model_Config */
      $modelConfig = Mage::getModel('core/config');
      $modelConfig->saveConfig('web/secure/base_url', $this->getArg('secure-base-url'));
    }
  }

  /**
   * Retrieve Usage Help Message
   *
   * @return string
   */
  public function usageHelp()
  {
    $usage = <<<USAGE
Usage:  php -f iostudio_mysqlload.php -- [options]

  -h            Short alias for help
  help          This help

Options
=======

  --unsecure-base-url [http://www.example.com]
      This makes sure that the fields in the database contain the new information
      for your store.

  --secure-base-url [https://www.example.com]
      This makes sure that the fields in the database contain the new information
      for your store.

  --backup
      Passing this option will take a database backup before importing. This file
      is located in var/backups and can also be downloaded in the magento backend
      by going to System > Tools > Backups

  --quite
      Do not spit out debug/info statements

  --no-confirmation
      Use the default answer for confirmation questions

USAGE;
    return $usage . PHP_EOL;
  }
}

$shell = new Iostudio_Shell_Mysqlload();
$shell->run();