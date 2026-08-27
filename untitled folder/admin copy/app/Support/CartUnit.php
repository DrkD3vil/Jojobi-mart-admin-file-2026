<?php

namespace App\Support;

class CartUnit
{
    // configurable ratios
    private const PCS_PER_DOZEN = 12;
    private const PCS_PER_BOX   = 1; // change if 1 box != 1 pcs

    // convert sale_qty (unit) -> batch_unit qty
    public static function toBatchQty(float $saleQty, string $saleUnit, string $batchUnit): float
    {
        $saleUnit  = strtolower($saleUnit);
        $batchUnit = strtolower($batchUnit);

        if ($saleQty <= 0) return 0;

        // same unit
        if ($saleUnit === $batchUnit) return $saleQty;

        /*
        |--------------------------------------------------------------------------
        | PCS / DOZEN / BOX
        |--------------------------------------------------------------------------
        */
        $pcsUnits = ['pcs', 'dozen', 'box'];

        if (in_array($saleUnit, $pcsUnits, true) && in_array($batchUnit, $pcsUnits, true)) {
            // convert sale → pcs
            $pcsQty = match ($saleUnit) {
                'pcs'   => $saleQty,
                'dozen' => $saleQty * self::PCS_PER_DOZEN,
                'box'   => $saleQty * self::PCS_PER_BOX,
            };

            // convert pcs → batch
            return match ($batchUnit) {
                'pcs'   => $pcsQty,
                'dozen' => $pcsQty / self::PCS_PER_DOZEN,
                'box'   => $pcsQty / self::PCS_PER_BOX,
            };
        }

        /*
        |--------------------------------------------------------------------------
        | WEIGHT
        |--------------------------------------------------------------------------
        */
        if ($batchUnit === 'kg' && $saleUnit === 'g') return $saleQty / 1000;
        if ($batchUnit === 'g'  && $saleUnit === 'kg') return $saleQty * 1000;

        /*
        |--------------------------------------------------------------------------
        | VOLUME
        |--------------------------------------------------------------------------
        */
        if ($batchUnit === 'l'  && $saleUnit === 'ml') return $saleQty / 1000;
        if ($batchUnit === 'ml' && $saleUnit === 'l')  return $saleQty * 1000;

        return 0; // invalid conversion
    }

    public static function allowedUnits(string $batchUnit): array
    {
        $u = strtolower($batchUnit);

        return match ($u) {
            'kg', 'g'      => ['kg', 'g'],
            'l', 'ml'      => ['l', 'ml'],
            'pcs', 'dozen', 'box' => ['pcs', 'dozen', 'box'],
            default        => ['pcs'],
        };
    }
}
