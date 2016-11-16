# YAML form: Salesforce

## Handlers

### Salesforce object provider
This provider allows you to use tokens to map form element values onto a
Salesforce object, to be pushed over the API on submission. This handler
will use the API to fetch a description of the requested object, and
provide a mapping UI for the object's fields.

## Integrating the Salesforce API

I don't see it being the responsibility of this module to set up the
connection to Salesforce; only to provide the handler to YAML form, and
UX to map the form element values to a Salesforce object. At the moment
there isn't a Drupal 8-ready Salesforce connection management module, so
this module requires some manual configuration to wire up. 

In your container YAML's for your project (settings.php), you can use 
the `Drupal\yamlform_salesforce\Salesforce\PhpforceClientAdapter` to
plug in the Phpforce Salesforce SOAP client library.

Require the library:
```
composer require phpforce/soap-client ^0.1.0
```

Project services.yml:
```yaml
parameters:
  project.salesforce.wsdl: '../config/salesforce/wsdl.xml'
  project.salesforce.username: 'username'
  project.salesforce.password: 'password'
  project.salesforce.token: 'security token'

services:
  # Salesforce client services
  project.salesforce.phpforce.client.builder:
    class: Phpforce\SoapClient\ClientBuilder
    arguments:
      - '%project.salesforce.wsdl%'
      - '%project.salesforce.username%'
      - '%project.salesforce.password%'
      - '%project.salesforce.token%'

  project.salesforce.phpforce.client:
    class: Phpforce\SoapClient\Client
    factory: ['@project.salesforce.phpforce.client.builder', 'build']

  project.yamlform_salesforce.client:
    class: Drupal\yamlform_salesforce\Salesforce\PhpforceClientAdapter
    arguments:
      - '@project.salesforce.phpforce.client'
      - '@cache.default'

  # Replace yamlform_salesforce service definitions for our Salesforce client.
  # This doesn't actually work unless you apply the patch here:
  # https://www.drupal.org/node/2828099
  # Otherwise, you'll have to use a module to write a service modifier
  # and add the aliases in manually.
  yamlform_salesforce.client.describer: '@project.yamlform_salesforce.client'
  yamlform_salesforce.client.creator: '@project.yamlform_salesforce.client'
```
