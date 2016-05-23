<?php

namespace Drupal\sapi;

/**
 * Interface DispatcherInterface.
 *
 * @package Drupal\sapi
 */
interface DispatcherInterface {

  /**
   * Dispatches the statistics item to interested parties.
   *
   * @param \Drupal\sapi\ActionTypeInterface $action
   *   SAPI action item to be passed to the handler plugins
   *
   * @return void
   *
   * @todo should we pass anything into the handler plugin constructor?
   */
  public function dispatch(ActionTypeInterface $action);

}
