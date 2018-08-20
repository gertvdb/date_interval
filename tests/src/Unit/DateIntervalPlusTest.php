<?php

namespace Drupal\Tests\date_interval\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\date_interval\DateIntervalPlus;

/**
 * Class DateIntervalPlusTests.
 *
 * @group date_interval
 */
class DateIntervalPlusTest extends UnitTestCase {

  /**
   * Test the create from array function.
   */
  public function testCreateFromArray() {

    $spec = 'P1Y0M4DT0H0M39S';
    $intervalPlus = DateIntervalPlus::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(1, $intervalPlus->getYears());
    $this->assertSame(0, $intervalPlus->getMonths());
    $this->assertSame(4, $intervalPlus->getDays());
    $this->assertSame(0, $intervalPlus->getHours());
    $this->assertSame(0, $intervalPlus->getMinutes());
    $this->assertSame(39, $intervalPlus->getSeconds());

    $this->assertSame($intervalPlus->getIntervalSpec(), $spec);
  }

  /**
   * Test the create from array function.
   */
  public function testCreateFromArrayInvalid() {

    $spec = 'P0Y0M5DT0H0M0S';
    $intervalPlus = DateIntervalPlus::createFromArray(
      [
        'days' => 5,
        'random' => 1,
        'keys' => 4,
      ]
    );

    $this->assertSame(0, $intervalPlus->getYears());
    $this->assertSame(0, $intervalPlus->getMonths());
    $this->assertSame(5, $intervalPlus->getDays());
    $this->assertSame(0, $intervalPlus->getHours());
    $this->assertSame(0, $intervalPlus->getMinutes());
    $this->assertSame(0, $intervalPlus->getSeconds());

    $this->assertSame($intervalPlus->getIntervalSpec(), $spec);
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateInterval() {

    $spec = 'P1Y0M4DT0H0M39S';
    $interval = new \DateInterval($spec);
    $intervalPlus = DateIntervalPlus::createFromDateInterval($interval);

    $this->assertSame(1, $intervalPlus->getYears());
    $this->assertSame(0, $intervalPlus->getMonths());
    $this->assertSame(4, $intervalPlus->getDays());
    $this->assertSame(0, $intervalPlus->getHours());
    $this->assertSame(0, $intervalPlus->getMinutes());
    $this->assertSame(39, $intervalPlus->getSeconds());

    $this->assertSame($spec, $intervalPlus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateString() {

    $spec = 'P1Y0M4DT0H0M39S';
    $intervalPlus = DateIntervalPlus::createFromDateString('1 year 4 days 39 seconds');

    $this->assertSame(1, $intervalPlus->getYears());
    $this->assertSame(0, $intervalPlus->getMonths());
    $this->assertSame(4, $intervalPlus->getDays());
    $this->assertSame(0, $intervalPlus->getHours());
    $this->assertSame(0, $intervalPlus->getMinutes());
    $this->assertSame(39, $intervalPlus->getSeconds());

    $this->assertSame($spec, $intervalPlus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateFromDateStringInvalid() {

    $spec = 'P0Y0M0DT0H0M0S';
    $intervalPlus = DateIntervalPlus::createFromDateString('random string');

    $this->assertSame(TRUE, $intervalPlus->isEmpty());
    $this->assertSame($spec, $intervalPlus->getIntervalSpec());
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testCreateSpec() {

    $spec = 'P1Y0M4DT0H0M39S';
    $createdSpec = DateIntervalPlus::createSpec(1, 0, 4, 0, 0, 39);

    $this->assertSame($spec, $createdSpec);
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testFormat() {
    $spec = 'P1Y0M4DT0H0M39S';
    $interval = new \DateInterval($spec);
    $intervalPlus = DateIntervalPlus::createFromDateInterval($interval);

    $this->assertSame('1 0 4 0 0 39', $intervalPlus->format('%y %m %d %h %i %s'));
    $this->assertSame('1 0 4 0 0 39', $intervalPlus->format('%y %m %d %h %i %s', 'Empty period provided!'));
  }

  /**
   * Test the create from \DateInterval function.
   */
  public function testFormatInvalid() {
    $intervalPlus = DateIntervalPlus::createFromDateString('random string');

    $this->assertSame('0 0 0 0 0 0', $intervalPlus->format('%y %m %d %h %i %s'));
    $this->assertSame('Empty period provided!', $intervalPlus->format('%y %m %d %h %i %s', 'Empty period provided!'));
  }

}
