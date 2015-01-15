# Change Log
All notable changes to this project will be documented in this file.

## Unreleased

### Added

### Changed

* Twig version is raised to 1.17

### Deprecated

### Removed

* Removed [numerous methods](https://github.com/app-zap/PHPFramework/commit/77137cf25a12e8a7879bac24457874ba20639248) that were deprecated and marked for removal in 1.5.

## Version 1.4, 2015-01-15 21:13:59

### Added

* [MailService](https://github.com/app-zap/PHPFramework/blob/develop/classes/Mail/MailService.php) and [MailMessage](https://github.com/app-zap/PHPFramework/blob/develop/classes/Mail/MailMessage.php)
* A controller can implement the `getTemplateName()` method to modify the default twig template name.
* `Configuration::getSection()` now has an optional 3rd parameter (array) `$defaultValues`
* Simplified routing expressions in `routes.php`

### Deprecated

* Deprecated `AbstractController->handle_not_supported_method()`. Use `AbstractController->handleNotSupportedMethod()` instead.
* Deprecated `AbstractController->require_http_authentication`. Use `AbstractController->requireHttpAuthentication` instead.
* Deprecated `AbstractDomainRepository->create_identity_model()`. Use `AbstractDomainRepository->createIdentityModel()` instead.
* Deprecated `AbstractDomainRepository->find_all()`. Use `AbstractDomainRepository->findAll()` instead.
* Deprecated `AbstractDomainRepository->find_by_id()`. Use `AbstractDomainRepository->findById()` instead.
* Deprecated `AbstractDomainRepository->query_many()`. Use `AbstractDomainRepository->queryMany()` instead.
* Deprecated `AbstractDomainRepository->query_one()`. Use `AbstractDomainRepository->queryOne()` instead.
* Deprecated `AbstractModelCollection->get_by_id()`. Use `AbstractModelCollection->getById()` instead.
* Deprecated `AbstractModelCollection->remove_item()`. Use `AbstractModelCollection->remove()` instead.
* Deprecated `AbstractModelCollection->set_item()`. Use `AbstractModelCollection->add()` instead.
* Deprecated `AbstractView->json_output()`. Use `AbstractView->jsonOutput()` instead.
* Deprecated `AbstractView->set_template_name()`. Use `AbstractView->setTemplateName()` instead.
* Deprecated `BaseHttpAuthentication->check_authentication()`. Use `BaseHttpAuthentication->checkAuthentication()` instead.
* Deprecated `BaseSessionInterface->clear_all()`. Use `BaseSessionInterface->clearAll()` instead.
* Deprecated `Configuration::remove_key()`. Use `Configuration::remove()` instead.
* Deprecated `Configuration::remove_section()`. Use `Configuration::removeSection()` instead.
* Deprecated `DatabaseConnection->is_connected()`. Use `DatabaseConnection->isConnected()` instead.
* Deprecated `DatabaseConnection->last_id()`. Use `DatabaseConnection->lastId()` instead.
* Deprecated `Dispatcher->get_request_method()`. Use `Dispatcher->getRequestMethod()` instead.
* Deprecated `Dispatcher->get_request_method()`. Use `Dispatcher->getRequestMethod()` instead.
* Deprecated `EntityMapper->object_to_record()`. Use `EntityMapper->objectToRecord()` instead.
* Deprecated `EntityMapper->record_to_object()`. Use `EntityMapper->recordToObject()` instead.
* Deprecated `EntityMapper->scalarize_value()`. Use `EntityMapper->scalarizeValue()` instead.
* Deprecated `HttpStatus::get_status()` Use `HttpStatus::getStatus()` instead.
* Deprecated `HttpStatus::send_headers()` Use `HttpStatus::sendHeaders()` instead.
* Deprecated `HttpStatus::set_status()` Use `HttpStatus::setStatus()` instead.
* Deprecated `Nomenclature::collectionclassname_to_repositoryclassname()` Use `Nomenclature::collectionclassnameToRepositoryclassname()` instead.
* Deprecated `Nomenclature::fieldname_to_getter()` Use `Nomenclature::fieldnameToGetter()` instead.
* Deprecated `Nomenclature::fieldname_to_setter()` Use `Nomenclature::fieldnameToSetter()` instead.
* Deprecated `Nomenclature::getter_to_fieldname()` Use `Nomenclature::getterToFieldname()` instead.
* Deprecated `Nomenclature::modelclassname_to_collectionclassname()` Use `Nomenclature::modelClassnameToCollectionClassname()` instead.
* Deprecated `Nomenclature::modelclassname_to_repositoryclassname()` Use `Nomenclature::modelClassnameToRepositoryClassname()` instead.
* Deprecated `Nomenclature::repositoryclassname_to_collectionclassname()` Use `Nomenclature::repositoryClassnameToCollectionClassname()` instead.
* Deprecated `Nomenclature::repositoryclassname_to_modelclassname()` Use `Nomenclature::repositoryClassnameToModelClassname()` instead.
* Deprecated `Nomenclature::repositoryclassname_to_tablename()` Use `Nomenclature::repositoryClassnameToTablename()` instead.
* Deprecated `Router::get_parameters()` Use `Router::getParameters()` instead.
* Deprecated `Router::get_responder()` Use `Router::getResponder()` instead.
* Deprecated `Singleton::get_instance()` Use `Singleton::getInstance()` instead.
* Deprecated `TwigView->add_output_filter()` Use `TwigView->addOutputFilter()` instead.
* Deprecated `TwigView->add_output_function()` Use `TwigView->addOutputFunction()` instead.
* Deprecated `TwigView->has_output_filter()` Use `TwigView->hasOutputFilter()` instead.
* Deprecated `TwigView->has_output_function()` Use `TwigView->hasOutputFunction()` instead.
* Deprecated lower_camel_cased setters and getters in model classes. Use lowerCamelCased method names instead.
