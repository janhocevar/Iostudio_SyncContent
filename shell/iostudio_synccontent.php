<?php

require_once 'iostudio_abstract.php';

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
class Iostudio_Shell_SyncContent extends Iostudio_Shell_Abstract
{

  /**
   * 
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

      $cmd = strtr('rsync %dry-run% %rsync-options% -e "ssh -p%ssh-port%" %local-dir%/ %username%@%host%:%remote-dir%/', array(
          '%dry-run%'       => $this->getArg('dry-run') ? '--dry-run' : '',
          '%rsync-options%' => Mage::getStoreConfig(sprintf('synccontent/%s/rsync_options', $syncTo)),
          '%ssh-port%'      => Mage::getStoreConfig(sprintf('synccontent/%s/port', $syncTo)),
          '%username%'      => $username,
          '%host%'          => $host,
          '%remote-dir%'    => $remote_dir,
          '%local-dir%'     => Mage::getBaseDir(),
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
      
      $cmd = strtr('rsync %dry-run% %rsync-options% -e "ssh -p%ssh-port%" %username%@%host%:%remote-dir%/ %local-dir%/', array(
          '%dry-run%'       => $this->getArg('dry-run') ? '--dry-run' : '',
          '%rsync-options%' => Mage::getStoreConfig(sprintf('synccontent/%s/rsync_options', $syncFrom)),
          '%ssh-port%'      => Mage::getStoreConfig(sprintf('synccontent/%s/port', $syncFrom)),
          '%username%'      => Mage::getStoreConfig(sprintf('synccontent/%s/username', $syncFrom)),
          '%host%'          => Mage::getStoreConfig(sprintf('synccontent/%s/host', $syncFrom)),
          '%remote-dir%'    => Mage::getStoreConfig(sprintf('synccontent/%s/dir', $syncFrom)),
          '%local-dir%'     => Mage::getBaseDir(),
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
   */
  public function usageHelp()
  {
    $usage = <<<USAGE
Usage:  php -f iostudio_synccontent.php [options]

Example of how to sync code from the server you are currently on to the production
server:

  php -f iostudio_synccontent.php --sync-to production

Example of how to pull files from another server to the server you are currently
on:

  php -f iostudio_synccontent.php --sync-from production


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

  --dry-run
      Pass this option if you want the rsync to just do a dry run and not actually
      sync files.

  --quite
      Do not spit out debug/info statements

  --no-confirmation
      Use the default answer for confirmation questions

USAGE;
    return $usage . PHP_EOL;
  }

}

$shell = new Iostudio_Shell_SyncContent();
$shell->run();