<?php

namespace Drupal\pets_owners_storage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class PetsOwnersStorage extends ControllerBase {

  public function displayTable() {

    $form =[];
    //create table header
    $header_table = [
      'id' => t('ID'),
      'name' => t('Name'),
      'gender' => t('Gender'),
      'age' => t('Age'),
      'father' => t('Father'),
      'mother' => t('Mother'),
      'pet_name' => t('Pet Name'),
      'email' => t('Email'),
      'delete' => t('Delete'),

    ];

    // get data from database
    $connection = \Drupal::database()
      ->select('pets_owners_storage', 'p');
    $query = $connection->fields('p',
    ['poid', 'name', 'gender', 'age', 'father', 'mother', 'pet_name', 'email']);
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $data) {

      $url_delete = Url::fromRoute('pets_owners_storage.delete',
        ['id' => $data->poid], []);

      $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);


      //get data
      $rows[] = [
        'id' => $data->poid,
        'name' => $data->name,
        'gender' => $data->gender,
        'age' => $data->age,
        'father' => $data->father,
        'mother' => $data->mother,
        'pet_name' => $data->pet_name,
        'email' => $data->email,
        'delete' => $linkDelete,
      ];
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
