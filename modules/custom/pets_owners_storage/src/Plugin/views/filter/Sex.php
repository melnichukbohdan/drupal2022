<?php

namespace Drupal\pets_owners_storage\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Custom view handler that will provide filter by prefix field
 * @ViewsFilter("custom_views_sex")
 */

class Sex extends FilterPluginBase {

  public function defineOptions() {
    $options = parent::defineOptions();
    $options['operator'] = [
      'default' => '<>',
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => [
        'mr' => $this->t('Man'),
        'mrs | ms' => $this->t('Woman')
      ],
      '#default_value' => $this->value,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {

    $this->ensureMyTable();

    /** @var \Drupal\views\Plugin\views\query\Sql $query */
    $query = $this->query;
    // set "WHERE pets_owners_storage.prefix"
    $field = $this->tableAlias . "." . $this->realField;
    // set parameters in WHERE  after select in filter
    if ($this->value[0] == 'mr') {
      $query->addWhere($this->options['group'], $field, 'mr', '=');
    } else {
      $query->addWhere($this->options['group'], $field,  'mr', '<>');
    }
 }

}
