# Change Log
All notable changes to this project will be documented in this file.

## Unreleased

### Added
* A controller can implement the `getTemplateName()` method to modify the default twig template name.

### Changed

### Deprecated

* Deprecated `AbstractModelCollection->set_item()` because of its misleading naming. Use `AbstractModelCollection->add()` instead.
* Deprecated `AbstractModelCollection->remove_item()` because of its inconsistent naming. Use `AbstractModelCollection->remove()` instead.
* Deprecated `BaseHttpAuthentication->check_authentication()`. Use `BaseHttpAuthentication->checkAuthentication()` instead.
* Deprecated `BaseSessionInterface->clear_all()`. Use `BaseSessionInterface->clearAll()` instead.
* Deprecated `Configuration::remove_key()`. Use `Configuration::remove()` instead.
* Deprecated `Configuration::remove_section()`. Use `Configuration::removeSection()` instead.
* Deprecated lower_camel_cased setters and getters in model classes. Use lowerCamelCased method names instead.

### Removed
