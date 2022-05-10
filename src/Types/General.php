<?php

namespace Stepanenko3\NovaSettings\Types;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Boolean;

class General extends AbstractType
{
    public function fields(): array
    {
        return [
            Boolean::make('Param 1', 'param_1'),
            Boolean::make('Param 2', 'param_2'),
            Boolean::make('Param 3', 'param_3'),

            new Panel('Tab', [
                Boolean::make('Param 4', 'param_4'),
                Boolean::make('Param 5', 'param_5'),
                Boolean::make('Param 6', 'param_6'),
            ]),
        ];
    }
}
