<?php
/**
 * @file
 * YAML form: Salesforce
 * PhpforceClientAdapter.php
 *
 * Created by Jake Wise 15/11/2016.
 *
 * You are permitted to use, modify, and distribute this file in accordance with
 * the terms of the license agreement accompanying it.
 */

namespace Drupal\yamlform_salesforce\Salesforce;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\yamlform_salesforce\Exception\FailedToCreateSalesforceObjectException;
use Drupal\yamlform_salesforce\Exception\UnknownSalesforceObjectException;
use Phpforce\SoapClient\ClientInterface;
use Phpforce\SoapClient\Result\SaveResult;

/**
 * Class PhpforceClientAdapter
 *
 * This class is an adapter for the Salesforce/Phpforce library's Client class
 * to work with this module. The requirement for this that the application
 * already has Phpforce/Client connection to Salesforce configured in the
 * dependency injection container, and can be injected into this adapter. This
 * adapter can then be used to describe and create objects within Salesforce.
 *
 * @package Drupal\yamlform_salesforce
 */
class PhpforceClientAdapter implements
  SalesforceObjectDescriberInterface,
  SalesforceObjectCreatorInterface {

  const CACHE_OBJECT_DESCRIPTIONS = 'yamlform_salesforce.object_descriptions';

  /** @var ClientInterface Phpforce client service */
  protected $client;

  /** @var CacheBackendInterface Cache service */
  protected $cacheBackend;

  /**
   * Internal storage for object type descriptions.
   *
   * @var array<string, SalesforceObjectField[]>
   */
  protected $objectTypeDescriptions;

  /**
   * ClientAdapter constructor.
   *
   * @param ClientInterface $client
   * @param CacheBackendInterface $cacheBackend
   */
  public function __construct(ClientInterface $client, CacheBackendInterface $cacheBackend) {
    $this->client = $client;
    $this->cacheBackend = $cacheBackend;

    $this->loadObjectTypeDescriptions();
  }

  /**
   * @inheritdoc
   */
  public function describeSObject(string $objectType): SalesforceObjectDescription {
    if (isset($this->objectTypeDescriptions[$objectType])) {
      return $this->objectTypeDescriptions[$objectType];
    }

    try {
      $results = $this->client->describeSObjects([$objectType]);
    }
    catch (\Exception $e) {
      throw new UnknownSalesforceObjectException($e->getMessage());
    }

    $result = $results[0];

    $description = new SalesforceObjectDescription();
    foreach ($result->getFields() as $field) {
      $description->addField(new SalesforceObjectField($field->getName(), $field->getLabel()));
    }

    $this->objectTypeDescriptions[$objectType] = $description;
    $this->cacheObjectTypeDescriptions();

    return $description;
  }

  /**
   * @inheritdoc
   */
  public function createObject(string $object, \stdClass $objectData)/* : void */ {
    /** @var SaveResult[] $results */
    if (isset($objectData->Id)) {
      $results = $this->client->update([$objectData], $object);
    }
    else {
      $results = $this->client->create([$objectData], $object);
    }

    $result = $results[0];

    if (!$result->isSuccess()) {
      $errors = $result->getErrors();
      throw new FailedToCreateSalesforceObjectException($errors[0]->getMessage());
    }
  }

  /**
   * Load the object type descriptions from the cache.
   */
  protected function loadObjectTypeDescriptions() {
    $cache = $this->cacheBackend->get(self::CACHE_OBJECT_DESCRIPTIONS);
    $this->objectTypeDescriptions = $cache ? $cache->data : [];
  }

  /**
   * Persist the object type descriptions to the cache.
   */
  protected function cacheObjectTypeDescriptions() {
    $this->cacheBackend->set(self::CACHE_OBJECT_DESCRIPTIONS, $this->objectTypeDescriptions);
  }

}
