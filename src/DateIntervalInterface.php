<?php

namespace Drupal\date_interval;

/**
 * Interface DateIntervalInterface.
 *
 * @package Drupal\date_interval
 */
interface DateIntervalInterface {

  /**
   * Interval spec period designators.
   */
  const PERIOD_PREFIX = 'P';
  const PERIOD_YEARS = 'Y';
  const PERIOD_MONTHS = 'M';
  const PERIOD_DAYS = 'D';
  const PERIOD_TIME_PREFIX = 'T';
  const PERIOD_HOURS = 'H';
  const PERIOD_MINUTES = 'M';
  const PERIOD_SECONDS = 'S';
  const PERIOD_MICRO_SECONDS = 'F';

  /**
   * Invert the interval.
   *
   * @return $this
   */
  public function invert();

  /**
   * Getter for year values.
   *
   * @return int
   *   The number of years.
   */
  public function getYears();

  /**
   * Setter for year values.
   *
   * @param int $year
   *   The new number of years.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setYears(int $year);

  /**
   * Getter for year months.
   *
   * @return int
   *   The number of months.
   */
  public function getMonths();

  /**
   * Setter for month values.
   *
   * @param int $month
   *   The new number of month.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setMonth(int $month);

  /**
   * Getter for days values.
   *
   * @return int
   *   The number of days.
   */
  public function getDays();

  /**
   * Setter for day values.
   *
   * @param int $days
   *   The new number of days.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setDays(int $days);

  /**
   * Getter for hours values.
   *
   * @return int
   *   The number of minutes.
   */
  public function getHours();

  /**
   * Setter for hours values.
   *
   * @param int $hours
   *   The new number of hours.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setHours(int $hours);

  /**
   * Getter for minutes values.
   *
   * @return int
   *   The number of minutes.
   */
  public function getMinutes();

  /**
   * Setter for minutes values.
   *
   * @param int $minutes
   *   The new number of minutes.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setMinutes(int $minutes);

  /**
   * Getter for seconds values.
   *
   * @return int
   *   The number of seconds.
   */
  public function getSeconds();

  /**
   * Setter for seconds values.
   *
   * @param int $seconds
   *   The new number of seconds.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setSeconds(int $seconds);

  /**
   * Getter for micro seconds values.
   *
   * @return float
   *   The number of micro seconds.
   */
  public function getMicroSeconds();

  /**
   * Setter for micro seconds values.
   *
   * @param float $microSeconds
   *   The number of micro seconds.
   */
  public function setMicroSeconds(float $microSeconds);

  /**
   * Getter for interval spec.
   *
   * @return null|string
   *   The spec the interval was created with.
   */
  public function getIntervalSpec();

  /**
   * Gets a clone of the proxied PHP \DateInterval object wrapped by this class.
   *
   * @return \DateInterval
   *   A clone of the PHP \DateInterval object.
   */
  public function getPhpDateInterval();

  /**
   * Check if the period is empty.
   *
   * @return bool
   *   A boolean indicating if the period is empty.
   */
  public function isEmpty();

}
