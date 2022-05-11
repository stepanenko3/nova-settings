<?php

namespace Stepanenko3\NovaSettings\Resources;

use Stepanenko3\NovaSettings\Types\AbstractType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;

class Settings extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static $globallySearchable = true;

    /**
     * Hide resource from Nova's standard menu.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    public static function group()
    {
        return __('Settings');
    }

    /**
     * Label for display.
     *
     * @return string
     */
    public static function label()
    {
        return __('Settings');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Settings');
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    {
        self::$model =  config('nova-settings.model');

        return new self::$model();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields = [];
        if ($this->type && class_exists($this->type)) {
            $fields = (new $this->type)->getFields('settings');
        }

        return [
            ...$this->main($request),
            ...$fields,
            new Panel('Data', $this->data()),
        ];
    }

    protected function main(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')
                ->sortable(),

            Text::make(__('Slug'), 'slug')
                ->creationRules([
                    'required',
                    'max:255',
                    Rule::unique('settings', 'slug')
                        ->where(function ($query) use ($request) {
                            return $query
                                ->where('env', $request->env)
                                ->where('type', $request->type);
                        }),
                ])
                ->updateRules([
                    'required',
                    'max:255',
                    Rule::unique('settings', 'slug')
                        ->where(function ($query) use ($request) {
                            return $query
                                ->where('env', $request->env)
                                ->where('type', $request->type ?: $this->type);
                        })
                        ->ignore($request->resourceId)
                ]),

            Text::make(__('Env'), 'env')
                ->rules('required'),

            Select::make(__('Type'), 'type')
                ->options($this->getClasses()->toArray())
                ->readonly(fn ($request) => $request->isUpdateOrUpdateAttachedRequest())
                ->rules('required'),
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
                if (!class_exists($class))
                    return false;

                $reflection = new \ReflectionClass($class);

                return $reflection->isSubclassOf(AbstractType::class) &&
                    !$reflection->isAbstract();
            })
            ->mapWithKeys(function ($class) {
                $parts = explode('\\', $class);
                $className = end($parts);

                return [$class => $className];
            });
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
