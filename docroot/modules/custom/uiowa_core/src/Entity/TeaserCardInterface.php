<?php

namespace Drupal\uiowa_core\Entity;

/**
 * Defines the interface for content types use teaser view modes.
 */
interface TeaserCardInterface {

  /**
   * Set the build to render as a card.
   *
   * @param array $build
   *   The renderable build array.
   */
  public function addCardBuildInfo(array &$build);

  /**
   * Build card teaser render array.
   *
   * @param array $build
   *   The renderable build array.
   */
  public function buildCard(array &$build);

  /**
   * Return an array mapping card style group names to classes.
   *
   * @return array
   */
  public function getDefaultCardStyles(): array;

  /**
   * Add card styles to the build array.
   *
   * @param $build
   *   The build array that is being updated.
   */
  public function buildCardStyles(array &$build);
}
