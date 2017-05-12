<?php

namespace Drupal\as_book\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class BookReservationForm.
 *
 * @package Drupal\as_book\Form
 */
class BookReservationForm extends FormBase {

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;
  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;
  /**
   * Constructs a new BookReservationForm object.
   */
  public function __construct(
    RequestStack $request_stack,
    AccountProxy $current_user,
    EntityTypeManager $entity_type_manager
  ) {
    $this->requestStack = $request_stack;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'book_reservation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

      //    Préparation des données utilisateur et livre.
      $book = $this->requestStack
          ->getCurrentRequest()
          ->get('node');

    $form['member'] = [
        '#type' => 'hidden',
        '#value' => $this->currentUser->id(),
    ];
    $form['book'] = [
        '#type' => 'hidden',
        '#value' => $book->id(),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Réserver ce livre'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    //      Chargement des objets d'entité du livre et de l'utilisateur connecté.
      $user = $this->entityTypeManager
          ->getStorage('user')
          ->load($form_state->getValue('member'));
      $book = $this->entityTypeManager
          ->getStorage('node')
          ->load($form_state->getValue('book'));

    //      Préparation des données des champs de la réservation.
      $values = [
          'title' => 'Réservation de ' . $book->label() . ' pour ' . $user->getUsername(),
          'type' => 'reservation',
          'field_book' => $book->id(),
          'field_user' => $user->id(),
      ];

    //      Création de l'instance d'Entité de la réservation grâce au
    //      service 'entity_type.manager'.
      $reservation = $this->entityTypeManager
          ->getStorage('node')
          ->create($values);
    //      Sauvegarde en base de donnée de la réservation.
      $reservation->save();

    // Affichage du message de confirmation.
      drupal_set_message('Votre réservation a bien été prise en compte.');

  }

}
