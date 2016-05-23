<?php

namespace Drupal\sapi\Plugin\Statistics\ActionType;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\sapi\ActionTypeBase;

/**
 * @ActionType(
 *  id = "route_used",
 *  label = "A route was actively used in Drupal"
 * )
 */
class RouteUsed extends ActionTypeBase {

  /**
   * @var RouteMatchInterface  $route_match
   */
  protected $route_match;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    if (!isset($configuration['route_match']) || !($configuration['route_match'] instanceof RouteMatchInterface)) {
      throw new \UnexpectedValueException('Expected Drupal\Core\Routing\RouteMatchInterface parameter in plugin configuration.  None was provided.');
    }

    // save the route
    $this->route_match = $configuration['route_match'];

  }

  /**
   * Get the stored Route Match
   *
   * @return \Drupal\Core\Routing\RouteMatchInterface|mixed
   */
  function getRouteMatch() {
    return $this->route_match;
  }

}