<?php

namespace Drupal\date_interval;

/**
 * Wraps DateInterval().
 *
 * This class wraps the PHP DateInterval class with more flexible initialization
 * parameters, allowing a date to be created from an existing date interval
 * object or an array of date parts. It also adds a __toString() method
 * to the date interval object.
 */
class DateIntervalPlus {

  const FORMAT = '%Y years %M months %D days %H hours %I minutes %S seconds';

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
  public static function createSpec($years = NULL, $months = NULL, $days = NULL, $hours = NULL, $minutes = NULL, $seconds = NULL) {
    $years = $years ?: 0;
    $months = $months ?: 0;
    $days = $days ?: 0;
    $hours = $hours ?: 0;
    $minutes = $minutes ?: 0;
    $seconds = $seconds ?: 0;

    $spec = 'P' . $years . 'Y' . $months . 'M' . $days . 'DT' . $hours . 'H' . $minutes . 'M' . $seconds . 'S';
    return $spec;
  }

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param string $intervalSpec
   *   A interval spec.
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromIntervalSpec(string $intervalSpec, array $settings = []) {
    return new static($intervalSpec, $settings);
  }

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param \DateInterval $dateInterval
   *   A php DateInterval object.
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromDateInterval(\DateInterval $dateInterval, array $settings = []) {
    $spec = self::createSpec(
      $dateInterval->y,
      $dateInterval->m,
      $dateInterval->d,
      $dateInterval->h,
      $dateInterval->i,
      $dateInterval->s
    );
    return new static($spec, $settings);
  }

  /**
   * Create a DateIntervalPlus from date string.
   *
   * @param string $time
   *   A time string (ex. 2 days)
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromDateString(string $time, array $settings = []) {
    $interval = \DateInterval::createFromDateString($time);
    return self::createFromDateInterval($interval, $settings);
  }

  /**
   * Create a DateIntervalPlus from keyed array.
   *
   * @param array $intervalArray
   *   A keyed array (years, months, days, hours, minutes, seconds).
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromArray(array $intervalArray, array $settings = []) {

    // Make sure all values are set and are numeric.
    $values = [
      'years' => isset($intervalArray['years']) && is_numeric($intervalArray['years']) ? (int) $intervalArray['years'] : 0,
      'months' => isset($intervalArray['months']) && is_numeric($intervalArray['months']) ? (int) $intervalArray['months'] : 0,
      'days' => isset($intervalArray['days']) && is_numeric($intervalArray['days']) ? (int) $intervalArray['days'] : 0,
      'hours' => isset($intervalArray['hours']) && is_numeric($intervalArray['hours']) ? (int) $intervalArray['hours'] : 0,
      'minutes' => isset($intervalArray['minutes']) && is_numeric($intervalArray['minutes']) ? (int) $intervalArray['minutes'] : 0,
      'seconds' => isset($intervalArray['seconds']) && is_numeric($intervalArray['seconds']) ? (int) $intervalArray['seconds'] : 0,
    ];

    $spec = self::createSpec(
      $values['years'],
      $values['months'],
      $values['days'],
      $values['hours'],
      $values['minutes'],
      $values['seconds']
    );

    return new static($spec, $settings);
  }

  /**
   * Constructs a date interval plus object.
   *
   * @param string $intervalSpec
   *   An interval specification.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct(string $intervalSpec = 'P1D', array $settings = []) {

    // Unpack the settings array.
    $this->langcode = !empty($settings['langcode']) ? $settings['langcode'] : NULL;

    // Store the spec used to create the interval.
    $this->intervalSpec = $this->prepareSpec($intervalSpec);

    try {
      $this->dateIntervalObject = new \DateInterval($intervalSpec);
    }
    catch (\Exception $e) {
      throw new \InvalidArgumentException("Invalid interval spec provided");
    }
  }

  /**
   * Getter for year values.
   *
   * @return int
   *   The number of years.
   */
  public function getYears() {
    return $this->dateIntervalObject->y;
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
   * Getter for days values.
   *
   * @return int
   *   The number of days.
   */
  public function getDays() {
    return $this->dateIntervalObject->d;
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
   * Getter for minutes values.
   *
   * @return int
   *   The number of minutes.
   */
  public function getMinutes() {
    return $this->dateIntervalObject->i;
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
   * @param bool $clone
   *   FALSE to return the original proxied \DateInterval object. By default a
   *   clone will be returned, to avoid proxy pattern breaks.
   *
   * @return \DateInterval
   *   A clone of the PHP \DateInterval object, or the original instance
   *   if $clone is FALSE.
   */
  public function getPhpDateInterval($clone = TRUE) {
    return $clone ? clone $this->dateIntervalObject : $this->dateIntervalObject;
  }

  /**
   * Renders the interval.
   *
   * @return string
   *   The rendered interval in standard format.
   */
  public function render() {
    return $this->format(static::FORMAT);
  }

  /**
   * Format the period.
   *
   * @param string $format
   *   The format string.
   * @param string $empty
   *   The message to show when period is empty (optional).
   *   When message is not passed an empty period will be formatted.
   *   (ex : 0 year 0 days)
   *
   * @return string
   *   The formatted interval.
   */
  public function format($format, $empty = FALSE) {
    if ($empty && $this->isEmpty()) {
      return $empty;
    }

    return $this->dateIntervalObject->format($format);
  }

  /**
   * Check if the period is empty.
   *
   * @return bool
   *   A boolean indicating if the period is empty.
   */
  public function isEmpty() {
    return $this->getIntervalSpec() === 'P0Y0M0DT0H0M0S' ? TRUE : FALSE;
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
    return $this->format(static::FORMAT);
  }

  /**
   * Prepares the spec value.
   *
   * Changes the spec value before trying to use it, if necessary.
   * Can be overridden to handle special cases.
   *
   * @param mixed $spec
   *   An interval spec.
   *
   * @return mixed
   *   The massaged spec.
   */
  protected function prepareSpec($spec) {
    return $spec;
  }

}
