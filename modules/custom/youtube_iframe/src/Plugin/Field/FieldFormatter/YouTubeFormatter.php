<?php

namespace Drupal\youtube_iframe\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'youtube video' formatter.
 *
 * @FieldFormatter(
 *   id = "YouTubeFormatter",
 *   module = "youtube_iframe",
 *   label = @Translation("Displays Youtube video"),
 *   field_types = {
 *     "youtube_iframe"
 *   }
 * )
 */

class YouTubeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'width' => '600',
        'height' => '450',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Youtube video width'),
      '#default_value' => $this->getSetting('width'),
    );
    $elements['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Youtube video height'),
      '#default_value' => $this->getSetting('height'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
    public function settingsSummary() {
    $summary = [];

    $settings = $this->getSettings();

    if (!empty($settings['width']) && !empty($settings['height'])) {
      $summary[] = t('Video size: @width x @height', [
        '@width' => $settings['width'], '@height' => $settings['height']
      ]);
    }
    else {
      $summary[] = t('Define video size');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements (FieldItemListInterface $items, $langcode) {

    $elements = [];
    foreach ($items as $delta => $item) {

      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'width' => $this->settings['width'],
          'height' => $this->settings['height'],
          'src' => 'https://www.youtube.com/embed/' . $this->parse_yturl($item->url),
          'allow' => 'fullscreen'
        ],
      ];
    }

    return $elements;
  }

  /**
   * Get part url for attribute 'src'  function viewElements
   */
  public function parse_yturl($url) {
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
    $pattern .= '([\w-]{11})';
    $pattern .= '(?:.+)?$#x';
    preg_match($pattern, $url, $matches);
    return (isset($matches[1])) ? $matches[1] : false;
  }

}
