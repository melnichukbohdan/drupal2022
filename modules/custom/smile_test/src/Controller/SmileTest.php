<?php

/*
 * @file
 * Contains \Drupal\smile-test\Controller\SmileTest
 */

namespace Drupal\smile_test\Controller;


/*
 * Provide route for my custom routs
 */

use Drupal\Core\Controller\ControllerBase;

class SmileTest extends ControllerBase {

  public function content () {
    return array(
      '#markup' => '<h2>It is my first route ever</h2>'
    );
  }

}
