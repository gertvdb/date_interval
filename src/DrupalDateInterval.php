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
 */
class DrupalDateInterval extends DateIntervalPlus {

  use StringTranslationTrait;

  /**
   * Define indexes for our mappings.
   */
  const ABBR_SINGULAR = 0;
  const ABBR_PLURAL = 1;
  const SINGULAR = 2;
  const PLURAL = 3;

  /**
   * The translation context.
   *
   * @var string
   */
  protected $context;

  /**
   * Constructs a date interval.
   *
   * @param string $interval_spec
   *   An interval specification.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct(string $interval_spec = 'P1D', array $settings = []) {
    if (!isset($settings['langcode'])) {
      $settings['langcode'] = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }

    $this->context = isset($settings['context']) ? $settings['context'] : 'Drupal date interval';

    // Instantiate the parent class.
    parent::__construct($interval_spec, $settings);
  }

  /**
   * Overrides format().
   *
   * @param string $format
   *   A format string using the format options from \DateInterval.
   * @param bool $show_units
   *   A boolean indicating whether translatable units
   *   should be appended to the values.
   * @param bool $abbr_units
   *   A boolean indicating whether translatable units
   *   should be abbreviated. This will only be done when
   *   $show_units is set to TRUE.
   * @param bool $remove_empty_values
   *   If we just print an interval using the format function, we don't always
   *   know beforehand which format to pass exactly, to make sure it's properly
   *   presented. When using '%d %h %i', we prefer not to get this :
   *     - 1 day 0 hours 4 minutes
   *   but instead, we prefer to get something like this :
   *     - 1 day 4 minutes
   *   Setting this values to TRUE will handle this.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   *   - context: (optional) Translation context to set on the units.
   */
  public function format($format, $show_units = FALSE, $abbr_units = FALSE, $remove_empty_values = TRUE, array $settings = []) {

    // Convert the format to an array based on spaces
    // so we have a better way to handle whitespace.
    $format_array = explode(' ', $format);

    // Loop over the format literals that we mapped
    // to properties on the date interval object..
    foreach ($this->getLiteralsMappedToProperties() as $literal => $property) {

      // When the option to remove empty values is passed we need
      // to filter out all literals that have a property on the date
      // interval object that's set to zero.
      if ($remove_empty_values) {

        // Make sure the property exsists just to be save and
        // check if it's equal to zero aka empty.
        if (property_exists($this->getPhpDateInterval(), $property) && $this->getPhpDateInterval()->{$property} === 0) {

          // Loop the array keys and when literal is somewhere in key
          // remove the entire key.
          foreach ($format_array as $key => $item) {
            if (strpos($item, $literal) !== FALSE) {
              unset($format_array[$key]);
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
      if ($show_units) {

        // Make sure the property exsists just to be save and
        // check if it's equal to zero aka empty.
        if (property_exists($this->getPhpDateInterval(), $property)) {

          if ($abbr_units) {
            // Abbreviations should stick to the number.
            $prefix = '';
            $mapping_key = $this->getPhpDateInterval()->{$property} === 1 ? DrupalDateInterval::ABBR_SINGULAR : DrupalDateInterval::ABBR_PLURAL;
          }
          else {
            // Full text strings should contain a
            // space between number and string.
            $prefix = ' ';
            $mapping_key = $this->getPhpDateInterval()->{$property} === 1 ? DrupalDateInterval::SINGULAR : DrupalDateInterval::PLURAL;
          }

          foreach ($format_array as $key => $item) {

            // Loop the array keys and when literal is somewhere in key
            // append the correct unit.
            if (strpos($item, $literal) !== FALSE) {
              $replace = str_replace($item, $literal . $prefix . $mappings[$literal][$mapping_key], $literal);
              $format_array[$key] = $replace;
            }

          }

        }

      }
    }

    // Convert the format back to a string to pass
    // to format function of DateInterval.
    $format = implode(' ', $format_array);

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
        $this->t('y', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('yrs', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('year', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('years', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%y' => [
        $this->t('y', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('yrs', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('year', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('years', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%M' => [
        $this->t('mth', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('mths', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('month', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('months', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%m' => [
        $this->t('mth', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('mths', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('month', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('months', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%D' => [
        $this->t('d', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('d', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('day', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('days', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%d' => [
        $this->t('d', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('d', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('day', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('days', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%H' => [
        $this->t('h', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hrs', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hour', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hours', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%h' => [
        $this->t('h', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hrs', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hour', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('hours', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%I' => [
        $this->t('min', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('min', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minute', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minutes', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%i' => [
        $this->t('min', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('min', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minute', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('minutes', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%S' => [
        $this->t('sec', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('sec', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('second', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('seconds', [], ['langcode' => $langcode, 'context' => $context]),
      ],
      '%s' => [
        $this->t('sec', [], ['langcode' => $langcode, 'context' => $context]),
        $this->t('sec', [], ['langcode' => $langcode, 'context' => $context]),
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
