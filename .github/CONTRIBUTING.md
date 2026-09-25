# Contributing to EdifactParser

Thank you for your interest in contributing! 🎉  
We welcome all pull requests, bug reports, and suggestions to help improve the project.

---

## 📜 Code of Conduct

By participating in this project, you agree to follow our [Code of Conduct](CODE_OF_CONDUCT.md).

---

## ⚖️ Licensing

All contributions are made under the [MIT License](https://github.com/Chemaclass/EdifactParser/blob/main/LICENSE).

---

## 🐛 Reporting Bugs

Please include the following in your bug report:

- A brief summary and context
- Steps to reproduce (with code samples if possible)
- What you expected to happen
- What actually happened
- Any relevant notes or observations

📌 Please format your code and output as text (not screenshots) using [Markdown syntax](https://guides.github.com/features/mastering-markdown/).

---

## 🔧 Pull Request Workflow

1. Fork and clone the repo.
2. Run `composer install` to install dependencies (PHP 8.2+ to run every check; the
   library itself supports 8.0).
3. Create a branch from `main`.
4. Make your changes and add tests. CI requires 100% line coverage.
5. Apply code-style and Rector fixes:
    - `composer fix`
6. Run the same checks CI runs:
    - `composer test` (static analysis, unit and functional tests)
    - `composer examples` if you touched the docs or a public API
7. Submit your pull request 🎉
    - Make sure your Git name/email is correctly set
      up ([guide](https://git-scm.com/book/en/v2/Getting-Started-First-Time-Git-Setup)). 
