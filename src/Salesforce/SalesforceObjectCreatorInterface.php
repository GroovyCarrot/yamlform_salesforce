<?php
/**
 * @file
 * YAML form: Salesforce
 * SalesforceObjectCreatorInterface.php
 *
 * Created by Jake Wise 15/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Salesforce;

use Drupal\yamlform_salesforce\Exception\FailedToCreateSalesforceObjectException;

/**
 * Interface SalesforceObjectCreatorInterface
 * @package Drupal\yamlform_salesforce
 */
interface SalesforceObjectCreatorInterface {

  /**
   * Create an object in Salesforce.
   *
   * @param string $object
   *   The object type to create.
   * @param \stdClass $objectData
   *
   * @throws FailedToCreateSalesforceObjectException
   *
   */
  public function createObject(string $object, \stdClass $objectData)/* : void */;

}