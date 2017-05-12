<?php

namespace Drupal\as_book\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\as_book\reservationHandlerService;

/**
 * Class DefaultController.
 *
 * @package Drupal\as_book\Controller
 */
class DefaultController extends ControllerBase {

    /**
     * Drupal\Core\Entity\EntityTypeManager definition.
     *
     * @var \Drupal\Core\Entity\EntityTypeManager
     */
    protected $entityTypeManager;

    /**
     * Drupal\as_book\reservationHandlerService definition.
     *
     * @var \Drupal\as_book\reservationHandlerService
     */
    protected $reservationHandler;

    /**
     * Constructs a new DefaultController object.
     */
    public function __construct(EntityTypeManager $entity_type_manager, reservationHandlerService $reservation_handler) {
        $this->entityTypeManager = $entity_type_manager;
        $this->reservationHandler = $reservation_handler;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('entity_type.manager'),
            $container->get('reservation.handler')
        );
    }

    /**
     * Bookreservationajax.
     *
     * @return string
     *   Return Hello string.
     */
    public function bookReservationAjax(int $book_id, int $user_id, $mode) {

        $this->reservationHandler->createNewReservation($book_id, $user_id);

        $message = 'Votre réservation a bien été prise en compte.';

        if ($mode == 'nojs') {
            drupal_set_message($message);
            $route_name = 'entity.node.canonical';
            $parameters = ['node' => $book_id];
            return $this->redirect($route_name, $parameters);
        }

        if ($mode == 'ajax') {
            $message = '
                <p>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                        . $message .

                    '</div>
                </p>';

            $command = new BeforeCommand('#bookReservationButton', $message);
            $response = new AjaxResponse();
            $response->addCommand($command);

            return $response;
        }
    }

}
