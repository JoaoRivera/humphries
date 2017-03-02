<?php

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if ($value[0] === '"' && $value[count($value) - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('filter_by_keys')) {
    /**
     * Filters array by allowed keys
     *
     * @param  mixed  $values
     * @param  array  $allowed
     *
     * @return array
     */
    function filter_by_keys($values, array $allowed)
    {
        if (is_object($values) && method_exists($values, 'toArray')) {
            $values = $values->toArray();
        } else if ($values instanceof \stdClass) {
            $values = (array) $values;
        }

        return array_filter(
            $values,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}

if (! function_exists('is_json')) {
    /**
     * Check if string is json.
     *
     * @param  string $value
     *
     * @return bool
     */
    function is_json($value)
    {
        return is_string($value) && is_array(json_decode($value, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}