<?php

namespace Drupal\pokemon\Service;

use Drupal\Core\Database\Connection;

class pokemonFavoritesService {

  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function getFavoritesByUid($uid) {
    $query = $this->database->select('pokemon_favorites', 'pf')
      ->fields('pf')
      ->condition('pf.uid', $uid);
    $result = $query->execute()->fetchAll();
    
    //Return the list of favorites pokemons of a specific user
    return $result;
  }
}