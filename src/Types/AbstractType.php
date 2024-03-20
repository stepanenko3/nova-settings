<?php

namespace Stepanenko3\NovaSettings\Types;

use Laravel\Nova\Panel;

abstract class AbstractType
{
    abstract public function fields(): array;

    public function getFields(
        string $attribute,
    ) {
        $fields = $this->fields();

        $this->prepareFields(
            fields: $fields,
            attribute: $attribute,
        );

        return $fields;
    }

    public function prepareFields(
        array &$fields,
        string $attribute,
    ): void {
        foreach ($fields as &$field) {
            if ($field instanceof Panel) {
                $this->prepareFields(
                    $field->data,
                    $attribute,
                );
            } else {
                $field->attribute = "{$attribute}->{$field->attribute}";
                $field->hideFromIndex();
                $field->hideWhenCreating();
            }
        }
    }
}
