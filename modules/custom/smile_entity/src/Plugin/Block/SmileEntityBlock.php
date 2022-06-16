<?php

namespace Drupal\smile_entity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a smile entity block.
 *
 * @Block(
 *   id = "smile_entity_block",
 *   admin_label = @Translation("Smile Entity"),
 *   category = @Translation("Custom")
 * )
 */
class SmileEntityBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var AccountInterface $currentUser
   */
  protected $currentUser;

  /**
   * @var EntityTypeManagerInterface $entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param AccountInterface $currentUser

   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition,
                              AccountInterface $currentUser,
                              EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['items'] = [
      '#type' => 'number',
      '#title' => $this->t("How many 'Smile Entity' do you want load?"),
      '#default_value' => $this->configuration['items'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['items'] = $form_state->getValue('items');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $entityTypeID = 'smile_entity';
    $query = $this->getEntityTypeManager()->getStorage($entityTypeID)->getQuery();
    $results = $query->condition('role', $this->getCurrentUser()->getRoles(), 'IN')
      ->range(0, $this->configuration['items'])
      ->sort('id', 'DESC')
      ->execute();

    $entitys = $this->getEntityTypeManager()->getStorage($entityTypeID)->loadMultiple($results);
    $build[] = $this->getEntityTypeManager()->getViewBuilder($entityTypeID)->viewMultiple($entitys);

    return[
      'elements' => $build,
      '#cache' => [
        'context' => [
          'user.roles',
        ],
      ],
    ];
  }

  /**
   * @return AccountInterface
   */
  public function getCurrentUser(): AccountInterface  {
    return $this->currentUser;
  }

  /**
   * @return EntityTypeManagerInterface
   */
  public function getEntityTypeManager(): EntityTypeManagerInterface  {
    return $this->entityTypeManager;
  }

}
