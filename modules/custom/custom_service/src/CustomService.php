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

  /**
   * @var Connection
   */
  protected $connection;

  /**
   * @var AccountInterface
   */
  protected $currentUser;

  /**
   * @param Connection $connection
   * @param AccountInterface $currentUser
   * @param TranslationInterface $string_translation
   * @param EntityTypeManagerInterface $entityTypeManager
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
   * get user name
   * @return TranslatableMarkup
   */
  public function getName() {
    $userName = $this->currentUser->getDisplayName();
    return $this->t('@name',[
      '@name' => $userName,
    ]);
  }

  /**
   * get count all users
   * @return TranslatableMarkup
   */
  public function getActiveUsers(): TranslatableMarkup {

    $result = $this->connection->select('users_field_data')
      ->condition('status', '1', '=')
      ->countQuery()
      ->execute()->fetchAssoc();

    return $this->t('You are unique among @count_all_users users',[
      '@count_all_users' => $result['expression'],
    ]);
  }

  /**
   * get position of registration
   * @return TranslatableMarkup
   */
  public function getPositionOfRegistration () {
    $userId = $this->currentUser->id();
    return $this->t('You have been registered @position_of_registration',[
      '@position_of_registration' => $userId,
    ]);
  }

  /**
   * get random node
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNode () {
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple();
    $id_node = array_rand($nodes,1);
    $node = $this->entityTypeManager->getStorage('node')->load($id_node);
    return $this->entityTypeManager->getViewBuilder('node')->view($node,'teaser');
  }

}
