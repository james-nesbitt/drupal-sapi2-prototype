<?php

namespace Drupal\sapi;

use Drupal\Core\Config\ConfigManager;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Class SapiDispatcher.
 *
 * @package Drupal\sapi
 */
class SapiDispatcher implements SapiDispatcherInterface {

  /**
   * Drupal\Core\Config\ConfigManager definition.
   *
   * @var \Drupal\Core\Config\ConfigManager
   */
  protected $configManager;

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Drupal\Component\Plugin\PluginManagerInterface definition.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $statisticsPluginManager;

  /**
   * SapiDispatcher constructor.
   *
   * @param \Drupal\Core\Config\ConfigManager $configManager
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   * @param \Drupal\Component\Plugin\PluginManagerInterface $statisticsPluginManager
   */
  public function __construct(ConfigManager $configManager, AccountProxy $currentUser, CurrentRouteMatch $currentRouteMatch, PluginManagerInterface $statisticsPluginManager) {
    $this->configManager = $configManager;
    $this->currentUser = $currentUser;
    $this->currentRouteMatch = $currentRouteMatch;
    $this->statisticsPluginManager = $statisticsPluginManager;
  }

   /**
    * Dispatches the statistics item to interested parties.
    *
    * @param \Drupal\sapi\StatisticsItemInterface $item
    *
    * @return void
   */
  public function dispatch(StatisticsItemInterface $item) {
    /** @var \Drupal\sapi\Plugin\StatisticsPluginInterface $instance */
    foreach ($this->statisticsPluginManager->getDefinitions() as $pluginDefinition) {
      $instance = $this->statisticsPluginManager->createInstance($pluginDefinition['id']);
      $instance->process($item);
    }
  }

}
