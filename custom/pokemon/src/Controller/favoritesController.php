<?php

namespace Drupal\pokemon\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Database\Database;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\pokemon\Service\pokemonFavoritesService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class favoritesController extends ControllerBase {

  protected $pokemonFavoritesService;

  public function __construct(pokemonFavoritesService $pokemonFavoritesService) {
    
    $this->pokemonFavoritesService = $pokemonFavoritesService;
  }

  public static function create(ContainerInterface $container) {
    
    return new static(
      $container->get('pokemon.pokemon_favorites')
    );
  }

  public function saveFavoritePokemon(Request $request) {

    // Get the pokemon id.
    $data = json_decode(file_get_contents('php://input'), true);

    // Verify if the user is authencticated
    $user = \Drupal::currentUser();
    if ($user->isAuthenticated()) {

      // Get the user id of the current user and some fields
      $user_id = $user->id();
      $user = User::load($user_id);
      $name = $user->get('field_name')->value;
      $lastname = $user->get('field_lastname')->value;
      $email = $user->get('mail')->value;

      //First we verrify if the user has 10 pokemons or more  
      $query_user_pokemons = \Drupal::database()->select('pokemon_favorites', 'pf_user');
      $query_user_pokemons->fields('pf_user', ['uid']);
      $query_user_pokemons->condition('pf_user.uid', $user_id);
      $user_pokemons_count = $query_user_pokemons->countQuery()->execute()->fetchField();
    
      if ($user_pokemons_count >= 10) {
        
        return new JsonResponse(['error' => "You can't add more than 10 pokemons", 'error_message' => 'more_than_10', 'code' => 409], 409);
      }
      
      
      //We validate if the pokemon already exists with the same user id
      $query_pokemon_id = \Drupal::database()->select('pokemon_favorites', 'pf');
      $query_pokemon_id->fields('pf');
      $query_pokemon_id->condition('pf.pokemon_id', $data['pokemon_id']);
      $result_pokemon_id = $query_pokemon_id->execute();

      if(!empty($result_pokemon_id->fetchCol())){

        return new JsonResponse(['error' => 'The pokemon already has been added previously', 'error_message' => 'pokemon_exist', 'code' => 409], 409);
      }
      
      // Save the user's favorites pokemons in the correct table
      $database = \Drupal::database();
      $database->insert('pokemon_favorites')
        ->fields([
          'uid' => $user_id,
          'name' => $name,
          'lastname' => $lastname,
          'email' => $email,
          'pokemon_id' => $data['pokemon_id'],
          'picture' => $data['picture'],
          'pokemon_name' => $data['pokemon_name']
        ])
        ->execute();

        return new JsonResponse(['message' => 'Pokemon saved', 'code' => 201], 201);
    }
  }

  /**
   * Page to list favorites pokemons
   *
   * @return void
   */

  public function favorites(){

    $uid = \Drupal::currentUser()->id();

    //We get the user's favorites pokemons with the service pokemonFavoritesService
    $favorites = $this->pokemonFavoritesService->getFavoritesByUid($uid);
    
    // Return page
    $page = [
      '#theme' => 'favorites',
      '#data' => $favorites,
      '#cache' => [
          'max-age' => 0
      ]
    ];

    return $page;
  }
}