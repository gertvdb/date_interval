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
   * @param int $years
   *   The number of years.
   * @param int $months
   *   The number of months.
   * @param int $days
   *   The number of days.
   * @param int $hours
   *   The number of hours.
   * @param int $minutes
   *   The number of minutes.
   * @param int $seconds
   *   The number of seconds.
   *
   * @return string
   *   The spec string.
   */
  public static function createSpec(int $years = 0, int $months = 0, int $days = 0, int $hours = 0, int $minutes = 0, int $seconds = 0) {
    $spec = 'P' . $years . 'Y' . $months . 'M' . $days . 'DT' . $hours . 'H' . $minutes . 'M' . $seconds . 'S';
    return $spec;
  }

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param string $interval_spec
   *   A interval spec.
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromIntervalSpec(string $interval_spec, array $settings = []) {
    return new static($interval_spec, $settings);
  }

  /**
   * Create a DateIntervalPlus from php \DateInterval.
   *
   * @param \DateInterval $date_interval
   *   A php DateInterval object.
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromDateInterval(\DateInterval $date_interval, array $settings = []) {
    $spec = self::createSpec(
      $date_interval->y,
      $date_interval->m,
      $date_interval->d,
      $date_interval->h,
      $date_interval->i,
      $date_interval->s
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
   * @param array $interval_array
   *   A keyed array (years, months, days, hours, minutes, seconds).
   * @param array $settings
   *   An array of settings.
   *
   * @return static
   *   A DateIntervalPlus object
   */
  public static function createFromArray(array $interval_array, array $settings = []) {

    // Make sure all values are set and are numeric.
    $values = [
      'years' => isset($interval_array['years']) && is_numeric($interval_array['years']) ? (int) $interval_array['years'] : 0,
      'months' => isset($interval_array['months']) && is_numeric($interval_array['months']) ? (int) $interval_array['months'] : 0,
      'days' => isset($interval_array['days']) && is_numeric($interval_array['days']) ? (int) $interval_array['days'] : 0,
      'hours' => isset($interval_array['hours']) && is_numeric($interval_array['hours']) ? (int) $interval_array['hours'] : 0,
      'minutes' => isset($interval_array['minutes']) && is_numeric($interval_array['minutes']) ? (int) $interval_array['minutes'] : 0,
      'seconds' => isset($interval_array['seconds']) && is_numeric($interval_array['seconds']) ? (int) $interval_array['seconds'] : 0,
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
   * @param string $interval_spec
   *   An interval specification.
   * @param array $settings
   *   (optional) Keyed array of settings. Defaults to empty array.
   *   - langcode: (optional) String two letter language code used to control
   *     the result of the format(). Defaults to NULL.
   */
  public function __construct(string $interval_spec = 'P1D', array $settings = []) {

    // Unpack the settings array.
    $this->langcode = !empty($settings['langcode']) ? $settings['langcode'] : NULL;

    // Store the spec used to create the interval.
    $this->intervalSpec = $this->prepareSpec($interval_spec);

    try {
      $this->dateIntervalObject = new \DateInterval($interval_spec);
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
