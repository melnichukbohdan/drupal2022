<?php

namespace Drupal\contact_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for contact_page routes.
 */
class ContactPageController extends ControllerBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * @var EntityTypeManagerInterface $entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var Drupal\Core\Database\Connection $database
   */
  protected $connection;

  /**
   * @param AccountInterface $account
   * @param EntityTypeManagerInterface $entityTypeManager
   * @param Connection $connection
   */
  public function __construct(AccountInterface $account,
                              EntityTypeManagerInterface $entityTypeManager,
                              Connection $connection) {
    $this->account = $account;
    $this->entityTypeManager = $entityTypeManager;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('database'),
    );
  }

  /**
   * Builds the response.
   */
  public function build() {

    $header_table = [
      'id' => t('ID'),
      'email' => t('Email'),
      'phone' => t('Phone'),
      'message' => t('Message'),
      'category' => t('Category'),
    ];

    $results = $this->getConnection()->select('contact_page', 'c')
      ->fields('c', [
        'id',
        'email',
        'phone_number',
        'message',
        'category',
      ])
      ->condition('uid', $this->getAccount()->id())
      ->execute()->fetchAll();

    $rows = [];
    foreach ($results as $data) {
      $rows[] = [
        'id' => $data->id,
        'email' => $data->email,
        'phone_number' => $data->phone_number,
        'message' => $data->message,
        'category' => $this->getEntityTypeManager()->getStorage('taxonomy_term')
          ->load($data->category)->label(),
      ];
    }

    $build['content'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => $this->t('No entries available.'),
      '#cache' => [
        'max-age' => 0
      ],
    ];

    return $build;
  }

  /**
   * @return Drupal\Core\Database\Connection
   */
  public function getConnection():Connection {
    return $this->connection;
  }

  /**
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface  {
    return $this->entityTypeManager;
  }

  /**
   * @return AccountInterface
   */
  public function getAccount(): AccountInterface   {
    return $this->account;
  }

}
