<?php
/**
 * @file
 * YAML form: Salesforce
 * SalesforceObjectDescription.php
 *
 * Created by Jake Wise 16/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Salesforce;

/**
 * Class SalesforceObjectDescription
 * @package Drupal\yamlform_salesforce
 */
class SalesforceObjectDescription implements \IteratorAggregate {

  /** @var SalesforceObjectField[] */
  protected $fields = [];

  /**
   * Add a field to the object description.
   *
   * @param SalesforceObjectField $field
   */
  public function addField(SalesforceObjectField $field) {
    $this->fields[$field->getName()] = $field;
  }

  /**
   * @inheritdoc
   */
  public function getIterator() {
    return new \ArrayIterator($this->fields);
  }

}