<?php

namespace Drupal\uiowa_core\Entity;

use Drupal\node\Entity\Node;

/**
 * Bundle-specific subclass of Node.
 */
abstract class NodeBundleBase extends Node implements RendersAsCardInterface {

  use RendersAsCardTrait;

  /**
   * If entity has link directly to source field.
   *
   * @var string|null
   *   field name or null.
   */
  protected $sourceLinkDirect = NULL;

  /**
   * If entity has source link field.
   *
   * @var string|null
   *   field name or null.
   */
  protected $sourceLink = NULL;

  /**
   * {@inheritdoc}
   */
  public function buildCard(array &$build) {
    $this->buildCardStyles($build);
    // @todo How to handle setting the headline size?
    // @todo Do we need any of the '.node' or '.node--*' classes? E.g.:
    //   'node',
    //   'node--type-' ~ node.bundle|clean_class,
    //   node.isPromoted() ? 'node--promoted',
    //   node.isSticky() ? 'node--sticky',
    //   not node.isPublished() ? 'node--unpublished',
    //   view_mode ? 'node--view-mode-' ~ view_mode|clean_class,
    // Add shared fields to card.
    $this->mapFieldsToCardBuild($build, [
      '#media' => 'field_image',
      '#title' => 'title',
      '#content' => 'field_teaser',
    ]);

    // Handle link directly to source functionality.
    $build['#url'] = $this->getNodeUrl();

    // Create heading_size variable for node teaser templates if a
    // corresponding render property was set.
    if (isset($variables['elements']['#heading_size'])) {
      $building['heading_size'] = $variables['elements']['#heading_size'];
    }

    // Determine whether the link indicator should be set.
    $config_name = 'sitenow_' . $this->bundle() . '.settings';
    $config = \Drupal::configFactory()->getEditable($config_name);
    $link_indicator = $config->get('show_teaser_link_indicator') ?? FALSE;
    $build['#link_indicator'] = $link_indicator;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultCardStyles(): array {
    return [
      'card_media_position' => 'card--layout-right',
      'media_format' => 'media--widescreen',
      'media_size' => 'media--medium',
      'styles' => 'borderless',
    ];
  }

  /**
   * Helper function to construct link directly to source functionality.
   *
   * @return string|null
   *   The url used to link the view mode.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getNodeUrl(): ?string {
    $source_link_direct = $this->sourceLinkDirect;
    $source_link = $this->sourceLink;

    if (!is_null($source_link_direct) || !is_null($source_link)) {
      $link_direct = (int) $this->get($source_link_direct)->value;
      $link = $this->get($source_link)->uri;
      if ($link_direct === 1 && isset($link) && !empty($link)) {
        return $this
          ->get($source_link)
          ?->get(0)
          ?->getUrl()
          ?->toString();
      }
    }

    return !$this->isNew() ? $this->toUrl()->toString() : NULL;
  }

}
