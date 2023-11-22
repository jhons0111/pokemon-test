<?php

namespace Drupal\pokemon\Form;

use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the sign up form.
 */
class AddUserForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'addUserForm';
    }

    /**
     * Build function
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Email adress'),
            '#required' => TRUE,
            '#attributes' => array(
                'class' => array('email'),
                'placeholder' => $this->t('Email adress'),
            ),
        );

        $form['firstname'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#required' => TRUE,
            '#attributes' => array(
                'class' => array('name'),
                'onkeypress' => "return /[a-z-' ']/i.test(event.key)",
                'placeholder' => $this->t('Name'),
            ),
        );

        $form['lastname'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Lastname'),
            '#required' => TRUE,
            '#attributes' => array(
                'class' => array('abi-form_input'),
                'onkeypress' => "return /[a-z-' ']/i.test(event.key)",
                'placeholder' => $this->t('Lastname'),
            ),
        );

        $form['phone'] = [
            '#type' => 'textfield',
            '#title' => 'Phone*',
            '#maxlength'  => 10,
            '#required' => TRUE,
            '#attributes' => [
                'class' => ['cellphone'],
                'placeholder' => $this->t('Phone'),
                'onkeypress' => "return /^[0-9\s]*$/.test(event.key)",
            ],
        ];

        $form['password'] = [
            '#type' => 'password',
            '#title' => 'Password',
            '#size' => 20,
            '#required' => TRUE,
            '#attributes' => [
                'autocomplete' => 'new-password',
            ],
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Sign up')

        ];

        return $form;
    }

    /**
     * Validate function
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

        //Verify if the email already exists
        $userByEmail = user_load_by_mail($form_state->getValue('email'));
        if ($userByEmail) {

            $form_state->setErrorByName('email', $this->t('<p class="hidden-form-error">The email adress already exists ' . $form_state->getValue('email') . '</p>'));
            $form['email']['#suffix'] = "<span class='exist-user emailUser'>The email adress already exists</span>";
            $form_state->setRebuild();
        }

        //Verify if the phone number is correct.
        $phone = $form_state->getValue('phone');
        if (strlen($phone) > 10) {

            $form_state->setErrorByName('phone', $this->t('<span class="hidden-form-error">The phone number must be less than 10 digits.</span>')
            );
            $form['phone']['#suffix'] = "<span class='exist-user phoneUser'>The phone number must be less than 10 digits.</span>";
            $form_state->setRebuild();
        }
    }

    /**
     * Submit function
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        //We create a new user with the form fields
        $user = user_load_by_mail($form_state->getValue('email'));
        if ($user == false) {
            $user = User::create(array(
                'name' => $form_state->getValue('email'),
                'mail' => $form_state->getValue('email'),
                'field_name' => $form_state->getValue('firstname'),
                'field_lastname' => $form_state->getValue('lastname'),
                'field_phone' => $form_state->getValue('phone'),
                'roles' => array(),
                'status' => 1,
                'pass' => $form_state->getValue('password')
            ));
            $user->save();
        }

        //We log in the user and redirect to favorites page 
        $userlogin = user_load_by_mail($form_state->getValue('email'));
        $userlogin->activate();
        user_login_finalize($userlogin);
        $form_state->setRedirectUrl(Url::fromUri('internal:/favorites'));
    }
}
