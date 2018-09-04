<?php

namespace Drupal\date_interval;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Extends DateIntervalPlus().
 *
 * This class extends the basic component and adds in Drupal-specific
 * handling, like translation of the format() method.
 *
 * Static methods in base class can also be used to create
 * DrupalDateInterval objects. For example:
 *
 * DrupalDateInterval::createFromIntervalSpec('P2Y', $settings)
 *
 * @see \Drupal\date_interval\DateIntervalPlus.php
 *
 * @package Drupal\date_interval
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DrupalDateInterval extends DateIntervalPlus implements DateIntervalInterface {

  use StringTranslationTrait;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The translation context.
   *
   * @var array
   */
  private $settings;

  /**
   * Constructs a date interval plus object.
   *
   * @param string|null $intervalSpec
   *   An interval specification.
   * @param int $invert
   *   Whether to invert the interval.
   *   Is 1 if the interval represents a negative
   *   time period and 0 otherwise.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct($languageManager, $intervalSpec = NULL, $invert = 0, array $settings = []) {
    parent::__construct($intervalSpec, $invert, $settings);
    $this->settings = $this->prepareSettings($settings);
    $this->stringTranslation = $this->getStringTranslation();
  }

  /**
   * Prepare the settings array.
   *
   * @param array $settings
   *   The passed settings.
   *
   * @return mixed
   *   The passed setting merged with defaults.
   */
  protected function prepareSettings(array $settings = []) {
    if (!isset($settings['langcode'])) {
      $languageManager = \Drupal::getContainer()->get('language_manager');
      $settings['langcode'] = $languageManager->getCurrentLanguage()->getId();
    }
    if (!isset($settings['context'])) {
      $settings['context'] = 'date_interval';
    }

    return $settings;
  }

}
