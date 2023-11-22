<?php

namespace Drupal\pokemon\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the login form.
 */

class LoginForm extends FormBase{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'loginForm';
    }

    /**
     * Build function
     */

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['name'] = [
            '#type' => 'email',
            '#title' => $this->t('Email address'),
            '#attributes' => [
                'placeholder' => $this->t('Type your email address'),
                'autocomplete' => 'username',
            ],
            '#required' => TRUE,
        ];
        $form['pass'] = [
            '#type' => 'password',
            '#title' => $this->t('Password'),
            '#attributes' => [
                'placeholder' => $this->t('Type your password'),
                'autocomplete' => 'current-password',
            ],
            '#required' => TRUE,
        ];
        $form['submit'] = [
            '#type' => 'submit',
            '#value' =>  'Sign in',
        ];
        return $form;
    } 

    /**
     * Validate function
     */
    public function validateForm(array &$form, FormStateInterface $form_state){

        // Validate if user and password are well
        $email = $form_state->getValue('name');
        $pass  = $form_state->getValue('pass');

        $uid = \Drupal::service('user.auth')->authenticate(
            $email,
            $pass
        );

        // Action if the access is not well
        if (!$uid) {

            $message = $this->t('<p class="hidden-form-error">Incorrect username or password.</p>');
            $form_state->setErrorByName('pass', $message);
            $form['pass']['#suffix'] = "<span class='incorrect-login'>Incorrect username or password.</span>";
            $form_state->setRebuild();
        }
    }

    /**
     * Submit function
     */
    public function submitForm(array &$form, FormStateInterface $form_state){

        $email = $form_state->getValue('name');
        $userByEmail = user_load_by_mail($email);

        // Login and redirect user
        user_login_finalize($userByEmail);
        $form_state->setRedirectUrl(Url::fromUri('internal:/favorites'));
    }
}
