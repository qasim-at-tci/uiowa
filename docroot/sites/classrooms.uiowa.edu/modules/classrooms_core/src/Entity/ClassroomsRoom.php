<?php

namespace Drupal\classrooms_core\Entity;

use Drupal\uiowa_core\Entity\NodeBundleBase;
use Drupal\uiowa_core\Entity\RendersAsCardInterface;

/**
 * Provides an interface for area of study page entries.
 */
class ClassroomsRoom extends NodeBundleBase implements RendersAsCardInterface {

  /**
   * {@inheritdoc}
   */
  public function buildCard(array &$build) {
    parent::buildCard($build);

    // Process additional card mappings.
    $this->mapFieldsToCardBuild($build, [
      '#meta' => [
        'field_room_max_occupancy',
        'field_room_responsible_unit',
      ],
    ]);

    $building_id = $this->get('field_room_building_id')->target_id;

    // Get the building ID and room ID fields.
    $room_id = $this->get('field_room_room_id')->value;

    // Combine the fields to create the title.
    $title_combined = strtoupper($building_id) . ' ' . $room_id;
    $title = ['#markup' => $title_combined];
    $build['#title'] = $title;
    $build['#link_indicator'] = TRUE;

  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultCardStyles(): array {
    return [
      ...parent::getDefaultCardStyles(),
      'card_media_position' => 'card--stacked',
      'media_size' => 'media--large',
      'headline_class' => 'headline--serif headline--underline h4',
      'styles' => 'card--button-align-bottom',
      'border' => '',
    ];
  }

}
