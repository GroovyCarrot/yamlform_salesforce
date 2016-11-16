<?php
/**
 * @file
 * YAML form: Salesforce
 * SalesforceObjectDescriberInterface.php
 *
 * Created by Jake Wise 15/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Salesforce;

use Drupal\yamlform_salesforce\Exception\UnknownSalesforceObjectException;

/**
 * Interface SalesforceObjectDescriberInterface
 * @package Drupal\yamlform_salesforce
 */
interface SalesforceObjectDescriberInterface {

  /**
   * Describe a Salesforce object.
   *
   * @param string $objectType
   *   The object to describe.
   *
   * @return SalesforceObjectDescription
   * @throws UnknownSalesforceObjectException
   */
  public function describeSObject(string $objectType): SalesforceObjectDescription;

}
