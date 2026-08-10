# Contributing to syscage/sdk-shopee-php

Thanks for your interest in contributing! This document covers everything
you need to get started.

## Getting Started

Clone your fork, then install dependencies:

```bash
composer install
```

PHP `^8.1` is required (see `composer.json` for the authoritative supported
range).

## Project Structure

```text
src/
├── Core/          Framework-independent SDK — no Laravel/Symfony dependency
└── Framework/
    ├── Laravel/   Optional Laravel integration (Service Provider, Facade)
    └── Symfony/   Optional Symfony integration (Bundle, DI extension)
tests/
├── Unit/          No network access or credentials required
└── Integration/   Requires your own Shopee sandbox credentials (see below)
```

## Running Tests

```bash
vendor/bin/phpunit
```

This runs the unit suite only — no network access or credentials required,
and it's what CI runs on every pull request.

An additional integration suite exercises the SDK against the real Shopee
sandbox API:

```bash
vendor/bin/phpunit --testsuite integration
```

It requires your own Shopee Open Platform **sandbox** Partner credentials in
a local `.env` file and is skipped automatically when none are present.
Never commit `.env` or any credentials.

## Coding Standards

- PHP `^8.1`, `declare(strict_types=1)` in every file
- PSR-4 autoloading, PSR-12-compatible style consistent with the existing
  codebase
- Prefer small, focused classes and composition over inheritance; avoid
  introducing abstractions (DTOs, request/response objects, etc.) that
  aren't already justified by the surrounding code
- Keep `src/Core/` free of any Laravel/Symfony/framework dependency —
  framework-specific code belongs under `src/Framework/`
- Add tests for new or changed behavior; keep unit tests network-free

## Submitting Changes

1. Fork the repository and create a branch for your change.
2. Make your change, with tests covering the new/changed behavior.
3. Run `vendor/bin/phpunit` and `composer validate --strict` locally.
4. Open a pull request describing the change and the motivation behind it.

Keep pull requests focused on a single logical change — it makes them much
easier to review.

## Reporting Issues

Please use the issue tracker to report bugs or request features. Include:

- SDK version and PHP version
- Steps to reproduce
- Expected vs. actual behavior
- Any relevant error messages (with credentials/tokens redacted)

## Documentation

For the full documentation — installation, configuration, authentication,
API reference, Push Mechanism, Laravel, Symfony, and testing — see:

**[Read the full documentation](https://doc.syscage.com/sdk-shopee-php)**

## License

By contributing, you agree that your contributions will be licensed under
the [MIT License](LICENSE).
