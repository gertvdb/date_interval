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
 * @method static createFromDateString(string $time)
 */
class DateIntervalPlus extends \DateInterval {

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
    $this->intervalSpec = $interval_spec;

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
    $spec = 'P' . $date_interval->y . 'Y' . $date_interval->m . 'M' . $date_interval->d . 'DT' . $date_interval->h . 'H' . $date_interval->i . 'M' . $date_interval->s . 'S';
    return new static($spec, $settings);
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


  public function format($format) {
    return $this->dateIntervalObject->format($format);
  }


}
