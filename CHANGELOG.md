# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2026-01-XX

### Breaking Changes
- **Minimum PHP version updated to 7.4.0** - PHP 5.x, 7.0, 7.1, 7.2 and 7.3 are no longer supported
- Type declarations added to method signatures
- Typed properties used throughout the codebase

### Security Improvements
- Timing-safe comparison (hash_equals) used for webhook signature verification
- HTTP header injection protection added (CR/LF characters are sanitized)
- SSRF protection: HTTP redirects are disabled
- OpenSSL extension is now required

### New Features
- Full support for PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5
- PHP 8.2+ compatibility with `#[\AllowDynamicProperties]` attribute
- `Tahsilat::reset()` method added for testing environments
- `verifyHmacSignature()` and `parseSignatureHeader()` methods added to Webhook class

### Improvements
- cURL handle check uses `CurlHandle` class for PHP 8.0+
- Better error messages and exception handling
- PHPStan level 6 compatibility
- `JSON_THROW_ON_ERROR` used in JSON operations
- `PHP_QUERY_RFC3986` encoding for query strings

### Fixes
- `curl_close()` deprecation issue fixed for PHP 8.0+
- Null safety improvements

## [1.1.6] - Previous Version

- PHP 5.6+ support (no longer supported)
- Initial stable release