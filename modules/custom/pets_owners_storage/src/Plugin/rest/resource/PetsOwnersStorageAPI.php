<?php

namespace Drupal\pets_owners_storage\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Database;
use Drupal\rest\ModifiedResourceResponse;


/**
 * Provides a resource to get Pets Owners info.
 *
 * @RestResource(
 *   id = "get_pets_owners",
 *   label = @Translation("Get Pets Owners"),
 *   uri_paths = {
 *     "canonical" = "/api/pets_owners/v1/get-pets-owners"
 *   }
 * )
 */


class PetsOwnersStorageAPI extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new GetArticleResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('pets_owners_storage'),
      $container->get('current_user')
    );
  }

  /**
   * Load specific record (by row Primary Key - ID)
   * @return ResourceResponse
   */
  public function get() {
    //get pets owner ID from API query
    $queryAPI = \Drupal::request()->query;
    $poid = $queryAPI->get('id');

    if ($queryAPI->has('id')) {
      //get pets owner data
      $queryDB = Database::getConnection()
        ->select('pets_owners_storage', 'p')
        ->condition('p.poid', $poid)
        ->fields('p')
        ->execute()
        ->fetchAssoc();
      if (!empty($queryDB)) {
        return new ModifiedResourceResponse($queryDB, 200);
      } else {
        $response['message'] = 'Pet owner with ID ' . $queryAPI->get('id') . ' is not found';
        return new ModifiedResourceResponse($response);
      }
    }
  }

}
