<?php

namespace Drupal\date_interval\Factory;

/**
 * Interface DateIntervalFactoryInterface.
 *
 * @package Drupal\date_interval
 */
interface DateIntervalFactoryInterface {

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
  public static function createFromIntervalSpec(string $intervalSpec, $invert = FALSE, array $settings = []);

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param \DateInterval $dateInterval
   *   A php DateInterval object.
   * @param bool $invert
   *   Whether to invert the interval.
   * @param array $settings
   *   An array of settings.
   *
   * @return \Drupal\date_interval\DateIntervalInterface
   *   A DateIntervalPlus object
   */
  public static function createFromDateInterval(\DateInterval $dateInterval, $invert = FALSE, array $settings = []);

  /**
   * Create a DateIntervalPlus from date string.
   *
   * @param string $time
   *   A time string (ex. 2 days)
   * @param array $settings
   *   An array of settings.
   *
   * @return \Drupal\date_interval\DateIntervalInterface
   *   A DateIntervalPlus object
   */
  public static function createFromDateString(string $time, array $settings = []);

  /**
   * Create a DateIntervalPlus from date string.
   *
   * @param \DateTimeInterface $date1
   *   A php datetime object.
   * @param \DateTimeInterface $date2
   *   A php datetime object.
   * @param array $settings
   *   An array of settings.
   *
   * @return \Drupal\date_interval\DateIntervalInterface
   *   A DateIntervalPlus object
   */
  public static function createFromDiff(\DateTimeInterface $date1, \DateTimeInterface $date2, array $settings = []);

  /**
   * Create a DateIntervalPlus from keyed array.
   *
   * @param array $intervalArray
   *   A keyed array (Y, M, D, H, I, S, F).
   * @param bool $invert
   *   Whether to invert the interval.
   * @param array $settings
   *   An array of settings.
   *
   * @return \Drupal\date_interval\DateIntervalInterface
   *   A DateIntervalPlus object
   */
  public static function createFromArray(array $intervalArray = [], $invert = FALSE, array $settings = []);

}
