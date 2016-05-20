<?php

namespace Drupal\sapi;

use Drupal\Core\Config\ConfigManager;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Class Dispatcher.
 *
 * @package Drupal\sapi
 */
class Dispatcher implements DispatcherInterface {

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
  protected $SAPIActionHandlerPluginManager;

  /**
   * Dispatcher constructor.
   *
   * @param \Drupal\Core\Config\ConfigManager $configManager
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   * @param \Drupal\Component\Plugin\PluginManagerInterface $SAPIActionHandlerPluginManager
   */
  public function __construct(ConfigManager $configManager, AccountProxy $currentUser, CurrentRouteMatch $currentRouteMatch, PluginManagerInterface $SAPIActionHandlerPluginManager) {
    $this->configManager = $configManager;
    $this->currentUser = $currentUser;
    $this->currentRouteMatch = $currentRouteMatch;
    $this->SAPIActionHandlerPluginManager = $SAPIActionHandlerPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public function dispatch(ActionTypeInterface $action) {
    /** @var \Drupal\sapi\ActionHandlerInterface $instance */
    foreach ($this->SAPIActionHandlerPluginManager->getDefinitions() as $pluginDefinition) {
      /** @var \Drupal\sapi\ActionHandlerInterface $instance */
      $instance = $this->SAPIActionHandlerPluginManager->createInstance($pluginDefinition['id']);
      $instance->process($action);
    }
  }

}
