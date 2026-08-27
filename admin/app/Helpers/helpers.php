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



if (!function_exists('app_currency_symbol')) {
    function app_currency_symbol() {
        // Defensive: this runs on virtually every page, including any
        // moment before the settings table/migration exists (fresh installs,
        // a DB hiccup) -- never let a missing setting take the whole page down.
        try {
            return \App\Models\Setting::get('currency_symbol') ?: '৳';
        } catch (\Throwable $e) {
            return '৳';
        }
    }
}

if (!function_exists('currency_bdt')) {
    function currency_bdt($amount) {
        return app_currency_symbol() . ' ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'BDT') {
        if ($currency === 'BDT') {
            return app_currency_symbol() . ' ' . number_format((float) $amount, 2);
        }
        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}

// These delegate to App\Models\Order's own accessors (isSplitChild()/
// isSplitParent(), getOrderTypeLabelAttribute/getOrderTypeBadgeAttribute)
// instead of re-deriving the same split-order logic from raw fields, so
// there's one place -- the model -- that decides what counts as a sub-order.

if (!function_exists('is_sub_order')) {
    function is_sub_order($order) {
        return $order?->is_sub_order ?? false;
    }
}

if (!function_exists('is_parent_order')) {
    function is_parent_order($order) {
        return $order?->is_parent_order ?? false;
    }
}

if (!function_exists('get_order_type')) {
    function get_order_type($order) {
        return $order?->order_type_label ?? 'Main Order';
    }
}

if (!function_exists('get_order_type_badge')) {
    function get_order_type_badge($order) {
        return $order?->order_type_badge ?? '<span class="badge badge-secondary">Main Order</span>';
    }
}


