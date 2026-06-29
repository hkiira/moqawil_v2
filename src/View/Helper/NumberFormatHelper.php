<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * NumberFormat Helper
 * Formats large numbers into abbreviated format (K, M, B, etc.)
 */
class NumberFormatHelper extends Helper
{
    /**
     * Format a number to abbreviated format with K, M, B suffixes
     * 
     * @param float $number The number to format
     * @param int $decimals Number of decimal places (default: 0)
     * @return string Formatted number (e.g., "17M", "534K", "1.5B")
     */
    public function abbreviate($number, $decimals = 0)
    {
        if ($number == 0) {
            return '0';
        }

        $abs = abs($number);
        $sign = $number < 0 ? '-' : '';

        if ($abs >= 1000000000) {
            // Billions
            return $sign . round($abs / 1000000000, $decimals) . 'B';
        } elseif ($abs >= 1000000) {
            // Millions
            return $sign . round($abs / 1000000, $decimals) . 'M';
        } elseif ($abs >= 1000) {
            // Thousands
            return $sign . round($abs / 1000, $decimals) . 'K';
        } else {
            // Less than 1000
            return $sign . round($abs, $decimals);
        }
    }

    /**
     * Format a number with thousands separator and optional currency
     * This is for detailed displays where you want full precision
     * 
     * @param float $number The number to format
     * @param int $decimals Number of decimal places
     * @param string $suffix Currency or unit suffix (e.g., "DH")
     * @return string Formatted number with separators
     */
    public function formatFull($number, $decimals = 2, $suffix = '')
    {
        $formatted = number_format($number, $decimals, '.', ',');
        return $suffix ? $formatted . ' ' . $suffix : $formatted;
    }

    /**
     * Format a number with abbreviated format for stat cards
     * Combines abbreviation with currency
     * 
     * @param float $number The number to format
     * @param string $suffix Currency or unit suffix (default: "DH")
     * @return string Abbreviated format with suffix
     */
    public function abbreviateWithCurrency($number, $suffix = 'DH')
    {
        $abbreviated = $this->abbreviate($number, 1);
        return $abbreviated . ' ' . $suffix;
    }
}
