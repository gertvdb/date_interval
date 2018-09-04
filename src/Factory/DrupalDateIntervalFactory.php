<?php

namespace Drupal\date_interval\Factory;

use Drupal\date_interval\DrupalDateIntervalPlus;
use Drupal\date_interval\DateIntervalInterface;

/**
 * Class NodePreprocessEventFactory.
 */
final class DrupalDateIntervalFactory extends DateIntervalPlusFactory implements DateIntervalFactoryInterface {

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param \Drupal\date_interval\DateIntervalPlus $dateIntervalPlus
   *   A php DateIntervalPlus object.
   *
   * @return \Drupal\date_interval\DrupalDateInterval
   *   A DrupalDateInterval object
   */
  public static function createFromDateIntervalPlus(\DateIntervalPlus $dateIntervalPlus) {

  }

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param string $intervalSpec
   *   A interval spec.
   * @param bool $invert
   *   Whether to invert the interval.
   * @param array $settings
   *   An array of settings.
   *
   * @return \Drupal\date_interval\DateIntervalInterface
   *   A DateIntervalPlus object
   */
  public static function createFromIntervalSpec(string $intervalSpec, $invert = FALSE, array $settings = []) {
    $dateIntervalPlus = parent::createFromIntervalSpec($intervalSpec, $invert, $settings);
    return $dateIntervalPlus;
  }
}