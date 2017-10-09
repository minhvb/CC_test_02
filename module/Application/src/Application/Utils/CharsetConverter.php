<?php
namespace Application\Utils;

/**
 * Charset converter helper
 * A helper class to connect convert
 */
class CharsetConverter
{

    /**
     * Convert string from UTF-8 to Shift_JIS encoding
     *
     * @param string $fromUtf8
     * @return mixed|string
     */
    public static function utf8ToShiftJis($fromUtf8 = '')
    {
        return mb_convert_encoding($fromUtf8, 'SJIS', 'UTF-8');
    }

    /**
     * Convert string from Shift_JIS to UTF-8 encoding
     *
     * @param string $fromShiftJis            
     * @return string
     */
    public static function shiftJisToUtf8($fromShiftJis = '')
    {
        $encodingDetected = mb_detect_encoding($fromShiftJis, 'JIS,SJIS,sjis-win,eucjp-win');
        
        if ($encodingDetected === false) {
            return $fromShiftJis;
        }
        
        return mb_convert_encoding($fromShiftJis, 'UTF-8', $encodingDetected);
    }

    /**
     * Convert string from any charset to UTF-8 encoding
     *
     * @param string $fromAuto
     * @return mixed|string
     */
    public static function toUtf8($fromAuto = '')
    {
        return mb_convert_encoding($fromAuto, 'UTF-8', 'auto');
    }

    /**
     * Convert string from any charset to Shift_JIS encoding
     *
     * @param string $fromAuto
     * @return mixed|string
     */
    public static function toShiftJis($fromAuto = '')
    {
        return mb_convert_encoding($fromAuto, 'SJIS', 'auto');
    }

}