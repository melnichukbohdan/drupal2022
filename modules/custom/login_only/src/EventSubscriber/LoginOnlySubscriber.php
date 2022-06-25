<?php

namespace Drupal\login_only\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;


/**
 * login_only event subscriber.
 */
class LoginOnlySubscriber implements EventSubscriberInterface {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $current_route_match;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $current_user;

  /**
   * The page cache disabling policy.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $page_cache_kill_switch;

  /**
   * @param MessengerInterface $messenger
   * @param RouteMatchInterface $current_route_match
   * @param ConfigFactory $config_factory
   * @param AccountInterface $current_user
   * @param KillSwitch $page_cache_kill_switch
   */

  public function __construct(MessengerInterface $messenger,
                              RouteMatchInterface $current_route_match,
                              ConfigFactory $config_factory,
                              AccountInterface $current_user,
                              KillSwitch $page_cache_kill_switch ) {
    $this->messenger = $messenger;
    $this->current_route_match = $current_route_match;
    $this->config_factory = $config_factory;
    $this->current_user = $current_user;
    $this->page_cache_kill_switch = $page_cache_kill_switch;
  }

  /**
   * Kernel request event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Response event.
   */
  public function userAccessiblePage(RequestEvent $event) {
    if ($this->getCurrentUser()->id() == 1) {
      return;
    }

    // chek for Enable Login Only mode
    if (!$this->getConfigFactory()->get('login_only.settings')->get('login_only_mode') == TRUE) {
      return;
    }

    //Clean cache
    $this->getPageCacheKillSwitch()->trigger();

    //accessible pages for anonymous user
    if ($this->getCurrentUser()->isAnonymous()) {
      $this->anonymousUserAccessiblePages($event);
    }

    //accessible pages for authenticated user
    if ($this->getCurrentUser()->isAuthenticated()) {
      $this->authenticatedUserAccessiblePages($event);
    }

  }

  /**
   * chek accessible pages for anonymous user
   * @param RequestEvent $event
   */
  public function anonymousUserAccessiblePages (RequestEvent $event)  {
    //exceptions routes
    $routes = [
      'user.pass',
      'user.register',
      'user.login',
    ];
    if (!in_array($this->getCurrentRouteMatch()->getRouteName(), $routes)) {
      $event->setResponse(new RedirectResponse('http://drupal2022/user/login', 302));
    }
  }

  /**
   * chek accessible pages for authenticated user
   * @param RequestEvent $event
   */
  public function authenticatedUserAccessiblePages (RequestEvent $event)  {
    //exceptions routes
    $routes = [
      'entity.user.canonical',
      'entity.user.edit_form',
      'user.page',
      'user.logout',
      'contact_page.form',
      'contact_page.list',
    ];
    if (!in_array($this->getCurrentRouteMatch()->getRouteName(), $routes)) {
      $event->setResponse(new RedirectResponse('http://drupal2022/user/' . $this->getCurrentUser()->id(),302));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['userAccessiblePage'],
    ];
  }

  //Getters
  /**
   * @return MessengerInterface
   */
  public function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  /**
   * @return \Drupal\Core\Routing\RouteMatchInterface
   */
  public function getCurrentRouteMatch(): RouteMatchInterface {
    return $this->current_route_match;
  }

  /**
   * @return ConfigFactoryInterface
   */
  public function getConfigFactory(): ConfigFactoryInterface {
    return $this->config_factory;
  }

  /**
   * @return AccountInterface
   */
  public function getCurrentUser(): AccountInterface {
    return $this->current_user;
  }

  /**
   * @return KillSwitch
   */
  public function getPageCacheKillSwitch(): KillSwitch {
    return $this->page_cache_kill_switch;
  }

}
