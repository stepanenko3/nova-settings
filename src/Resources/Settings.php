<?php

namespace Stepanenko3\NovaSettings\Resources;

use Illuminate\Database\Query\Builder;
use Stepanenko3\NovaSettings\Types\AbstractType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use ReflectionClass;

class Settings extends Resource
{
    public static $model;

    public static $title = 'slug';

    public static $search = [
        'slug',
    ];

    public static $globallySearchable = true;

    public static $displayInNavigation = true;

    public static function group(): string
    {
        return __('Settings');
    }

    public static function label(): string
    {
        return __('Settings');
    }

    public static function singularLabel(): string
    {
        return __('Settings');
    }

    public static function newModel()
    {
        self::$model =  config('nova-settings.model');

        return new self::$model();
    }

    public function fields(
        Request $request,
    ): array {
        $fields = [];

        if ($this->type && class_exists($this->type)) {
            $fields = (new $this->type())->getFields('settings');
        }

        return [
            ...$this->main(
                request: $request,
            ),
            ...$fields,
            new Panel(
                name: 'Data',
                fields: $this->data(),
            ),
        ];
    }

    public function cards(
        Request $request,
    ): array {
        return [];
    }

    public function filters(
        Request $request,
    ): array {
        return [];
    }

    public function lenses(
        Request $request,
    ): array {
        return [];
    }

    public function actions(
        Request $request,
    ): array {
        return [];
    }

    protected function main(
        Request $request,
    ): array {
        return [
            ID::make(__('ID'), 'id')
                ->sortable(),

            Text::make(__('Slug'), 'slug')
                ->creationRules([
                    'required',
                    'max:255',
                    Rule::unique('settings', 'slug')
                        ->where(function (Builder $query) use ($request) {
                            return $query
                                ->where('env', $request->env)
                                ->where('type', $request->type);
                        }),
                ])
                ->updateRules([
                    'required',
                    'max:255',
                    Rule::unique('settings', 'slug')
                        ->where(function (Builder $query) use ($request) {
                            return $query
                                ->where('env', $request->env)
                                ->where('type', $request->type ?: $this->type);
                        })
                        ->ignore($request->resourceId),
                ]),

            Text::make(__('Env'), 'env')
                ->rules([
                    'required',
                ]),

            Select::make(__('Type'), 'type')
                ->options(
                    $this->getClasses()->toArray(),
                )
                ->readonly(
                    fn (NovaRequest $request) => $request->isUpdateOrUpdateAttachedRequest(),
                )
                ->rules([
                    'required',
                ]),
        ];
    }

    protected function data()
    {
        return [
            DateTime::make(__('Created At'), 'created_at')
                ->exceptOnForms()
                ->sortable(),

            DateTime::make(__('Updated At'), 'updated_at')
                ->onlyOnDetail(),
        ];
    }

    private function getClasses()
    {
        return collect(config('nova-settings.types'))
            ->filter(function ($class) {
                if (!class_exists($class)) {
                    return false;
                }

                $reflection = new ReflectionClass($class);

                return $reflection->isSubclassOf(AbstractType::class)
                    && !$reflection->isAbstract();
            })
            ->mapWithKeys(function ($class) {
                $parts = explode('\\', $class);
                $className = end($parts);

                return [
                    $class => $className,
                ];
            });
    }
}
