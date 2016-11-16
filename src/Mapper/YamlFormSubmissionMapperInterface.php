<?php
/**
 * @file
 * YAML form: Salesforce
 * YamlFormSubmissionMapperInterface.php
 *
 * Created by Jake Wise 16/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Mapper;

use Drupal\yamlform\YamlFormSubmissionInterface;

/**
 * Interface YamlFormSubmissionMapperInterface
 * @package Drupal\yamlform_salesforce
 */
interface YamlFormSubmissionMapperInterface {

  /**
   * Build the Salesforce object from a submission and mapping array.
   *
   * @param YamlFormSubmissionInterface $submission
   * @param array $mapping
   *   A mapping configuration array, keys for Salesforce fields, and values
   *   for submission element values.
   *
   * @return \stdClass
   */
  public function buildSObject(YamlFormSubmissionInterface $submission, array $mapping): \stdClass;

}
