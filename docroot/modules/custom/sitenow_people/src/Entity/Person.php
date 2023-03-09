<?php

namespace Drupal\sitenow_people\Entity;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\uiowa_core\Entity\NodeBundleBase;
use Drupal\uiowa_core\Entity\RendersAsCardInterface;

/**
 * Provides an interface for person entries.
 */
class Person extends NodeBundleBase implements RendersAsCardInterface {

  use StringTranslationTrait;

  /**
   * If entity has link directly to source field.
   *
   * @var string|null
   *   field name or null.
   */
  protected $sourceLinkDirect = 'field_person_website_link_direct';

  /**
   * If entity has source link field.
   *
   * @var string|null
   *   field name or null.
   */
  protected $sourceLink = 'field_person_website';

  /**
   * {@inheritdoc}
   */
  public function buildCard(array &$build) {
    parent::buildCard($build);

    // Add the person library.
    $build['#attached']['library'][] = 'uids_base/person';
    // Add the media library.
    $build['#attached']['library'][] = 'uids_base/media';

    // Handle the case when there is no image.
    if (empty($build['#media'])) {
      $build['#media']['empty'] = [
        '#markup' => '<div class="img--empty">&nbsp;</div>',
      ];
    }

    // Process additional card mappings.
    $this->mapFieldsToCardBuild($build, [
      '#subtitle' => 'field_person_position',
      '#meta' => [
        'field_person_email',
        'field_person_phone',
      ],
    ]);

    // Handle link directly to source functionality.
    $build['#url'] = $this->getNodeUrl();

    // Append person credentials to the node label in the teaser view mode.
    if ($creds = $this->get('field_person_credential')?->getString()) {
      $title = $this->getTitle();
      $build['#title'] = $this->t('@title, @creds', [
        '@title' => $title,
        '@creds' => $creds,
      ]);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultCardStyles(): array {
    $default_classes = [
      ...parent::getDefaultCardStyles(),
      'card_media_position' => 'card--layout-left',
      'media_border' => 'media--border',
      'media_format' => 'media--circle',
      'media_size' => 'media--small',
    ];

    return $default_classes;
  }

}
