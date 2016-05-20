<?php

namespace Drupal\sapi\Plugin\Statistics\ActionType;

use Drupal\sapi\ActionTypeBase;
use Drupal\sapi\ActionTypeInterface;

/**
 * @ActionType (
 *   id = "dummy",
 *   label = "Dummy action item"
 * )
 */
class Dummy extends ActionTypeBase implements ActionTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function describe() {
    return 'I am a dummy plugin ('.__class__.')';
  }
}