<?php

namespace Application\Utils;

/**
 * Class DateHelper
 * @package Application\Utils
 */
class DateHelper
{

    const DATE_FORMAT = 'Y/m/d';
    
    const DATE_TIME_FORMAT = 'Y/m/d H:i';
	
    public static function getCurrentDateTime(){
        return new \DateTime('now');
    }

    public static function getCurrentYear(){
        return self::getCurrentDateTime()->format('Y');
    }

    public static function getCurrentMonth(){
        return self::getCurrentDateTime()->format('m');
    }

    public static function getCurrentDay(){
        return self::getCurrentDateTime()->format('d');
    }

    public static function convertDateToNumber($date = '')
    {
        if (empty($date)) {
            $dateTime = new \DateTime('now');
        } else {
            $dateTime = new \DateTime($date);
        }
        return $dateTime->getTimestamp();
    }

    public static function getCurrentTimeStamp()
    {
        return self::convertDateToNumber();
    }

    public static function isCorrectFormatDate($date, $format = 'Y/m/d')
    {
        if (empty($date)) return false;

        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function convertTimestampToString($timestamp, $format = self::DATE_FORMAT){
        return self::convertTimestampToDateTime($timestamp)->format($format);
    }

    public static function convertTimestampToDateTime($timestamp){
        $dateTime = new \DateTime();
        return $dateTime->setTimestamp($timestamp);
    }

    /**
     * @param $date \DateTime
     * @return string
     */
    public static function convertDateTimeToGengo($date){
        $year = $date->format('Y');
        $year = self::gengo($year);
        $month = $date->format('m');
        $date = $date->format('d');
        return sprintf('%s年%s月%s日', $year, $month, $date);
    }

    public static function convertTimestampToGengo($timestamp){
        return self::convertDateTimeToGengo(self::convertTimestampToDateTime($timestamp));
    }

    public static function gengo($year)
    {
        // tinh nam shouwa ex: value - 1925
        if ($year >= 1911 && $year <= 1925) {
            $no = $year - 1911;

            return "大正" . $no;
        }

        // tinh nam shouwa ex: value - 1925
        if ($year > 1925 && $year <= 1988) {
            $no = $year - 1925;

            return "昭和" . $no;
        }
        // tinh nam heisei
        if ($year >= 1989) {
            $no = $year - 1988;

            return "平成" . $no;
        }

        return $year;
    }

}
