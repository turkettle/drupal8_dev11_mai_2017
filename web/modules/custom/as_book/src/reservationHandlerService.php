<?php

namespace Drupal\as_book;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class reservationHandlerService.
 *
 * @package Drupal\as_book
 */
class reservationHandlerService {

    /**
     * Drupal\Core\Entity\EntityTypeManager definition.
     *
     * @var \Drupal\Core\Entity\EntityTypeManager
     */
    protected $entityTypeManager;

    /**
     * Constructs a new reservationHandlerService object.
     */
    public function __construct(EntityTypeManager $entity_type_manager) {
        $this->entityTypeManager = $entity_type_manager;
    }

    public function createNewReservation(int $book_id, int $user_id) {

        //      Chargement des objets d'entité du livre et de l'utilisateur connecté.
        $user = $this->entityTypeManager
            ->getStorage('user')
            ->load($user_id);
        $book = $this->entityTypeManager
            ->getStorage('node')
            ->load($book_id);

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
        if ($reservation->save()) {
            return $reservation;
        }

        return false;
    }
}
