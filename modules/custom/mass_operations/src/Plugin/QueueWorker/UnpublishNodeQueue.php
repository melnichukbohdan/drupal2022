<?php

namespace Drupal\mass_operations\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 *
 * @QueueWorker(
 *   id = "node_unpublisher",
 *   title = @Translation("Node UnPublisher"),
 *   cron = {"time" = 30}
 * )
 */

 class UnpublishNodeQueue extends QueueWorkerBase implements ContainerFactoryPluginInterface {

   /**
    * The node storage.
    *
    * @var \Drupal\Core\Entity\EntityTypeManagerInterface
    */
   protected $nodeStorage;

    /**
    * Creates a new NodeUnpublish.
    *
    * @param \Drupal\Core\Entity\EntityTypeManagerInterface $nodeStorage
    *   The node storage.
    */
   public function __construct(EntityTypeManagerInterface $nodeStorage) {
       $this->nodeStorage = $nodeStorage;
   }

   /**
    * {@inheritdoc}
    */
   public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
     return new static(
       $container->get('entity_type.manager')
     );
   }

   /**
    * {@inheritdoc}
    */
   public function processItem($data) {
     $config = \Drupal::configFactory()->get('mass_operations.set_param');

     $nodes = $this->nodeStorage->getStorage('node')->load($data);
     if ($nodes instanceof NodeInterface && $nodes->isPublished()) {
        $nodes->setTitle($nodes->getTitle() . ' | ' . $config->get('unpublsihed_label'));
        $nodes->setUnpublished()
         ->save();
     }
   }
 }

