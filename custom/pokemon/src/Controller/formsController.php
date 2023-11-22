<?php

namespace Drupal\pokemon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the module.
 */
class formsController extends ControllerBase
{

    /**
     * Log in controller
     *
     * @return void
     */

    public function signin(){

        //If user is authenticated we redirect him to game
        $uid = \Drupal::currentUser()->id();
        if ($uid) {
            $response = new RedirectResponse('/');
            $response->send();
            return $response;
        }

        // To get forms and save them in the variable data
        $formLogin = \Drupal::formBuilder()->getForm('Drupal\pokemon\Form\LoginForm');

        $data = [
            'login' => $formLogin
        ];

        // Return page
        $page = [
        '#theme' => 'signin',
        '#data' => $data,
        '#cache' => [
            'max-age' => 0
        ]
        ];

        return $page;
    }

    /**
     * Register controller
     *
     * @return void
     */

     public function signup(){

        //If user is authenticated we redirect him to game
        $uid = \Drupal::currentUser()->id();
        if ($uid) {
            $response = new RedirectResponse('/');
            $response->send();
            return $response;
        }

        // To get forms and save them in the variable data
        $formRegister = \Drupal::formBuilder()->getForm('Drupal\pokemon\Form\AddUserForm');

        $data = [
            'register' => $formRegister,
        ];

        // Return page
        $page = [
        '#theme' => 'signup',
        '#data' => $data,
        '#cache' => [
            'max-age' => 0
        ]
        ];

        return $page;
    }
}
