<?php

namespace Drupal\date_interval\Tests\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\date_interval\DrupalDateInterval;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * @coversDefaultClass \Drupal\date_interval\DrupalDateInterval
 * @group date_interval
 */
class DrupalDateIntervalTests extends UnitTestCase {

  /**
   * The translation manager used for testing.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $translationManager;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $languageManager;

  /**
   * Setup.
   */
  protected function setUp() {
    parent::setUp();

    $language = $this->createMock(LanguageInterface::class);
    $language->expects($this->any())
      ->method('getId')
      ->willReturn('en');

    $this->translationManager = $this->getStringTranslationStub();
    $this->languageManager = $this->createMock(LanguageManagerInterface::class);

    $this->languageManager->expects($this->any())
      ->method('getCurrentLanguage')
      ->with(LanguageInterface::TYPE_INTERFACE)
      ->willReturn($language);

    $container = new ContainerBuilder();
    $container->set('string_translation', $this->translationManager);
    $container->set('language_manager', $this->languageManager);

    \Drupal::setContainer($container);
  }

  /**
   * Test the create from array function.
   */
  public function testFormatOne() {

    $intervalPlus = DrupalDateInterval::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(
      '1 year 4 days 39 seconds',
      $intervalPlus->format('%y %m %d %s', TRUE, ' ', TRUE, [])
    );
  }

  /**
   * Test the create from array function.
   */
  public function testFormatTwo() {

    $intervalPlus = DrupalDateInterval::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(
      '1-4-39',
      $intervalPlus->format('%y %m %d %s', FALSE, '-', TRUE, ['langcode' => 'en'])
    );
  }

  /**
   * Test the create from array function.
   */
  public function testFormatTree() {

    $intervalPlus = DrupalDateInterval::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(
      '1 year, 0 months, 4 days, 39 seconds',
      $intervalPlus->format('%y %m %d %s', TRUE, ', ', FALSE, ['langcode' => 'en'])
    );
  }

  /**
   * Test the create from array function.
   */
  public function testFormatFour() {

    $intervalPlus = DrupalDateInterval::createFromArray(
      [
        'years' => 1,
        'days' => 4,
        'seconds' => 39,
      ]
    );

    $this->assertSame(
      '1 y, 0 mths, 4 d, 39 s',
      $intervalPlus->format(
        '%y %m %d %s',
        [
          '%y' => [DrupalDateInterval::SINGULAR => 'y', DrupalDateInterval::PLURAL => 'ys'],
          '%m' => [DrupalDateInterval::SINGULAR => 'm', DrupalDateInterval::PLURAL => 'mths'],
          '%d' => [DrupalDateInterval::SINGULAR => 'd', DrupalDateInterval::PLURAL => 'd'],
          '%s' => [DrupalDateInterval::SINGULAR => 's', DrupalDateInterval::PLURAL => 's'],
        ],
        ', ',
        FALSE,
        ['langcode' => 'en']
      )
    );

  }

}
