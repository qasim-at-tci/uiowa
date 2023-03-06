<?php

namespace Drupal\uiowa_core\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\uiowa_core\Entity\RendersAsCardInterface;
use Drupal\uiowa_core\Entity\RendersAsCardTrait;

/**
 * Provides an interface for paragraph timeline items.
 */
class TimelineItem extends Paragraph implements RendersAsCardInterface {

  use RendersAsCardTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildCard(array &$build) {
    $this->buildCardStyles($build);

    // Process additional card mappings.
    $this->mapFieldsToCardBuild($build, [
      '#title' => 'field_timeline_heading',
      '#content' => 'field_timeline_body',
      '#subtitle' => 'field_timeline_date',
      '#media' => 'field_timeline_media',
      '#meta' => 'field_timeline_icon',
    ]);

    // Check if the timeline card should be linked.
    $source_link = 'field_timeline_link';
    $link = $this->get($source_link)->uri;
    if (isset($link) && !empty($link)) {
      $build['#url'] = $this
        ->get($source_link)
        ?->get(0)
        ?->getUrl()
        ?->toString();
    }
    // If we don't have a link set,
    // then we don't want the card linked at all.
    else {
      $build['#url'] = '';
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultCardStyles(): array {
    return [
      'headline_class' => 'headline--serif',
      'media_size' => 'media--large',
      'timeline--card' => 'timeline--card',
    ];
  }

}
