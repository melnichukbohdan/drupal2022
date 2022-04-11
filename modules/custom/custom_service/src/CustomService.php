<?php

namespace Drupal\custom_service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;



class CustomService {

  use StringTranslationTrait;

  protected $connection;

  protected $currentUser;

  /**
   * CustomServiceCustomService constructor.
   * @param AccountInterface $currentUser
   */
  public function __construct(Connection $connection,
                              AccountInterface $currentUser,
                              TranslationInterface $string_translation,
                              EntityTypeManagerInterface $entityTypeManager,
                             ) {
    $this->connection = $connection;
    $this->currentUser = $currentUser;
    $this->stringTranslation = $string_translation;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @return \Drupal\Component\Render\MarkupInterface|string
   */
  public function getData() {
    $userName = $this->currentUser->getDisplayName();
    return $this->t('User name - @name',[
      '@name' => $userName,
    ]);
  }

  public function getActiveUsers(): TranslatableMarkup {

    $result = $this->connection->select('users_field_data')
      ->condition('status', '1', '=')
      ->countQuery()
      ->execute()->fetchAssoc();

    return $this->t('You are unique among @count_all_users users',[
      '@count_all_users' => $result['expression'],
    ]);
  }

  public function getPositionOfRegistration () {
    $userId = $this->currentUser->id();
    return $this->t('You have been registered @position_of_registration',[
      '@position_of_registration' => $userId,
    ]);
  }

  public function getNode () {

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple();
    $id_node = array_rand($nodes,1);
    $node = $this->entityTypeManager->getStorage('node')->load($id_node);
    return $this->entityTypeManager->getViewBuilder('node')->view($node,'teaser');
  }


}
