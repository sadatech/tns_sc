<?php

namespace App\Providers;

use Collective\Html\FormFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);

        // FOR LARAVEL COLLECTIVE
        FormFacade::component('textInput', 'components.form.text_input', ['name', 'value', 'attributes']);
        FormFacade::component('emailInput', 'components.form.email_input', ['name', 'value', 'attributes']);
        FormFacade::component('textareaInput', 'components.form.textarea_input', ['name', 'value', 'attributes']);
        FormFacade::component('dateInput', 'components.form.date_input', ['name', 'value', 'attributes']);
        FormFacade::component('numberInput', 'components.form.number_input', ['name', 'value', 'attributes']);
        FormFacade::component('radioInput', 'components.form.radio_input', ['name', 'value', 'options' => [1 => 'yes', 0 => 'no'], 'attributes']);
        FormFacade::component('selectInput', 'components.form.select_input', ['name', 'value', 'options' => [], 'attributes']);
        FormFacade::component('switchInput', 'components.form.switch_input', ['name', 'value' => 1, 'attributes']);
        FormFacade::component('multipleInput', 'components.form.multiple_input', ['name', 'type', 'values' => [''], 'attributes']);
        FormFacade::component('multipleColumnInput', 'components.form.multiple_column_input', ['name', 'values' => [''], 'columns', 'attributes']);
        FormFacade::component('select2Input', 'components.form.select2_input', ['name', 'value', 'options' => [], 'attributes']);
        FormFacade::component('select2MultipleInput', 'components.form.select2_multiple_input', ['name', 'value', 'options' => [], 'attributes']);
    }
    public function register()
    {
    }
}
