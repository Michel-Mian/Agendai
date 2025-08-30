<?php

if (!function_exists('convertToFloat')) {
    function convertToFloat($value) {
        if (empty($value)) return 0;
        $value = preg_replace('/[^\d,.]/', '', $value);
        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }
        return (float) $value;
    }
}