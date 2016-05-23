<?php

namespace Drupal\sapi\Plugin\Statistics\ActionType;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\sapi\ActionTypeBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * @ActionType(
 *  id = "entity_action",
 *  label = "An action is performed on an entity"
 * )
 */
class EntityAction extends ActionTypeBase {

  /**
   * @var \Drupal\Core\Entity\EntityInterface $entity
   */
  protected $entity;

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface $account
   */
  protected $account;

  /**
   * The action taken on the entity
   *
   * @protected string $action
   */
  protected $action;

  /**
   * Optionally an action mode
   *
   * For example: a view mode if that action if view
   */
  protected $mode;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    /**
     * @TODO put some ifs on these to make them safe,
     */

    // save the account
    $this->account = $configuration['account'];
    // save the entity
    $this->entity = $configuration['entity'];
    // and the entity action
    $this->action = $configuration['action'];

    if (isset($configuration['mode'])) {
      $this->mode = $configuration['mode'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function describe() {
    return 'Entity event: [entity:'.(($this->entity instanceof EntityInterface)?$this->entity->label().'('.$this->entity->id().')':'none').'][account:'.(($this->account instanceof AccountProxyInterface)?$this->account->getDisplayName().'('.$this->account->id().')':'none').'][action:'.$this->action.']';
  }

  /**
   * Get the action performed on the entity
   *
   * @return string action
   */
  function getEntityAction() {
    return $this->action;
  }

  /**
   * Get the entity acted on
   *
   * @return \Drupal\Core\Entity\EntityInterface
   */
  function getEntity() {
    return $this->entity;
  }

}
