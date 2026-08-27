<?php

if (! function_exists('getContrastColor')) {
    function getContrastColor($hex)
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $brightness > 128 ? '#000' : '#fff';
    }
}



if (!function_exists('currency_bdt')) {
    function currency_bdt($amount) {
        return '৳ ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'BDT') {
        if ($currency === 'BDT') {
            return '৳ ' . number_format((float) $amount, 2);
        }
        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('is_sub_order')) {
    function is_sub_order($order) {
        return $order->is_split_child ?? false;
    }
}

if (!function_exists('is_parent_order')) {
    function is_parent_order($order) {
        return ($order->split_status ?? '') === 'split_parent';
    }
}

if (!function_exists('get_order_type')) {
    function get_order_type($order) {
        if (is_sub_order($order)) {
            return 'Sub-Order';
        }
        if (is_parent_order($order)) {
            return 'Parent Order';
        }
        return 'Main Order';
    }
}

if (!function_exists('get_order_type_badge')) {
    function get_order_type_badge($order) {
        if (is_sub_order($order)) {
            return '<span class="badge badge-warning">Sub-Order</span>';
        }
        if (is_parent_order($order)) {
            return '<span class="badge badge-info">Parent Order</span>';
        }
        return '<span class="badge badge-secondary">Main Order</span>';
    }
}


