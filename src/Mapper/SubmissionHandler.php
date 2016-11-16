<?php
/**
 * @file
 * YAML form: Salesforce
 * SubmissionHandler.php
 *
 * Created by Jake Wise 16/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Mapper;

use Drupal\yamlform\YamlFormSubmissionInterface;
use Drupal\yamlform_salesforce\Salesforce\SalesforceObjectCreatorInterface;

/**
 * Class SubmissionHandler
 * @package Drupal\yamlform_salesforce
 */
class SubmissionHandler implements ObjectMappingSubmissionHandlerInterface {

  /** @var YamlFormSubmissionMapperInterface */
  protected $submissionMapper;
  /** @var SalesforceObjectCreatorInterface */
  protected $salesforce;

  /**
   * SubmissionHandler constructor.
   *
   * @param YamlFormSubmissionMapperInterface $mapper
   * @param SalesforceObjectCreatorInterface $salesforce
   */
  public function __construct(
    YamlFormSubmissionMapperInterface $mapper,
    SalesforceObjectCreatorInterface $salesforce
  ) {
    $this->submissionMapper = $mapper;
    $this->salesforce = $salesforce;
  }

  /**
   * @inheritdoc
   */
  public function handleSubmission(YamlFormSubmissionInterface $submission, string $objectType, array $mapping) {
    $object = $this->submissionMapper->buildSObject($submission, $mapping);
    $this->salesforce->createObject($objectType, $object);
  }

}
