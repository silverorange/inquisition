# Inquisition

A package for creating, managing, and running online quizzes.

Inquisition is responsible for the following basic object types and related
tables:

- Inquisition (quizzes)
- InquisitionQuestion
- InquisitionInquisitionQuestionBinding
- InquisitionQuestionOption
- InquisitionResponse
- InquisitionResponseValue

Additional objects are provided for extended features:

- InquisitionQuestionImage
- InquisitionQuestionOptionImage
- InquisitionQuestionGroup
- InquisitionQuestionHint
- InquisitionResponseUsedHintBinding

It provides pages for displaying these objects and admin tools for managing
them.

There is also a CSV importer and exporter for question management.

## Installation

```sh
composer require silverorange/inquisition
```
