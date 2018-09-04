<?php

namespace Drupal\date_interval\Factory;

use Drupal\date_interval\DateIntervalPlus;
use Drupal\date_interval\DateIntervalInterface;

/**
 * Class NodePreprocessEventFactory.
 */
class DateIntervalPlusFactory implements DateIntervalFactoryInterface {

  /**
   * The spec used to create the interval.
   *
   * @var string
   */
  protected $intervalSpec = NULL;

  /**
   * The value of the language code passed to the constructor.
   *
   * @var string
   */
  protected $langcode = NULL;

  /**
   * The DateInterval object.
   *
   * @var \DateInterval
   */
  protected $dateIntervalObject = NULL;

  /**
   * Get the default interval array for creation from array.
   *
   * @return array
   *   A default array to create an interval.
   */
  private static function getDefaultIntervalArray() {
    return [
      DateIntervalInterface::PERIOD_YEARS => 0,
      DateIntervalInterface::PERIOD_MONTHS => 0,
      DateIntervalInterface::PERIOD_DAYS => 0,
      DateIntervalInterface::PERIOD_HOURS => 0,
      DateIntervalInterface::PERIOD_MINUTES => 0,
      DateIntervalInterface::PERIOD_SECONDS => 0,
      DateIntervalInterface::PERIOD_MICRO_SECONDS => 0,
    ];
  }

  /**
   * Create a spec string.
   *
   * @param int|null $years
   *   The number of years.
   * @param int|null $months
   *   The number of months.
   * @param int|null $days
   *   The number of days.
   * @param int|null $hours
   *   The number of hours.
   * @param int|null $minutes
   *   The number of minutes.
   * @param int|null $seconds
   *   The number of seconds.
   *
   * @return string
   *   The spec string.
   */
  private static function createSpec($years = NULL, $months = NULL, $days = NULL, $hours = NULL, $minutes = NULL, $seconds = NULL) {
    $years = $years ?: 0;
    $months = $months ?: 0;
    $days = $days ?: 0;
    $hours = $hours ?: 0;
    $minutes = $minutes ?: 0;
    $seconds = $seconds ?: 0;

    $spec = DateIntervalInterface::PERIOD_PREFIX . $years . DateIntervalInterface::PERIOD_YEARS . $months . DateIntervalInterface::PERIOD_MONTHS . $days . DateIntervalInterface::PERIOD_DAYS . DateIntervalInterface::PERIOD_TIME_PREFIX . $hours . DateIntervalInterface::PERIOD_HOURS . $minutes . DateIntervalInterface::PERIOD_MINUTES . $seconds . DateIntervalInterface::PERIOD_SECONDS;
    return $spec;
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
    $dateIntervalPlus = new DateIntervalPlus($intervalSpec, $settings);
    if ($invert) {
      $dateIntervalPlus->invert();
    }

    return $dateIntervalPlus;
  }

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
  public static function createFromDateInterval(\DateInterval $dateInterval, $invert = FALSE, array $settings = []) {
    $spec = self::createSpec(
      $dateInterval->y,
      $dateInterval->m,
      $dateInterval->d,
      $dateInterval->h,
      $dateInterval->i,
      $dateInterval->s
    );

    $dateIntervalPlus = new DateIntervalPlus($spec, $settings);
    if ($dateInterval->f) {
      $dateIntervalPlus->setMicroSeconds($dateInterval->f);
    }

    if ($invert) {
      $dateIntervalPlus->invert();
    }

    return $dateIntervalPlus;
  }

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
  public static function createFromDateString(string $time, array $settings = []) {
    $interval = \DateInterval::createFromDateString($time);
    return self::createFromDateInterval($interval, $settings);
  }

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
  public static function createFromDiff(\DateTimeInterface $date1, \DateTimeInterface $date2, array $settings = []) {
    $firstDate = ($date1 <= $date2) ? $date1 : $date2;
    $secondDate = ($date1 <= $date2) ? $date2 : $date1;

    $phpDateInterval = $firstDate->diff($secondDate);
    if (!$phpDateInterval) {
      return self::createFromIntervalSpec(self::createSpec());
    }

    $dateIntervalPlus = self::createFromDateInterval($phpDateInterval, $settings);
    if ($secondDate === $date2) {
      $dateIntervalPlus->invert();
    }

    return $dateIntervalPlus;
  }

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
  public static function createFromArray(array $intervalArray = [], $invert = FALSE, array $settings = []) {

    $preparedArray = array_filter($intervalArray, function ($value, $key) {
      return in_array($key, array_keys(self::getDefaultIntervalArray())) && is_numeric($value);
    }, ARRAY_FILTER_USE_BOTH);

    $values = array_merge(self::getDefaultIntervalArray(), $preparedArray);

    $spec = self::createSpec(
      $values[DateIntervalInterface::PERIOD_YEARS],
      $values[DateIntervalInterface::PERIOD_MONTHS],
      $values[DateIntervalInterface::PERIOD_DAYS],
      $values[DateIntervalInterface::PERIOD_HOURS],
      $values[DateIntervalInterface::PERIOD_MINUTES],
      $values[DateIntervalInterface::PERIOD_SECONDS]
    );

    $dateIntervalPlus = new DateIntervalPlus($spec, $settings);
    if (isset($intervalArray[$values[DateIntervalInterface::PERIOD_MICRO_SECONDS]]) && is_numeric($intervalArray[$values[DateIntervalInterface::PERIOD_MICRO_SECONDS]])) {
      $dateIntervalPlus->setMicroSeconds($intervalArray[$values[DateIntervalInterface::PERIOD_MICRO_SECONDS]]);
    }

    if ($invert) {
      $dateIntervalPlus->invert();
    }

    return $dateIntervalPlus;
  }

}
