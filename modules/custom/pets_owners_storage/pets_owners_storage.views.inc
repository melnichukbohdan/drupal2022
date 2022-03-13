<?php

/**
 * @file
 * Provide views data for pets_owners_storage.module.
 */

/**
 * Implements hook_views_data
 */

function pets_owners_storage_views_data () {

  $data = [];
  $data['pets_owners_storage']['table']['group'] = t('Pets Owners');
  $data['pets_owners_storage']['table']['provider'] = 'pets_owners_storage';
  $data['pets_owners_storage']['table']['base'] = [
    // Identifier (primary) field in this table for Views.
    'field' => 'poid',
    // Label in the UI.
    'title' => t('Pets Owners'),
    // Longer description in the UI. Required.
    'help' => t('Pets  Owners table contains all pets owners'),
  ];
  // All fields.
  $data['pets_owners_storage']['poid'] = [
    'title' => t('ID'),
    'help' => t('Pets owners ID'),
    'field' => [
      'id' => 'numeric',
    ],
  ];
  $data['pets_owners_storage']['name'] = [
    'title' => t('Name'),
    'help' => t('Pets owner name'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
   'filter' => [
      'id' => 'string',
    ],
  ];
  $data['pets_owners_storage']['gender'] = [
    'title' => t('Gender'),
    'help' => t('Pets owner gender'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  $data['pets_owners_storage']['prefix'] = [
    'title' => t('Prefix'),
    'help' => t('Pets owner prefix'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'title' => t('User gender'),
      'help' => t('Specify a list of gender'),
      'field' => 'prefix',
      'id' => 'standard',
      //TODO create custom_views_filter
    ],
  ];
  $data['pets_owners_storage']['age'] = [
    'title' => t('Age'),
    'help' => t('Pets owner age'),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
        'id' => 'numeric',
    ],
  ];
  $data['pets_owners_storage']['father'] = [
    'title' => t('Father'),
    'help' => t('Pets owner father'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  $data['pets_owners_storage']['mother'] = [
    'title' => t('Mother'),
    'help' => t('Pets owner mother'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  $data['pets_owners_storage']['pet_name'] = [
    'title' => t('Pet name'),
    'help' => t('Pet name'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  $data['pets_owners_storage']['email'] = [
    'title' => t('Email'),
    'help' => t('Pets owner email'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];

  return $data;
}

