<?php

/**
 * Validation Helpers
 * UCC-CES Management System
 */
class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Static factory.
     */
    public static function make(array $data): self
    {
        return new self($data);
    }

    // ── Rules ────────────────────────────────────────────────

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        if (empty(trim((string)($this->data[$field] ?? '')))) {
            $this->errors[$field][] = "{$label} is required.";
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = trim($this->data[$field] ?? '');
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = trim($this->data[$field] ?? '');
        if ($value && mb_strlen($value) < $min) {
            $this->errors[$field][] = "{$label} must be at least {$min} characters.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = trim($this->data[$field] ?? '');
        if (mb_strlen($value) > $max) {
            $this->errors[$field][] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field][] = "{$label} must be a number.";
        }
        return $this;
    }

    public function date(string $field, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = $this->data[$field] ?? '';
        if ($value && !strtotime($value)) {
            $this->errors[$field][] = "{$label} must be a valid date.";
        }
        return $this;
    }

    public function in(string $field, array $options, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        $value = $this->data[$field] ?? '';
        if ($value && !in_array($value, $options, true)) {
            $this->errors[$field][] = "{$label} contains an invalid value.";
        }
        return $this;
    }

    public function confirmed(string $field, string $confirmField, string $label = ''): self
    {
        $label = $label ?: titleCase($field);
        if (($this->data[$field] ?? '') !== ($this->data[$confirmField] ?? '')) {
            $this->errors[$field][] = "{$label} confirmation does not match.";
        }
        return $this;
    }

    // ── Results ──────────────────────────────────────────────

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error for a field (or null).
     */
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get all errors as a flat array of strings.
     */
    public function allErrors(): array
    {
        $flat = [];
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $flat[] = $error;
            }
        }
        return $flat;
    }

    /**
     * Get validated data (only fields that were checked).
     */
    public function validated(): array
    {
        return array_intersect_key(
            $this->data,
            array_flip(array_keys($this->errors) + array_keys($this->data))
        );
    }
}
