<?php

namespace Drupal\pets_owners_storage\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;



/**
 * Provides a resource to get Pets Owners info.
 *
 * @RestResource(
 *   id = "get_pets_owners_list",
 *   label = @Translation("Get Pets Owners List"),
 *   uri_paths = {
 *     "canonical" = "/api/pets_owners/v1/get-pets-owners-list",
 *   }
 * )
 */

class PetsOwnersStorageListAPI extends ResourceBase {

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
   * Responds to GET requests.
   */

  public function get (Request $request) {

    $page = $request->get('page');
    if (empty($request->get('page'))) {
      $page = 1;
    }
    $age = $request->get('age');
    if (empty($request->get('age'))) {
      $response['message'] = "The param 'age' is empty.";
      return new ModifiedResourceResponse($response, 400);
    }

    // get all pets owners with parameters for API query
    $response['items'] = $this->processItems($age, $page);

    //build link 'prev_page'
    $response['prev_page'] = $this->prevPage($page, $request);

    //build link 'next_page'
    $response['next_page'] = $this->nextPage($age, $page, $request);


    if ($this->countRecords($age) != 0) {
      return new ModifiedResourceResponse($response, 200);
    }
    else {
      $response['message'] = 'Record with provided AGE is not found.';
      return new ModifiedResourceResponse($response, 204);
    }
  }

  /**
   * get count pets owners
   * @param int $age
   * @return mixed
   */
  public function countRecords (int $age) {
    $countRecords = \Drupal::database()
      ->select('pets_owners_storage')
      ->condition('age', $age)
      ->countQuery()
      ->execute()
      ->fetchField();
    return $countRecords;
  }

  /**
   * get all pets owners with parameters for API query
   * @param int $age
   * @param int $page
   * @return array
   */
  public function processItems (int $age, int $page) {
    $limit = 5;
    $start = $page * $limit - $limit;
    $query = \Drupal::database()->select('pets_owners_storage')
      ->fields('pets_owners_storage')
      ->condition('age', $age)
      ->range($start, $limit)
      ->execute();
    $count = 0;
    while($record = $query->fetchAssoc()) {
      $count++;
      $response["$count"] = $record;
    }
    return $response;
  }

  /**
   * build link 'prev_page'
   * @param int $page
   * @param Request $request
   * @return string
   */
  public function prevPage(int $page, Request $request) {
    if ($page > 1) {
      $next_page_query = $request->query->all();
      $next_page_query['page'] = $page - 1;
      $response['prev_page'] = Url::createFromRequest($request)
        ->setOption('query', $next_page_query)
        ->toString(TRUE)
        ->getGeneratedUrl();
      return $response['prev_page'];
    } else {
      return 'FALSE';
    }
  }

  /**
   * build link 'next_page'
   * @param int $age
   * @param int $page
   * @param Request $request
   * @return string
   */
  public function nextPage (int $age, int $page, Request $request) {
    if ($this->countRecords($age) > 5 * $page) {
      $next_page_query = $request->query->all();
      $next_page_query['page'] = $page + 1;
      $response['next_page'] = Url::createFromRequest($request)
        ->setOption('query', $next_page_query)
        ->toString(TRUE)
        ->getGeneratedUrl();
      return $response['next_page'];
    }else {
      return 'FALSE';
    }
  }

}
