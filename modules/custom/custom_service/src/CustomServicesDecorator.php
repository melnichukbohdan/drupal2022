<?php

namespace Drupal\custom_service;

use Drupal\Core\Session\AccountProxyInterface ;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class CustomServicesDecorator extends AccountProxy {

  /**
   * @var AccountProxyInterface
   */
  protected $innerService;

  /**
   * @param AccountProxyInterface $current_user
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(AccountProxyInterface $current_user,
                              EventDispatcherInterface $eventDispatcher,
                              ) {
    $this->innerService = $current_user;
    parent::__construct($eventDispatcher);
  }

  /**
   * change method getDisplayName, now this method return client email
   * @return string
   */
  public function getDisplayName() {
    $email = $this->getEmail();

    return "Email: $email";

  }

}
