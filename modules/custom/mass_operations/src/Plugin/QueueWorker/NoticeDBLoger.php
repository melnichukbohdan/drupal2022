<?php

namespace Drupal\mass_operations\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Logger\LoggerChannelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\User;

/**
 *
 * @QueueWorker(
 *   id = "noticeDBLog",
 *   title = @Translation("notice DB Log"),
 *   cron = {"time" = 30}
 * )
 */

class NoticeDBLoger extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var LoggerChannelInterface
   */
  protected $loger;

   /**
   * Creates a new NodeUnpublish.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $nodeStorage
   *   The node storage.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager,
                              LoggerChannelInterface $loger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loger = $loger;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('logger.factory')->get('noticeDBLog'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    // get all users with role 'administrator'
    $users = $this->entityTypeManager->getStorage('user')
      ->getQuery()
      ->condition('status', '1')
      ->condition('roles', 'administrator')
      ->execute();

    foreach ($users as $uid) {
      // get user name
      $user = User::load($uid);
      $name = $user->getAccountName();
      // generate notice for dblog
      $this->loger->notice('User @username should be notified about new node ‘@node_title[@node_id]', [
        '@username' => $name,
        '@node_title' => $data['node_title'],
        '@node_id' => $data['node_id']
      ]);
    }
  }

}
