<?php

namespace Drupal\date_interval;

/**
 * Wraps DateInterval().
 *
 * This class wraps the PHP DateInterval class with more flexible initialization
 * parameters, allowing a date to be created from an existing date interval
 * object or an array of date parts. It also adds a __toString() method
 * to the date interval object.
 *
 * @package Drupal\date_interval
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DateIntervalPlus implements DateIntervalInterface {

  /**
   * The default format.
   *
   * @var string
   */
  const DEFAULT_FORMAT = '%Y years %M months %D days %H hours %I minutes %S seconds';

  /**
   * The default interval array.
   *
   * @var array
   */
  const DEFAULT_INTERVAL_ARRAY = [
    'years' => 0,
    'months' => 0,
    'days' => 0,
    'hours' => 0,
    'minutes' => 0,
    'seconds' => 0,
  ];

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
   * Constructs a date interval plus object.
   *
   * @param string $intervalSpec
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
  public function __construct($intervalSpec, $invert = 0, array $settings = []) {

    // Unpack the settings array.
    $this->langcode = !empty($settings['langcode']) ? $settings['langcode'] : NULL;

    // Store the spec used to create the interval.
    $this->intervalSpec = $intervalSpec;

    try {
      $this->dateIntervalObject = new \DateInterval($intervalSpec);
      if ($invert) {
        $this->invert();
      }
    }
    catch (\Exception $e) {
      throw new \InvalidArgumentException("Invalid interval spec provided");
    }
  }

  /**
   * Invert the interval.
   *
   * @return $this
   */
  public function invert() {
    $this->dateIntervalObject->invert = $this->dateIntervalObject->invert ? 0 : 1;
    return $this;
  }

  /**
   * Getter for year values.
   *v
   * @return int
   *   The number of years.
   */
  public function getYears() {
    return $this->dateIntervalObject->y;
  }

  /**
   * Setter for year values.
   *
   * @param int $year
   *   The new number of years.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setYears(int $year) {
    $this->dateIntervalObject->y = $year;
    return $this;
  }

  /**
   * Getter for year months.
   *
   * @return int
   *   The number of months.
   */
  public function getMonths() {
    return $this->dateIntervalObject->m;
  }

  /**
   * Setter for month values.
   *
   * @param int $month
   *   The new number of month.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setMonth(int $month) {
    $this->dateIntervalObject->m = $month;
    return $this;
  }

  /**
   * Getter for days values.
   *
   * @return int
   *   The number of days.
   */
  public function getDays() {
    return $this->dateIntervalObject->d;
  }

  /**
   * Setter for day values.
   *
   * @param int $days
   *   The new number of days.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setDays(int $days) {
    $this->dateIntervalObject->d = $days;
    return $this;
  }

  /**
   * Getter for hours values.
   *
   * @return int
   *   The number of minutes.
   */
  public function getHours() {
    return $this->dateIntervalObject->h;
  }

  /**
   * Setter for hours values.
   *
   * @param int $hours
   *   The new number of hours.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setHours(int $hours) {
    $this->dateIntervalObject->h = $hours;
    return $this;
  }

  /**
   * Getter for minutes values.
   *
   * @return int
   *   The number of minutes.
   */
  public function getMinutes() {
    return $this->dateIntervalObject->i;
  }

  /**
   * Setter for minutes values.
   *
   * @param int $minutes
   *   The new number of minutes.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setMinutes(int $minutes) {
    $this->dateIntervalObject->i = $minutes;
    return $this;
  }

  /**
   * Getter for seconds values.
   *
   * @return int
   *   The number of seconds.
   */
  public function getSeconds() {
    return $this->dateIntervalObject->s;
  }

  /**
   * Setter for seconds values.
   *
   * @param int $seconds
   *   The new number of seconds.
   *
   * @return $this
   *   The object itself so setters can be chained.
   */
  public function setSeconds(int $seconds) {
    $this->dateIntervalObject->s = $seconds;
    return $this;
  }

  /**
   * Getter for micro seconds values.
   *
   * @return float
   *   The number of micro seconds.
   */
  public function getMicroSeconds() {
    return $this->dateIntervalObject->f;
  }

  /**
   * Setter for micro seconds values.
   *
   * @param float $microSeconds
   *   The number of micro seconds.
   */
  public function setMicroSeconds(float $microSeconds) {
    $this->dateIntervalObject->f = $microSeconds;
  }

  /**
   * Getter for interval spec.
   *
   * @return null|string
   *   The spec the interval was created with.
   */
  public function getIntervalSpec() {
    return $this->intervalSpec;
  }

  /**
   * Gets a clone of the proxied PHP \DateInterval object wrapped by this class.
   *
   * @return \DateInterval
   *   A clone of the PHP \DateInterval object.
   */
  public function getPhpDateInterval() {
    return clone $this->dateIntervalObject;
  }

  /**
   * Check if the period is empty.
   *
   * @return bool
   *   A boolean indicating if the period is empty.
   */
  public function isEmpty() {
    return $this->getYears() === 0 &&
      $this->getMonths() === 0 &&
      $this->getDays() === 0 &&
      $this->getHours() === 0 &&
      $this->getMinutes() === 0 &&
      $this->getSeconds() === 0 &&
      $this->getMicroSeconds() === 0;
  }

  /**
   * Implements the magic __call method.
   *
   * Passes through all unknown calls onto the DateInterval object.
   *
   * @param string $method
   *   The method to call on the decorated object.
   * @param array $args
   *   Call arguments.
   *
   * @return mixed
   *   The return value from the method on the decorated object. If the proxied
   *   method call returns a DateInterval object, then return the original
   *   DateIntervalPlus object, which allows function chaining to work properly.
   *   Otherwise, the value from the proxied method call is returned.
   *
   * @throws \Exception
   *   Thrown when the DateInterval object is not set.
   * @throws \BadMethodCallException
   *   Thrown when there is no corresponding method on the DateInterval
   *   object to call.
   */
  public function __call($method, array $args) {
    if (!isset($this->dateIntervalObject)) {
      throw new \Exception('DateInterval object not set.');
    }
    if (!method_exists($this->dateIntervalObject, $method)) {
      throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $method));
    }

    $result = call_user_func_array([$this->dateIntervalObject, $method], $args);

    return $result === $this->dateIntervalObject ? $this : $result;
  }

  /**
   * Implements the magic __callStatic method.
   *
   * Passes through all unknown static calls onto the DateInterval object.
   */
  public static function __callStatic($method, $args) {
    if (!method_exists('\DateInterval', $method)) {
      throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_called_class(), $method));
    }
    return call_user_func_array(['\DateInterval', $method], $args);
  }

  /**
   * Implements the magic __clone method.
   *
   * Deep-clones the DateInterval object we're wrapping.
   */
  public function __clone() {
    $this->dateIntervalObject = clone($this->dateIntervalObject);
  }

  /**
   * Implements the __toString method.
   */
  public function __toString() {
    return $this->dateIntervalObject->format(static::DEFAULT_FORMAT);
  }

}
