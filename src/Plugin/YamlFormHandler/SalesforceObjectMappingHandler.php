<?php

namespace Drupal\yamlform_salesforce\Plugin\YamlFormHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\yamlform\YamlFormHandlerBase;
use Drupal\yamlform\YamlFormSubmissionInterface;
use Drupal\yamlform_salesforce\Exception\FailedToCreateSalesforceObjectException;
use Drupal\yamlform_salesforce\Exception\UnknownSalesforceObjectException;
use Drupal\yamlform_salesforce\Mapper\ObjectMappingSubmissionHandlerInterface;
use Drupal\yamlform_salesforce\Salesforce\SalesforceObjectDescriberInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form submission remote post handler.
 *
 * @YamlFormHandler(
 *   id = "salesforce_object_provider",
 *   label = @Translation("Salesforce object provider"),
 *   category = @Translation("External"),
 *   description = @Translation("Pushes an object from form data to Salesforce."),
 *   cardinality = \Drupal\yamlform\YamlFormHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\yamlform\YamlFormHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class SalesforceObjectMappingHandler extends YamlFormHandlerBase {

  /**
   * @var SalesforceObjectDescriberInterface
   */
  protected $objectDescriber;

  /**
   * @var ObjectMappingSubmissionHandlerInterface
   */
  protected $submissionHandler;

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('yamlform'),
      $container->get('yamlform_salesforce.client.describer'),
      $container->get('yamlform_salesforce.mapper.submission_handler')
    );
  }

  /**
   * @inheritdoc
   *
   * @param SalesforceObjectDescriberInterface $objectDescriber
   * @param ObjectMappingSubmissionHandlerInterface $submissionHandler
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LoggerInterface $logger,
    SalesforceObjectDescriberInterface $objectDescriber,
    ObjectMappingSubmissionHandlerInterface $submissionHandler
  ) {
    $this->objectDescriber = $objectDescriber;
    $this->submissionHandler = $submissionHandler;

    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
  }

  /**
   * @inheritdoc
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    parent::buildConfigurationForm($form, $form_state);

    $objectType = isset($this->configuration['object']) ? $this->configuration['object'] : NULL;

    $form['object'] = [
      '#type' => 'textfield',
      '#title' => 'Salesforce object',
      '#required' => TRUE,
      '#default_value' => $objectType,
      // Hide the object type from being changed if it is already set.
      '#access' => empty($objectType),
    ];

    if ($objectType) {
      $form['mapping'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('@object mapping', ['@object' => $objectType]),
      ];

      $this->buildMappingForm($form['mapping'], $objectType);
    }

    return $form;
  }

  /**
   * Build the mapping form element for a Salesforce object.
   *
   * @todo clean this up better, ajax can be used to dynamically add mappings
   * in a UI, rather than just displaying every field with a text field input.
   *
   * @param array $element
   *   The form element to display the mapping UI in.
   * @param string $objectType
   *   The type of object being mapped.
   */
  protected function buildMappingForm(array &$element, string $objectType) {
    $objectDescription = $this->objectDescriber->describeSObject($objectType);

    $element['token_tree_link'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['yamlform', 'yamlform-submission'],
      '#click_insert' => FALSE,
      '#dialog' => TRUE,
    ];

    foreach ($objectDescription as $field) {
      $mapping = isset($this->configuration['mapping'][$field->getName()]) ? $this->configuration['mapping'][$field->getName()] : NULL;

      $element[$field->getName()] = [
        '#type' => 'textfield',
        '#title' => $field->getLabel(),
        '#maxsize' => 1024,
        '#attributes' => ['width' => '100%'],
        '#description' => $field->getName(),
        '#default_value' => $mapping,
      ];
    }
  }

  /**
   * @inheritdoc
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $formState) {
    if (isset($this->configuration['object'])) {
      // Ignore if we have already configured this object mapping.
      return;
    }

    $objectType = $formState->getValue('object');

    try {
      $this->objectDescriber->describeSObject($objectType);
    }
    catch (UnknownSalesforceObjectException $e) {
      $formState->setError($form['settings']['object'], $e->getMessage());
    }
  }

  /**
   * @inheritdoc
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $formState) {
    $this->configuration['object'] = $formState->getValue('object');

    $mapping = $formState->getValue('mapping', []);
    $mapping = array_filter($mapping);
    $this->configuration['mapping'] = $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, YamlFormSubmissionInterface $submission) {
    $yamlform = $submission->getYamlForm();

    // Get elements values from form submission.
    $values = array_intersect_key(
      $form_state->getValues(),
      $yamlform->getElementsFlattenedAndHasValue()
    );

    // Create the submission object, so that we can substitute tokens and send
    // object data over the API.
    $completeSubmission = clone $submission;
    $completeSubmission->setData($values + $submission->getData());

    try {
      $this->submissionHandler->handleSubmission(
        $completeSubmission,
        $this->configuration['object'],
        $this->configuration['mapping']
      );
    }
    catch (FailedToCreateSalesforceObjectException $e) {
      $form_state->setError($form, $e->getMessage());
    }
  }

}
