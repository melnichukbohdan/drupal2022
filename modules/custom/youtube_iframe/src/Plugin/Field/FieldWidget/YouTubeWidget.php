<?php

namespace Drupal\youtube_iframe\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'snippets_default' widget.
 *
 * @FieldWidget(
 *   id = "YouTubeWidget",
 *   label = @Translation("YouTube IFrame Widget"),
 *   field_types = {
 *     "youtube_iframe"
 *   }
 * )
 */

class YouTubeWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['url'] = [
      '#title' => $this->t('YouTube'),
      '#type' => 'textfield',
     // '#default_value' => isset($items[$delta]->url ) ? $items[$delta]->url : NULL,
      '#placeholder' => t('Enter YouTube URL'),
      '#element_validate' => [[$this, 'validate']],
    ];

    return $element;
  }

  /**
   * Validate the url.
   */
  public static function validate($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) == 0) {
      $form_state->setValueForElement($element, '');
      return;
    }

    $pattern = '#^(?:https?://)?';
    $pattern .= '(?:www\.)?';
    $pattern .= '(?:';
    $pattern .=   'youtu\.be/';
    $pattern .=   '|youtube\.com';
    $pattern .=   '(?:';
    $pattern .=     '/embed/';
    $pattern .=     '|/v/';
    $pattern .=     '|/watch\?v=';
    $pattern .=     '|/watch\?.+&v=';
    $pattern .=   ')';
    $pattern .= ')';
    $pattern .= '([\w-]+)';
    $pattern .= '(?:.+)?$#x';


    if(!preg_match($pattern , $value, $matches)) {
      $form_state->setError($element, t("Youtube video URL is not correct."));
    }
  }

}
