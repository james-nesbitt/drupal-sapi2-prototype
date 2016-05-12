<?php

namespace Drupal\sapi\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Statistics plugin plugin manager.
 */
class StatisticsPluginManager extends DefaultPluginManager {

  /**
   * Constructor for StatisticsPluginManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/StatisticsPlugin', $namespaces, $module_handler, 'Drupal\sapi\Plugin\StatisticsPluginInterface', 'Drupal\sapi\Annotation\StatisticsPlugin');

    $this->alterInfo('sapi_statistics_plugins_info');
    $this->setCacheBackend($cache_backend, 'sapi_statistics_plugins');
  }

}
