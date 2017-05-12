<?php

namespace Drupal\as_book\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Provides a 'BookReservationBlock' block.
 *
 * @Block(
 *  id = "book_reservation_block",
 *  admin_label = @Translation("Book reservation block"),
 * )
 */
class BookReservationBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
     * Constructs a new BookReservationBlock object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param string $plugin_definition
     *   The plugin implementation definition.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        RequestStack $request_stack,
        AccountProxy $current_user,
        EntityTypeManager $entity_type_manager
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->requestStack = $request_stack;
        $this->currentUser = $current_user;
        $this->entityTypeManager = $entity_type_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('request_stack'),
            $container->get('current_user'),
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build() {

        //    Préparation des données utilisateur et livre.
        $book = $this->requestStack
            ->getCurrentRequest()
            ->get('node');

        //  Affichage via le fichier de template.
        $build['book_reservation_block'] = [
            '#theme' => 'reservation_link',
            '#book_id' => $book->id(),
            '#user_id' => $this->currentUser->id(),
        ];

        return $build;
    }

}
