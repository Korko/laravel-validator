<?php

namespace Korko\Validator;

use Illuminate\Support\ServiceProvider;
use Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    protected function addArrayUnique()
    {
        Validator::extend('arrayunique', function ($attribute, $value, $parameters, $validator) {
            return is_array($value) && count($value) === count(array_unique($value));
        }, trans('korko::validation.arrayunique'));
    }

    protected function addFieldIn()
    {
        Validator::extend('fieldin', function ($attribute, $value, $parameters, $validator) {
            if(count($parameters) < 1) {
                return FALSE;
            }

            if(!isset($validator->getData()[$parameters[0]]) || !is_array($validator->getData()[$parameters[0]])) {
                return FALSE;
            }

            $values = $validator->getData()[$parameters[0]];
            for($i=1; $i<count($parameters); $i++) {
                unset($values[$parameters[$i]]);
            }
            return (array_search($value, $values, true) !== false);
        }, trans('korko::validation.fieldin'));

        Validator::replacer('fieldin', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':other', $parameters[0], $message);
        });
    }

    protected function addFieldInKeys()
    {
        Validator::extend('fieldinkeys', function ($attribute, $value, $parameters, $validator) {
            if(count($parameters) < 1) {
                return FALSE;
            }

            if(!isset($validator->getData()[$parameters[0]]) || !is_array($validator->getData()[$parameters[0]])) {
                return FALSE;
            }

            $values = $validator->getData()[$parameters[0]];
            for($i=1; $i<count($parameters); $i++) {
                unset($values[$parameters[$i]]);
            }
            return array_key_exists($value, $values);
        }, trans('korko::validation.fieldinkeys'));

        Validator::replacer('fieldinkeys', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':other', $parameters[0], $message);
        });
    }

    protected function addContains()
    {
        Validator::extend('contains', function ($attribute, $value, $parameters, $validator) {
            return is_string($value) && array_reduce((array) $parameters, function ($return, $find) use ($value) {
                return $return && strpos($value, $find) !== false;
            }, true);
        }, trans('korko::validation.contains'));

        Validator::replacer('contains', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':values', implode(', ', $parameters), $message);
        });
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'korko');

        $this->addArrayUnique();
        $this->addFieldIn();
        $this->addFieldInKeys();
        $this->addContains();
    }

    public function register()
    {
        //
    }
}
