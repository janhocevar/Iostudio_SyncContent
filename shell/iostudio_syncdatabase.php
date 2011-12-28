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

class Iostudio_Shell_SyncDatabase extends Iostudio_Shell_Abstract
{

  /**
   * Will sync the database from one server to another
   */
  public function run()
  {
    if ($syncTo = $this->getArg('sync-to'))
    {
      if (!in_array($syncTo, array('staging', 'production')))
      {
        throw new Mage_Exception(sprintf('The server "%s" is invalid', $syncTo));
      }

      $username   = Mage::getStoreConfig(sprintf('synccontent/%s/username', $syncTo));
      $remote_dir = Mage::getStoreConfig(sprintf('synccontent/%s/dir', $syncTo));
      $host       = Mage::getStoreConfig(sprintf('synccontent/%s/host', $syncTo));

      if (empty($remote_dir) || empty($username) || empty($host))
      {
        throw new Mage_Exception(sprintf('The "%s" server has not been setup',$syncTo));
      }

      $cmd = strtr('./shell/iostudio_mysqldump.php --quite --no-confirmation | ssh -p%port% %username%@%host% \'cd %remote_dir%; ./shell/iostudio_mysqlload.php --backup --quite --no-confirmation\'', array(
          '%port%'      => Mage::getStoreConfig(sprintf('synccontent/%s/port', $syncTo)),
          '%username%'      => $username,
          '%host%'          => $host,
          '%remote_dir%'    => $remote_dir,
        ));
      $this->log(sprintf("You are about to run:\n\t%s",$cmd));
      if ($this->confirm("Are you sure you want to sync content\nFROM this server TO another server?[y]",true))
      {
        system($cmd);
      }
      $this->log('Complete');
    }
    elseif ($syncFrom = $this->getArg('sync-from'))
    {
      if (!in_array($syncFrom, array('staging', 'production')))
      {
        throw new Mage_Exception(sprintf('The server "%s" is invalid', $syncFrom));
      }

      $username   = Mage::getStoreConfig(sprintf('synccontent/%s/username', $syncFrom));
      $remote_dir = Mage::getStoreConfig(sprintf('synccontent/%s/dir', $syncFrom));
      $host       = Mage::getStoreConfig(sprintf('synccontent/%s/host', $syncFrom));

      if (empty($remote_dir) || empty($username) || empty($host))
      {
        throw new Mage_Exception(sprintf('The "%s" server has not been setup',$syncFrom));
      }

      $cmd = strtr('ssh -p%port% %username%@%host% \'cd %remote_dir%; ./shell/iostudio_mysqldump.php --quite --no-confirmation \' | ./shell/iostudio_mysqlload.php --backup --quite --no-confirmation',array(
        '%port%'       => Mage::getStoreConfig(sprintf('synccontent/%s/port',$syncFrom)),
        '%username%'   => $username,
        '%host%'       => $host,
        '%remote_dir%' => $remote_dir,
      ));

      $this->log(sprintf("You are about to run:\n\t%s",$cmd));
      if ($this->confirm("Are you sure you want to sync content\nFROM another server TO this server?[y]",true))
      {
        system($cmd);
      }
      $this->log('Complete');
    }
    else
    {
      echo $this->usageHelp();
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
Usage:  php -f iostudio_syncdatabase.php -- [options]

  -h            Short alias for help
  help          This help

Options
=======

  --sync-to [staging | production]
      Uses rsync to sync content from the server you are currently on to the server
      you have defined in the admin backend.

  --sync-from [staging | production]
      Uses rsync to sync content from another server to the server you are currently
      on.

  --quite
      Do not spit out debug/info statements

  --no-confirmation
      Use the default answer for confirmation questions

USAGE;
    return $usage . PHP_EOL;
  }
}

$shell = new Iostudio_Shell_SyncDatabase();
$shell->run();