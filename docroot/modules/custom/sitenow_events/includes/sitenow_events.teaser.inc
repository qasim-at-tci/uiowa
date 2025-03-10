<?php

/**
 * @file
 * Teaser theme functions.
 */

use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\smart_trim\TruncateHTML;

/**
 * Theme function for an event teaser.
 *
 * @param array $variables
 *   Variables from the theme function.
 */
function template_preprocess_sitenow_events_teaser(array &$variables) {
  $config = \Drupal::config('sitenow_events.settings');

  if (isset($variables['event']['media'][0]['original_image'])) {
    // Establish default view mode if layout_builder_styles don't exist.
    $image_view_mode = 'large__square';

    // Map the layout builder styles to the view mode that should be used.
    $media_formats = [
      'media--widescreen' => 'large__widescreen',
      'media--square' => 'large__square',
      'media--circle' => 'large__square',
    ];
    // Loop through the map to check if any of them are being used and
    // adjust the view mode accordingly. Last one wins out.
    if (isset($variables["event"]["parent_lb_styles"])) {
      foreach ($media_formats as $style => $view_mode) {
        if (in_array($style, $variables["event"]["parent_lb_styles"])) {
          $image_view_mode = $view_mode;
        }
      }
    }
    $variables['event']['image'] = [
      '#theme' => 'imagecache_external_responsive',
      '#uri' => $variables['event']['media'][0]['original_image'],
      '#responsive_image_style_id' => $image_view_mode,
      '#attributes' => [
        'data-lazy' => TRUE,
        'alt' => t('@title promotional image', ['@title' => $variables['event']['title']]),
      ],
    ];
  }

  $external_link = ($config->get('event_link') === 'event-link-external');

  if ($external_link) {
    $variables['event']['url'] = $variables['event']['events_site_url'];
  }
  else {
    $single_event_path = $config->get('single_event_path');

    // Add a parameter on the URL for event instances.
    foreach ($variables['event']['event_instances'] as $key => $instance) {
      if ($variables['event']['instance_start'] === $instance['event_instance']['start']) {
        $instance_id = $key;
      }
    }

    $event_path = isset($instance_id) ? "{$variables['event']['id']}/{$instance_id}" : $variables['event']['id'];
    $variables['event']['url'] = Url::fromUri("internal:/{$single_event_path}/{$event_path}", ['alias' => TRUE]);
  }

  $variables['event']['date_string'] = sitenow_events_format_instance([
    'start' => $variables['event']['instance_start'],
    'end' => $variables['event']['instance_end'],
    'last_date' => $variables['event']['last_date'],
    'all_day' => $variables['event']['instance_all_day'],
  ]);

  // Limit what html is allowed.
  $allowed_tags = ['a', 'strong', 'em'];

  if (isset($variables['event']['summary'])) {
    $variables['event']['description'] = Xss::filter($variables['event']['summary'], $allowed_tags);
  }

  elseif (isset($variables['event']['description'])) {
    $variables['event']['description'] = Xss::filter($variables['event']['description'], $allowed_tags);

    // Use smart_trim's helper to safely truncate without breaking HTML.
    $truncate = new TruncateHTML();
    $trim_suffix = '...';
    $variables['event']['description'] = $truncate->truncateChars($variables['event']['description'], 500, $trim_suffix);
  }
}
