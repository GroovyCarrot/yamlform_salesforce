<?php
/**
 * @file
 * YAML form: Salesforce
 * ObjectMappingSubmissionHandler.php
 *
 * Created by Jake Wise 16/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Mapper;

use Drupal\yamlform\YamlFormSubmissionInterface;

/**
 * Interface ObjectMappingSubmissionHandlerInterface
 * @package Drupal\yamlform_salesforce
 */
interface ObjectMappingSubmissionHandlerInterface {

  /**
   * Handle a YAML form submission.
   *
   * @param YamlFormSubmissionInterface $submission
   *   The submission entity.
   * @param string $objectType
   *   The type of object to map.
   * @param array $mapping
   *   The object-form field mapping.
   */
  public function handleSubmission(YamlFormSubmissionInterface $submission, string $objectType, array $mapping);

}
