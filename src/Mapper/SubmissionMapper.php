<?php
/**
 * @file
 * YAML form: Salesforce
 * SubmissionMapper.php
 *
 * Created by Jake Wise 16/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Mapper;

use Drupal\Core\Utility\Token;
use Drupal\yamlform\YamlFormSubmissionInterface;

/**
 * Class SubmissionMapper
 * @package Drupal\yamlform_salesforce
 */
class SubmissionMapper implements YamlFormSubmissionMapperInterface {

  /** @var Token */
  protected $token;

  /**
   * SubmissionMapper constructor.
   *
   * @param Token $token
   */
  public function __construct(Token $token) {
    $this->token = $token;
  }

  /**
   * @inheritdoc
   */
  public function buildSObject(YamlFormSubmissionInterface $submission, array $mapping): \stdClass {
    $token_data = [
      'yamlform' => $submission->getYamlForm(),
      'yamlform-submission' => $submission,
    ];
    $token_options = ['clear' => TRUE];

    $object = new \stdClass();
    foreach ($mapping as $sForceField => $value) {
      $object->{$sForceField} = $this->token->replace($value, $token_data, $token_options);
    }
    return $object;
  }

}
