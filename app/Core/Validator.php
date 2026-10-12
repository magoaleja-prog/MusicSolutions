<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Lightweight form validator.
 *
 * Rules are strings like "required|email|max:255" or "unique:users,email".
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(
        private array $data,
        private array $rules,
        private array $labels = []
    ) {
        $this->run();
    }

    public static function make(array $data, array $rules, array $labels = []): self
    {
        return new self($data, $rules, $labels);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors === [] ? null : (string) reset($this->errors);
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleSet) {
            $value = $this->data[$field] ?? null;
            $label = $this->labels[$field] ?? $field;

            foreach (explode('|', (string) $ruleSet) as $rule) {
                if ($rule === '') {
                    continue;
                }

                [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                if ($this->hasError($field)) {
                    continue;
                }

                $this->apply($field, $label, $name, $parameter, $value);
            }
        }
    }

    private function apply(string $field, string $label, string $rule, ?string $parameter, mixed $value): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, "El campo {$label} es obligatorio.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$label} debe ser un correo valido.");
                }
                break;

            case 'min':
                if ($value !== null && mb_strlen((string) $value) < (int) $parameter) {
                    $this->addError($field, "El campo {$label} debe tener al menos {$parameter} caracteres.");
                }
                break;

            case 'max':
                if ($value !== null && mb_strlen((string) $value) > (int) $parameter) {
                    $this->addError($field, "El campo {$label} no debe superar {$parameter} caracteres.");
                }
                break;

            case 'confirmed':
                if (($this->data[$field . '_confirmation'] ?? null) !== $value) {
                    $this->addError($field, "La confirmacion de {$label} no coincide.");
                }
                break;

            case 'in':
                $allowed = explode(',', (string) $parameter);
                if ($value !== null && $value !== '' && !in_array((string) $value, $allowed, true)) {
                    $this->addError($field, "El valor de {$label} no es valido.");
                }
                break;

            case 'unique':
                [$table, $column] = array_pad(explode(',', (string) $parameter, 2), 2, 'id');
                if ($value !== null && $value !== '') {
                    $statement = Database::connection()->prepare(
                        "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value"
                    );
                    $statement->execute(['value' => $value]);
                    if ((int) $statement->fetchColumn() > 0) {
                        $this->addError($field, "El campo {$label} ya esta registrado.");
                    }
                }
                break;
        }
    }

    private function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}
