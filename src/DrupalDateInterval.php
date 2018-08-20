<?php

namespace Drupal\date_interval;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
 */
class DrupalDateInterval extends DateIntervalPlus {

  use StringTranslationTrait;

  /**
   * Define indexes for our mappings.
   */
  const SINGULAR = 0;
  const PLURAL = 1;

  /**
   * The the string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The translation context.
   *
   * @var string
   */
  protected $context;

  /**
   * Constructs a date interval.
   *
   * @param string $intervalSpec
   *   An interval specification.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct(string $intervalSpec, array $settings) {

    $this->stringTranslation = $this->getStringTranslation();
    $this->languageManager = \Drupal::getContainer()->get('language_manager');
    if (!isset($settings['langcode'])) {
      $settings['langcode'] = $this->languageManager->getCurrentLanguage()->getId();
    }

    $this->context = isset($settings['context']) ? $settings['context'] : 'Drupal date interval';

    // Instantiate the parent class.
    parent::__construct($intervalSpec, $settings);
  }

  /**
   * Format the interval.
   *
   * @param string $format
   *   The format consisting of only valid literals.
   *   Other items will be removed.
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
   * @param bool $removeEmptyValues
   *   Boolean whether to remove empty values or leave them. Defaults to TRUE.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   *   - context: (optional) Translation context to set on the units.
   *
   * @return string
   *   The formatted string.
   */
  public function format($format, $units = TRUE, $separator = ' ', $removeEmptyValues = TRUE, array $settings = []) {

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
      if ($removeEmptyValues) {

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

    $mappings = $this->getFormatsMappedToUnits($settings);

    // Loop over the format literals that we mapped
    // to properties on the date interval object..
    foreach ($this->getLiteralsMappedToProperties() as $literal => $property) {

      // When the option to add units is set we will add a unit
      // to the to the format string based on the mapped units.
      if ($units) {

        // Make sure the property exsists just to be save and
        // check if it's equal to zero aka empty.
        if (property_exists($this->getPhpDateInterval(), $property)) {

          // Full text strings should contain a
          // space between number and string.
          $prefix = ' ';
          $mappingKey = $this->getPhpDateInterval()->{$property} === 1 ? DrupalDateInterval::SINGULAR : DrupalDateInterval::PLURAL;

          foreach ($formatArray as $key => $item) {

            // Loop the array keys and when literal is somewhere in key
            // append the correct unit.
            if (strpos($item, $literal) !== FALSE) {

              // The default mapped unit.
              $mappedUnit = $mappings[$literal][$mappingKey];

              // User can provide custom units per literal so we check for them.
              if (is_array($units) && isset($units[$literal][$mappingKey])) {
                $mappedUnit = $units[$literal][$mappingKey];
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
    $format = implode($separator, $formatArray);

    return parent::format($format);
  }

  /**
   * Get the formats mapped to units.
   *
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   *   - context: (optional) Translation context to set on the units.
   *
   * @return array
   *   A mapped array by index.
   */
  private function getFormatsMappedToUnits(array $settings = []) {

    // Check if a language code is passed else fallback to
    // the language intialized in the constructor.
    $langcode = !empty($settings['langcode']) ? $settings['langcode'] : $this->langcode;

    // Check if a language code is passed else fallback to
    // the language intialized in the constructor.
    $context = !empty($settings['context']) ? $settings['context'] : $this->context;

    return [
      '%Y' => [
        $this->t('year', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('years', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%y' => [
        $this->t('year', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('years', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%M' => [
        $this->t('month', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('months', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%m' => [
        $this->t('month', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('months', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%D' => [
        $this->t('day', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('days', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%d' => [
        $this->t('day', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('days', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%H' => [
        $this->t('hour', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hours', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%h' => [
        $this->t('hour', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hours', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%I' => [
        $this->t('minute', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minutes', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%i' => [
        $this->t('minute', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minutes', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%S' => [
        $this->t('second', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('seconds', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%s' => [
        $this->t('second', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('seconds', [], ['langcode' => $langcode, 'context' => $context]),
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
