<?php

namespace Drupal\date_interval\Tests\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\date_interval\DateIntervalPlus;

/**
 * @coversDefaultClass \Drupal\date_interval\DateIntervalPlus
 * @group date_interval
 */
class DateIntervalPlusTests extends UnitTestCase {

  /**
   * Test the create from array function.
   */
  public function testCreateFromArray() {

    $spec = 'P1Y0M4DT0H0M39S';
    $interval_plus = DateIntervalPlus::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(1, $interval_plus->getYears());
    $this->assertSame(0, $interval_plus->getMonths());
    $this->assertSame(4, $interval_plus->getDays());
    $this->assertSame(0, $interval_plus->getHours());
    $this->assertSame(0, $interval_plus->getMinutes());
    $this->assertSame(39, $interval_plus->getSeconds());

    $this->assertSame($interval_plus->getIntervalSpec(), $spec);
  }

  /**
   * Test the create from array function.
   */
  public function testCreateFromArrayInvalid() {

    $spec = 'P0Y0M5DT0H0M0S';
    $interval_plus = DateIntervalPlus::createFromArray(
      [
        'days' => 5,
        'random' => 1,
        'keys' => 4,
      ]
    );

    $this->assertSame(0, $interval_plus->getYears());
    $this->assertSame(0, $interval_plus->getMonths());
    $this->assertSame(5, $interval_plus->getDays());
    $this->assertSame(0, $interval_plus->getHours());
    $this->assertSame(0, $interval_plus->getMinutes());
    $this->assertSame(0, $interval_plus->getSeconds());

    $this->assertSame($interval_plus->getIntervalSpec(), $spec);
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateInterval() {

    $spec = 'P1Y0M4DT0H0M39S';
    $interval = new \DateInterval($spec);
    $interval_plus = DateIntervalPlus::createFromDateInterval($interval);

    $this->assertSame(1, $interval_plus->getYears());
    $this->assertSame(0, $interval_plus->getMonths());
    $this->assertSame(4, $interval_plus->getDays());
    $this->assertSame(0, $interval_plus->getHours());
    $this->assertSame(0, $interval_plus->getMinutes());
    $this->assertSame(39, $interval_plus->getSeconds());

    $this->assertSame($spec, $interval_plus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateString() {

    $spec = 'P1Y0M4DT0H0M39S';
    $interval_plus = DateIntervalPlus::createFromDateString('1 year 4 days 39 seconds');

    $this->assertSame(1, $interval_plus->getYears());
    $this->assertSame(0, $interval_plus->getMonths());
    $this->assertSame(4, $interval_plus->getDays());
    $this->assertSame(0, $interval_plus->getHours());
    $this->assertSame(0, $interval_plus->getMinutes());
    $this->assertSame(39, $interval_plus->getSeconds());

    $this->assertSame($spec, $interval_plus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateStringInvalid() {

    $spec = 'P0Y0M0DT0H0M0S';
    $interval_plus = DateIntervalPlus::createFromDateString('random string');

    $this->assertSame(TRUE, $interval_plus->isEmpty());
    $this->assertSame($spec, $interval_plus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateSpec() {

    $spec = 'P1Y0M4DT0H0M39S';
    $created_spec = DateIntervalPlus::createSpec(1, 0, 4, 0, 0, 39);

    $this->assertSame($spec, $created_spec);
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testFormat() {
    $spec = 'P1Y0M4DT0H0M39S';
    $interval = new \DateInterval($spec);
    $interval_plus = DateIntervalPlus::createFromDateInterval($interval);

    $this->assertSame('1 0 4 0 0 39', $interval_plus->format('%y %m %d %h %i %s'));
    $this->assertSame('1 0 4 0 0 39', $interval_plus->format('%y %m %d %h %i %s', 'Empty period provided!'));
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testFormatInvalid() {
    $interval_plus = DateIntervalPlus::createFromDateString('random string');

    $this->assertSame('0 0 0 0 0 0', $interval_plus->format('%y %m %d %h %i %s'));
    $this->assertSame('Empty period provided!', $interval_plus->format('%y %m %d %h %i %s', 'Empty period provided!'));
  }

}
