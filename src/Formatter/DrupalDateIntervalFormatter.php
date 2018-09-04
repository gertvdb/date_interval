<?php

namespace Drupal\date_interval\Formatter;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\date_interval\DateIntervalPlus;

/**
 * Formatter for DateInterval.
 *
 * @package Drupal\date_interval
 */
class DrupalDateIntervalFormatter implements DateIntervalFormatterInterface {

  use StringTranslationTrait;

  /**
   * Define indexes for our mappings.
   */
  const SINGULAR = 0;
  const PLURAL = 1;

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
   * Optional units.
   *
   * When TRUE the default units will be appended.
   * When FALSE no units will be appended.
   * When an array is provided the user can override the units.
   *
   * @var array|bool
   */
  private $units;

  /**
   * The separator.
   *
   * @var bool|string
   */
  private $separator;

  /**
   * Whether to show or remove empty values.
   *
   * @var bool
   */
  private $removeEmptyValues;

  /**
   * Constructs a date interval.
   *
   * @param bool $removeEmptyValues
   *   Boolean whether to remove empty values or leave them. Defaults to TRUE.
   * @param bool|array $units
   *   When TRUE the default units will be appended.
   *   When FALSE no units will be appended.
   *   When an array is provided the user can override the units.
   *   ex. :
   *   [
   *      '%d' => [
   *        DrupalDateInterval::SINGULAR => t('Unit for singular days'),
   *        DrupalDateInterval::PLURAL => t('Unit for plural days'),
   *      ]
   *   ];
   *   This way the user can fully customize the display for advanced use cases.
   * @param bool|string $separator
   *   The separator to add between the literals. Defaults to a space.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct($removeEmptyValues = TRUE, $units = TRUE, $separator = ' ', array $settings = []) {
    $this->removeEmptyValues = $removeEmptyValues;
    $this->units = $units;
    $this->separator = $separator;
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

  /**
   * Format the interval.
   *
   * @param \Drupal\date_interval\DateIntervalPlus $dateIntervalPlus
   *   A date interval plus object.
   * @param string $format
   *   The format consisting of only valid literals.
   *   Other items will be removed.
   *
   * @return string
   *   The formatted string.
   */
  public function format(DateIntervalPlus $dateIntervalPlus, $format) {

    // Convert the format to an array based on % literal
    // so we have a better way to handle the format.
    $formatArray = explode('%', $format);

    // Remove empty values.
    $formatArray = array_filter($formatArray);

    // Re add the cutoff % literal sign and remove whitespace.
    foreach ($formatArray as $key => $value) {
      $formatArray[$key] = '%' . preg_replace('/\s+/', '', $value);
    }

    // Filter out every item that's not part of the literals.
    $formatArray = array_filter($formatArray, function ($value) {
      $literals = array_keys($this->getLiteralsMappedToProperties());

      return in_array($value, $literals);
    });

    // Loop over the format literals that we mapped
    // to properties on the date interval object..
    foreach ($this->getLiteralsMappedToProperties() as $literal => $property) {

      // When the option to remove empty values is passed we need
      // to filter out all literals that have a property on the date
      // interval object that's set to zero.
      if ($this->removeEmptyValues) {

        // Make sure the property exists just to be save and
        // check if it's equal to zero aka empty.
        if (property_exists($this->getPhpDateInterval(), $property) && $this->getPhpDateInterval()->{$property} === 0) {

          // Loop the array keys and when literal is somewhere in key
          // remove the entire key.
          foreach ($formatArray as $key => $item) {
            if (strpos($item, $literal) !== FALSE) {
              unset($formatArray[$key]);
            }
          }

        }
      }
    }

    $mappings = $this->getFormatsMappedToUnits($this->settings);

    // Loop over the format literals that we mapped
    // to properties on the date interval object..
    foreach ($this->getLiteralsMappedToProperties() as $literal => $property) {

      // When the option to add units is set we will add a unit
      // to the to the format string based on the mapped units.
      if ($this->units) {

        // Make sure the property exsists just to be save and
        // check if it's equal to zero aka empty.
        if (property_exists($this->getPhpDateInterval(), $property)) {

          // Full text strings should contain a
          // space between number and string.
          $prefix = ' ';
          $mappingKey = $this->getPhpDateInterval()->{$property} === 1 ? DrupalDateIntervalFormatter::SINGULAR : DrupalDateIntervalFormatter::PLURAL;

          foreach ($formatArray as $key => $item) {

            // Loop the array keys and when literal is somewhere in key
            // append the correct unit.
            if (strpos($item, $literal) !== FALSE) {

              // The default mapped unit.
              $mappedUnit = $mappings[$literal][$mappingKey];

              // User can provide custom units per literal so we check for them.
              if (is_array($this->units) && isset($this->units[$literal][$mappingKey])) {
                $mappedUnit = $this->units[$literal][$mappingKey];
              }

              $replace = str_replace($item, $literal . $prefix . $mappedUnit, $literal);
              $formatArray[$key] = $replace;
            }

          }

        }

      }
    }

    // Convert the format back to a string to pass
    // to format function of DateInterval.
    $format = implode($this->separator, $formatArray);
    return $dateIntervalPlus->format($format);
  }

  /**
   * Get the formats mapped to units.
   *
   * @return array
   *   A mapped array by index.
   */
  private function getFormatsMappedToUnits() {
    return [
      '%Y' => [
        $this->t('year', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('years', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%y' => [
        $this->t('year', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('years', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%M' => [
        $this->t('month', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('months', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%m' => [
        $this->t('month', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('months', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%D' => [
        $this->t('day', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('days', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%d' => [
        $this->t('day', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('days', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%H' => [
        $this->t('hour', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('hours', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%h' => [
        $this->t('hour', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('hours', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%I' => [
        $this->t('minute', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('minutes', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%i' => [
        $this->t('minute', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('minutes', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%S' => [
        $this->t('second', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('seconds', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
      '%s' => [
        $this->t('second', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
        $this->t('seconds', [], ['langcode' => $this->settings['langcode'], 'context' => $this->settings['context']]),
      ],
    ];
  }

  /**
   * Get the literals mapped to properties.
   *
   * @return array
   *   A mapped array.
   */
  private function getLiteralsMappedToProperties() {
    return [
      '%Y' => 'y',
      '%y' => 'y',
      '%M' => 'm',
      '%m' => 'm',
      '%D' => 'd',
      '%d' => 'd',
      '%H' => 'h',
      '%h' => 'h',
      '%I' => 'i',
      '%i' => 'i',
      '%S' => 's',
      '%s' => 's',
    ];
  }

}
