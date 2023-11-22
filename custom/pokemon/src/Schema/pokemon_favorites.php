<?php

return [
  'description' => "User's favorites pokemons",
  'fields' => [
    'id' => [
      'type' => 'serial',
      'not null' => TRUE,
      'description' => 'Primary Key: Unique record ID.',
    ],
    'uid' => [
      'type' => 'int',
      'length' => 11,
      'null' => TRUE,
      'default' => 0,
      'description' => 'Drupal user id',
    ],
    'name' => [
      'type' => 'varchar',
      'length' => 60,
      'null' => TRUE,
      'default' => null,
    ],
    'lastname' => [
      'type' => 'varchar',
      'length' => 60,
      'null' => TRUE,
      'default' => null,
    ],
    'email' => [
      'type' => 'varchar',
      'length' => 100,
      'null' => TRUE,
      'default' => null,
    ],
    'pokemon_id' => [
      'type' => 'int',
      'length' => 11,
      'null' => TRUE,
      'default' => 0,
      'description' => 'Pokemon id',
    ],
    'pokemon_name' => [
      'type' => 'varchar',
      'length' => 60,
      'null' => TRUE,
      'default' => null,
    ],
    'picture' => [
      'type' => 'varchar',
      'length' => 250,
      'null' => TRUE,
      'default' => null,
    ],
    'created_at' => [
      'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
      'not null' => TRUE,
    ],
    'updated_at' => [
      'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
      'not null' => TRUE,
    ],
  ],
  'primary key' => ['id'],
];
