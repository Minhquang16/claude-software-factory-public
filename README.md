# Claude Software Factory — Public CI Demo

This repository contains an isolated **example WordPress shortcode plugin** and its GitHub Actions CI workflow. It is a public demonstration artifact, not a copy of the private Claude Software Factory workspace.

## Included

- `examples/csf-sample-shortcode/` — example WordPress shortcode plugin and unit tests.
- `.github/workflows/csf-sample-shortcode-ci.yml` — CI checks for the example plugin.

## Deliberately excluded

- Developer and Reviewer agent instructions and skills.
- Private project documentation, WordPress MCP connections, credentials, and environment configuration.
- Claude OAuth review workflow (not approved for public execution).
- The original repository's Git history.

## Local checks

From `examples/csf-sample-shortcode/` with PHP and Composer installed:

```bash
composer install
composer lint
composer test
```

The CI workflow must be verified on this new repository before relying on it; CI results from the private repository do not carry over.

## Safety

Do not add secrets, customer site files, production credentials, or personal information. This code is a development and CI example, not production-ready WordPress functionality. No automatic deployment is configured.

## Source and history

This repository uses a new root commit and an explicit allowlist of example files. It was not forked or mirrored from the private workspace.
