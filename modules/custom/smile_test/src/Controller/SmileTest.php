<?php

/*
 * @file
 * Contains \Drupal\smile-test\Controller\SmileTest
 */

namespace Drupal\smile_test\Controller;


/*
 * Provide route for my custom routs
 */

class SmileTest {

  /*
   * Display simple text message 'It is my first route ever'
   */
  public function content() {
    return array(
      '#markup' => '<h2>It is my first route ever</h2>'
    );
  }

  /*
   * @param nid - node number
   * Displays node teaser which number entered user
   * if node number doesn'n exist - displays message 'Node does not exist :('
   */
  public function nodeRender($nid) {

      $node = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($nid);
      if ($node) {
        $element = \Drupal::entityTypeManager()
          ->getViewBuilder('node')
          ->view($node, 'teaser');
        $output = \Drupal::service('renderer')->render($element);
        return ['#markup' => $output];
      }
      return ['#markup' => '<h2>Node does not exist :(</h2>'];

  }
}
