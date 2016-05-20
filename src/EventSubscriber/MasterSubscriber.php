<?php

namespace Drupal\sapi\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\sapi\DispatcherInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Class MasterSubscriber.
 *
 * @package Drupal\sapi
 */
class MasterSubscriber implements EventSubscriberInterface {


  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $current_route_match;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;

  /**
   * ActionType plugin manager, which we will use to build action items
   *
   * @protected Drupal\Component\Plugin\PluginManagerInterface $action_type_manager
   */
  protected $action_type_manager;

  /**
   * Current user account proxy
   *
   * @protected \Drupal\Core\Session\AccountProxyInterface $current_user
   */
  protected $current_user;

  /**
   * SAPI dispatcher
   *
   * @protected \Drupal\sapi\DispatcherInterface $dispatcher
   */
  protected $dispatcher;

  /**
   * Constructor.
   */
  public function __construct(CurrentRouteMatch $current_route_match, EntityTypeManager $entity_type_manager, PluginManagerInterface $action_type_manager, AccountProxyInterface $current_user, DispatcherInterface $dispatcher) {
    $this->current_route_match = $current_route_match;
    $this->entity_type_manager = $entity_type_manager;
    $this->action_type_manager = $action_type_manager;
    $this->current_user = $current_user;
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {

    $events[KernelEvents::VIEW][] = ['kernel_view', 1];
    return $events;
  }

  /**
   * This method is called whenever the routing.route_finished event is
   * dispatched.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   */
  public function kernel_view(Event $event) {
    /**
     * @var \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     *
     * @see Symfony\Component\HttpKernel\KernelEvents
     */

    
    

    /**
     * This event subscriber chooses this point to process a large number of
     * possible events based on settings.
     */
    $this->entityRouteActions();
    
  }

  
  private function entityRouteActions() {
    /** @var string $route_name */
    $route_name = $this->current_route_match->getRouteName();
    
    // only act on entity routes
    if (substr($route_name,0,7)!='entity.') {
      return;
    }

    /** @var string[] $route_elements */
    $route_elements = explode('.', $route_name);
    array_shift($route_elements);
    
    /** @var string entity_type */
    $entity_type = array_shift($route_elements);
    /** @var \Drupal\Core\Entity\EntityInterface|null $entity */
    $entity = $this->current_route_match->getParameter($entity_type);

    /** @var string $entity_action */
    $entity_action = array_shift($route_elements);

    // do some common action transformations
    switch($entity_action) {
      case 'canonical':
        $entity_action = 'view';
        break;
      case 'edit_form':
        $entity_action = 'edit';
        break;
      case 'delete_form':
        $entity_action = 'delete';
        break;
      case 'add_form':
        $entity_action = 'add';
        break;
    }


    /** @var \Drupal\sapi\ActionTypeInterface $action */
    $action = $this->action_type_manager->createInstance('entity_action', ['entity'=>$entity, 'account'=>$this->current_user, 'action'=>$entity_action]);

    // send the action to the dispatcher
    $this->dispatcher->dispatch($action);
  }
}
