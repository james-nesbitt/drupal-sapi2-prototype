<?php

namespace Drupal\sapi\Plugin\Statistics\ActionHandler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\sapi\ActionHandlerBase;
use Drupal\sapi\ActionHandlerInterface;
use Drupal\sapi\ActionTypeInterface;
use Drupal\Core\Logger\LoggerChannelFactory;

/**
 * @ActionHandler (
 *  id = "action_logger",
 *  label = "Log any received actions"
 * )
 */
class ActionLogger extends ActionHandlerBase implements ActionHandlerInterface, ContainerFactoryPluginInterface {

  /** @var \Drupal\Core\Logger\LoggerChannelInterface $logger    */
  protected $logger;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerChannelFactory $loggerFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // don't keep the whole factory, just the one logger
    $this->logger = $loggerFactory->get('sapi');
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,

      $container->get('logger.factory')
    );
  }


  /**
   * (@inheritdoc)
   */
  function process(ActionTypeInterface $action) {
    // just throw the action at the log for now.
    $this->logger->info($action->describe());
  }

}
