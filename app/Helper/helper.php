<?php

if (!function_exists('normalizeArabic')) {
    /**
     * Normalize Arabic text for searching or processing.
     *
     * @param string $text
     * @return string
     */
    function normalizeArabic(string $text): string {

        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $text = str_replace($arabic, $english, $text);

        $text = str_replace(['أ','إ','آ'], 'ا', $text);
        $text = str_replace('ى', 'ي', $text);
        $text = preg_replace('/[ًٌَُِْ]/u', '', $text); // حذف التشكيل
        $text = str_replace('ـ', '', $text); // حذف التطويل
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}
