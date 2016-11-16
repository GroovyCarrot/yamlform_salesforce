<?php
/**
 * @file
 * YAML form: Salesforce
 * SalesforceObjectField.php
 *
 * Created by Jake Wise 15/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Salesforce;

/**
 * Class SalesforceObjectField
 * @package Drupal\yamlform_salesforce
 */
class SalesforceObjectField {

  /** @var string */
  protected $name;
  /** @var string */
  protected $label;

  /**
   * SalesforceObjectField constructor.
   *
   * @param string $name
   * @param string $label
   */
  public function __construct(string $name, string $label) {
    $this->name = $name;
    $this->label = $label;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getLabel(): string {
    return $this->label;
  }

}
