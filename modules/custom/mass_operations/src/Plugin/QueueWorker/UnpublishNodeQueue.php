<?php

namespace Drupal\mass_operations\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityPublishedInterface;




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
    * @var \Drupal\Core\Entity\EntityStorageInterface
    */
   protected $nodeStorage;

    /**
    * Creates a new NodeUnpublish.
    *
    * @param \Drupal\Core\Entity\EntityStorageInterface $nodeStorage
    *   The node storage.
    */
   public function __construct(EntityStorageInterface $nodeStorage) {
       $this->nodeStorage = $nodeStorage;
   }

   /**
    * {@inheritdoc}
    */
   public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
     return new static(
       $container->get('entity_type.manager')->getStorage('node')
     );
   }


   public function processItem($data) {
     /** @var EntityPublishedInterface $node_storage */
     $node_storage = \Drupal::entityTypeManager()->getStorage('node');
     if (!empty($data) && $node_storage->load($data) && $node_storage->load($data)->isPublished()) {
       $node_storage->load($data)
         ->setUnpublished()
         ->save();

     }
   }
 }

