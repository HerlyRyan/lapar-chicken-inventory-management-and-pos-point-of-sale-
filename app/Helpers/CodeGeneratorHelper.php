<?php

namespace App\Helpers;

class CodeGeneratorHelper
{
    /**
     * Generate a unique code for a product based on its type and name
     * 
     * @param string $type The type of product (RM for Raw Material, SF for Semi-Finished, FP for Finished Product)
     * @param string $name The product name to base the code on
     * @param string $model The model class name to check for uniqueness
     * @return string The generated unique code
     */
    public static function generateProductCode(string $type, string $name, string $model): string
    {
        // Clean and format the name - take first 3 characters
        $namePrefix = self::cleanString(substr($name, 0, 3));
        
        // Base code format: [TYPE]-[NAME_PREFIX]-[RANDOM_NUMBER]
        $baseCode = strtoupper($type . '-' . $namePrefix);
        
        // Find the last used number for this prefix
        $lastNumber = 1;
        
        $latestProduct = $model::where('code', 'like', $baseCode . '%')
            ->orderBy('code', 'desc')
            ->first();
            
        if ($latestProduct) {
            // Extract the number part from the code
            $codeParts = explode('-', $latestProduct->code);
            if (count($codeParts) >= 3) {
                $lastUsedNumber = intval(end($codeParts));
                $lastNumber = $lastUsedNumber + 1;
            }
        }
        
        // Format the number with leading zeros (3 digits)
        $formattedNumber = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
        
        // Final code: [TYPE]-[NAME_PREFIX]-[SEQUENTIAL_NUMBER]
        return $baseCode . '-' . $formattedNumber;
    }
    
    /**
     * Clean a string for use in a code (remove special characters, etc.)
     * 
     * @param string $string The string to clean
     * @return string The cleaned string
     */
    private static function cleanString(string $string): string
    {
        // Replace accented characters
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        
        // Remove any character that is not a letter or number
        $string = preg_replace('/[^a-zA-Z0-9]/', '', $string);
        
        // Convert to uppercase for consistency
        return strtoupper($string);
    }

    /**
     * Generate a daily sequential code with the pattern: PREFIX-YYYYMMDD-XXX
     * It inspects the given model's $column to find the latest sequence for the day.
     *
     * @param string $prefix e.g. 'DR'
     * @param string|null $date e.g. '20250131' (defaults to today in app timezone)
     * @param string $modelClass Fully qualified model class name (e.g., \App\Models\DestructionReport::class)
     * @param string $column Column to check for uniqueness (default 'report_number')
     * @return string
     */
    public static function generateDailySequentialCode(string $prefix, ?string $date, string $modelClass, string $column = 'report_number'): string
    {
        $datePart = $date ?: now()->format('Ymd');
        $base = strtoupper($prefix . '-' . $datePart . '-');

        /** @var \Illuminate\Database\Eloquent\Model $modelClass */
        $latest = $modelClass::where($column, 'like', $base . '%')
            ->orderBy($column, 'desc')
            ->first();

        $next = 1;
        if ($latest) {
            $parts = explode('-', $latest->{$column});
            $last = intval(end($parts));
            $next = $last + 1;
        }

        $seq = str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        return $base . $seq;
    }
}
