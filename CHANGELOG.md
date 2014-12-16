# Change Log
All notable changes to this project will be documented in this file.

## Unreleased

### Added
* A controller can implement the `getTemplateName()` method to modify the default twig template name.

### Changed

### Deprecated

* Deprecated `AbstractController->handle_not_supported_method()`. Use `AbstractController->handleNotSupportedMethod()` instead.
* Deprecated `AbstractDomainRepository->create_identity_model()`. Use `AbstractDomainRepository->createIdentityModel()` instead.
* Deprecated `AbstractDomainRepository->find_all()`. Use `AbstractDomainRepository->findAll()` instead.
* Deprecated `AbstractDomainRepository->find_by_id()`. Use `AbstractDomainRepository->findById()` instead.
* Deprecated `AbstractDomainRepository->query_many()`. Use `AbstractDomainRepository->queryMany()` instead.
* Deprecated `AbstractDomainRepository->query_one()`. Use `AbstractDomainRepository->queryOne()` instead.
* Deprecated `AbstractModelCollection->get_by_id()`. Use `AbstractModelCollection->getById()` instead.
* Deprecated `AbstractModelCollection->remove_item()`. Use `AbstractModelCollection->remove()` instead.
* Deprecated `AbstractModelCollection->set_item()`. Use `AbstractModelCollection->add()` instead.
* Deprecated `BaseHttpAuthentication->check_authentication()`. Use `BaseHttpAuthentication->checkAuthentication()` instead.
* Deprecated `BaseSessionInterface->clear_all()`. Use `BaseSessionInterface->clearAll()` instead.
* Deprecated `Configuration::remove_key()`. Use `Configuration::remove()` instead.
* Deprecated `Configuration::remove_section()`. Use `Configuration::removeSection()` instead.
* Deprecated `Dispatcher::get_request_method()`. Use `Dispatcher::getRequestMethod()` instead.
* Deprecated lower_camel_cased setters and getters in model classes. Use lowerCamelCased method names instead.

### Removed
