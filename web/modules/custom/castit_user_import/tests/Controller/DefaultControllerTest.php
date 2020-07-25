<?php

namespace Drupal\castit_user_import\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the castit_user_import module.
 */
class DefaultControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "castit_user_import DefaultController's controller functionality",
      'description' => 'Test Unit for module castit_user_import and controller DefaultController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests castit_user_import functionality.
   */
  public function testDefaultController() {
    // Check that the basic functions of module castit_user_import.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
