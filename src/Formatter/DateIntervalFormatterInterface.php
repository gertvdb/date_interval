<?php

namespace Drupal\date_interval\Formatter;

use Drupal\date_interval\DateIntervalInterface;

/**
 * Interface DateIntervalFormatterInterface.
 */
interface DateIntervalFormatterInterface {

  /**
   * Format the interval.
   *
   * @param \Drupal\date_interval\DateIntervalInterface $dateInterval
   *   A date interval plus object.
   * @param string $format
   *   The format consisting of only valid literals.
   *   Other items will be removed.
   */
  public function format(DateIntervalInterface $dateInterval, $format);

}
