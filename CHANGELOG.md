# Change Log
All notable changes to this project will be documented in this file.

## Unreleased

### Added
* [[ddcc9f01](https://github.com/app-zap/PHPFramework/commit/ddcc9f01d7e1e571d8fca5711620ed8bd6dd8739)] A controller can implement the `getTemplateName()` method to modify the default twig template name.

### Changed

### Deprecated

* [[e570b5ad](https://github.com/app-zap/PHPFramework/commit/e570b5addcbab94a38a56fb2eb4da1108d05854b)] Deprecated `AbstractModelCollection->set_item()` because of its misleading naming. Use `AbstractModelCollection->add()` instead.
* [[c6aaed0e](https://github.com/app-zap/PHPFramework/commit/c6aaed0e9ae84bb7d7502fece39ed92449c5ad63)] Deprecated `AbstractModelCollection->remove_item()` because of its inconsistent naming. Use `AbstractModelCollection->remove()` instead.

### Removed
