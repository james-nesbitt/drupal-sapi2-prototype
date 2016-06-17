<?php

namespace Drupal\sapi_entity_interaction\Plugin\Statistics\ActionHandler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\sapi\ConfigurableActionHandlerBase;
use Drupal\sapi\ActionTypeInterface;
use Drupal\sapi\Plugin\Statistics\ActionType\EntityInteraction;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;


/**
 * This is a SAPI handler plugin used to track any entity interactions (view, update and create).
 *
 * @ActionHandler(
 *  id = "entity_interaction_tracker",
 *  label = "Track any entity interactions"
 * )
 */
class EntityInteractionTracker extends ConfigurableActionHandlerBase implements ContainerFactoryPluginInterface {

  /**
   * EntityTypeManager used to get entity storage for sapi_data items, which is
   * used to create and edit sapi_data items from tracking.
   *
   * @protected Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * EntityInteractionTracker constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,

      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process(ActionTypeInterface $action){

    /**
     * Only acts if $action is an EntityInteraction plugin type
     */
    if (!($action instanceof EntityInteraction)) {
      return;
    }

    /**
     * @var string $entity_type
     *   Entity type ID
     */
    $entity_type = $action->getEntity()->getEntityTypeId();

    /**
     * @var string $entity_id
     *   ID of Entity
     */
    $entity_id = $action->getEntity()->id();

    /**
     * @var string $interaction_type
     *   Interaction type to Entity(View, Update, Create)
     */
    $interaction_type = $action->getAction();

    /**
     * @var string $user_id
     *   UID of user who interacted to Entity
     */
    $user_id = $action->getAccount()->id();

    /**
     * @var EntityStorageInterface $sapiDataStorage
     */
    $sapiDataStorage = $this->entityTypeManager->getStorage('sapi_data');

    /** Creating a new interaction entity */

    /** @var \Drupal\sapi_data\SAPIDataInterface $sapiData */
    $sapiData = $sapiDataStorage->create([
      'type' => 'entity_interactions',
      'name' => $entity_type .':'. $entity_id .':'. $interaction_type,
      'field_interaction_type' => $interaction_type,
      'field_entity_reference' => ['target_id'=>$entity_id, 'target_type'=>$entity_type],
      'field_user' => $user_id,
    ]);

    if (!$sapiData->save()) {
      \Drupal::logger('sapi')->warning('Could not create SAPI data');
    }

  }

  protected function getEditableConfigNames() {
    return ['sapi.entity_interaction_config'];
  }

  /**
   *{@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    /** @var array $all_roles Array of all available roles */
    $all_roles = [];
    foreach (Role::loadMultiple() as $role) {
      /** @var \Drupal\user\Entity\Role $role */
      $all_roles[$role->id()] = $role->label();
    }
    $form['roles'] = array(
      '#type' => 'checkboxes',
      '#title' => 'Tracked roles',
      '#description' => 'User roles to track.',
      '#options' => $all_roles,
      '#default_value' => $this->config('sapi.entity_interaction_config')->get('roles')
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Save configuration',
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   *{@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateConfigurationForm() method.
  }

  /**
   *{@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->config('sapi.entity_interaction_config')
      ->set('roles', array_filter($form_state->getValue('roles')))
      ->save();
  }

}
