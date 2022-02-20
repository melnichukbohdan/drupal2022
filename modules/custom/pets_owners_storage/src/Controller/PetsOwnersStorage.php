<?php

namespace Drupal\pets_owners_storage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

class PetsOwnersStorage extends ControllerBase {

  //displays pets owners list
  public function displayTable() {

    $form =[];
    //create table header
    $header_table = [
      'id' => t('ID'),
      'name' => t('Name'),
      'gender' => t('Gender'),
      'prefix' => t('Prefix'),
      'age' => t('Age'),
      'father' => t('Father'),
      'mother' => t('Mother'),
      'pet_name' => t('Pet Name'),
      'email' => t('Email'),
      'delete' => t('Delete'),
      'edit' => t('Edit'),
    ];

    // get data from database
    $connection = \Drupal::database()
      ->select('pets_owners_storage', 'p');
    $query = $connection->fields('p', [
      'poid',
      'name',
      'gender',
      'prefix',
      'age',
      'father',
      'mother',
      'pet_name',
      'email']);
    $results = $query->execute()->fetchAll();

    // create array for data every row
    $rows = [];
    $i = 0;

    foreach ($results as $data) {

      $rows[] =
       array_map('Drupal\Component\Utility\Html::escape', (array) $data);

      //build route for link 'delete'
      $linkDelete = Url::fromRoute('pets_owners_storage.delete',
        ['id'=>$rows[$i]['poid']]);
      //set class 'use-ajax' ond options for callback modal window
      $linkDelete->setOptions([
        'attributes' => [
          'class' => ['use-ajax', 'link'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode(['width' => 400]),
        ],
      ]);

      //set parameters for link 'delete'
      $ajax_link = ['#type' => 'markup',
        //conversion data in variable $linkDelete to string
        '#markup' => Link::fromTextAndUrl(t('Delete'), $linkDelete)
          ->toString(),
        //attached drupal.dialog.ajax library
        '#attached' => ['library' => ['core/drupal.dialog.ajax']]];
      //call Core service Renderer and his method render
      $rows[$i]['delete']=\Drupal::service('renderer')->render($ajax_link);

      //build link edit
      $url_edit = Url::fromRoute('pets_owners_storage.edit',
        ['id' => $data->poid], []);
      $rows[$i]['edit'] = Link::fromTextAndUrl('Edit', $url_edit);
      $i++;
    }

      // render table
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header_table,
        '#rows' => $rows,
      ];
      return $form;
  }
}

