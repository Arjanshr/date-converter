<?php

namespace Arjanshr\DateConverter;


class DateConverter
{

    // Data for nepali date
    private $bs_array;

    // Constructor to initialize the bs_array
    public function __construct()
    {
        $this->bs_array = config('bs_date.bs');
    }

    private $_nep_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'nmonth' => '', 'num_day' => '');
    private $_eng_date = array('year' => '', 'month' => '', 'date' => '', 'day' => '', 'emonth' => '', 'num_day' => '');
    public $debug_info = "";

    private $days = [
        1 => "आईतवार", 2 => "सोमबार", 3 => "मंगलवार", 4 => "बुधबार",
        5 => "बिहीबार", 6 => "शुक्रबार", 7 => "शनिबार"
    ];

    private $englishMonths = [
        1 => "January", 2 => "February", 3 => "March", 4 => "April",
        5 => "May", 6 => "June", 7 => "July", 8 => "August",
        9 => "September", 10 => "October", 11 => "November", 12 => "December"
    ];

    private $nepaliMonths = [
        1 => "बैशाख", 2 => "जेष्ठ", 3 => "असार", 4 => "श्रावण",
        5 => "भाद्र", 6 => "आश्विन", 7 => "कार्तिक", 8 => "मंसिर",
        9 => "पुष", 10 => "माघ", 11 => "फाल्गुन", 12 => "चैत्र"
    ];

    // Return day in Nepali
    private function _get_day_of_week($day)
    {
        return $this->days[$day] ?? 'Invalid day';
    }

    // Return English month name
    private function _get_english_month($m)
    {
        return $this->englishMonths[$m] ?? false;
    }

    // Return Nepali month name
    private function _get_nepali_month($m)
    {
        return $this->nepaliMonths[$m] ?? false;
    }

    // Check if date is in valid English date range
    private function _is_in_range_eng($yy, $mm, $dd)
    {
        if ($yy < 1944 || $yy > 2033) {
            return 'Supported only between 1944-2033';
        }
        if ($mm < 1 || $mm > 12) {
            return 'Error! month value can be between 1-12 only';
        }
        if ($dd < 1 || $dd > 31) {
            return 'Error! day value can be between 1-31 only';
        }
        return true;
    }

    // Check if date is within Nepali date range
    private function _is_in_range_nep($yy, $mm, $dd)
    {
        if ($yy < 2000 || $yy > 2089) {
            return 'Supported only between 2000-2089';
        }
        if ($mm < 1 || $mm > 12) {
            return 'Error! month value can be between 1-12 only';
        }
        if ($dd < 1 || $dd > 31) {
            return 'Error! day value can be between 1-31 only';
        }
        return true;
    }

    // Check if a year is a leap year
    private function is_leap_year($year)
    {
        return ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0));
    }
    /**
     * currently can only calculate the date between AD 1944-2033...
     *
     * @param int $yy
     * @param int $mm
     * @param int $dd
     * @return array
     */
    public function engToNep($yy, $mm, $dd, $nepali_format = false)
    {
        // Check for date range
        $chk = $this->_is_in_range_eng($yy, $mm, $dd);
        if ($chk !== TRUE) {
            die($chk);
        } else {
            // Month data.
            $month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

            // Month for leap year
            $lmonth = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
            $def_eyy = 1944;    // initial english date.
            $def_nyy = 2000;
            $def_nmm = 9;
            $def_ndd = 17 - 1;    // inital nepali date.
            $total_eDays = 0;
            $total_nDays = 0;
            $a = 0;
            $day = 7 - 1;
            $m = 0;
            $y = 0;
            $i = 0;
            $j = 0;
            $numDay = 0;
            // Count total no. of days in-terms year
            for ($i = 0; $i < ($yy - $def_eyy); $i++) //total days for month calculation...(english)
            {
                if ($this->is_leap_year($def_eyy + $i) === TRUE) {
                    for ($j = 0; $j < 12; $j++) {
                        $total_eDays += $lmonth[$j];
                    }
                } else {
                    for ($j = 0; $j < 12; $j++) {
                        $total_eDays += $month[$j];
                    }
                }
            }
            // Count total no. of days in-terms of month
            for ($i = 0; $i < ($mm - 1); $i++) {
                if ($this->is_leap_year($yy) === TRUE) {
                    $total_eDays += $lmonth[$i];
                } else {
                    $total_eDays += $month[$i];
                }
            }
            // Count total no. of days in-terms of date
            $total_eDays += $dd;
            $i = 0;
            $j = $def_nmm;
            $total_nDays = $def_ndd;
            $m = $def_nmm;
            $y = $def_nyy;
            // Count nepali date from array
            while ($total_eDays != 0) {
                $a = $this->bs_array[$i][$j];

                $total_nDays++;        //count the days
                $day++;                //count the days interms of 7 days
                if ($total_nDays > $a) {
                    $m++;
                    $total_nDays = 1;
                    $j++;
                }

                if ($day > 7) {
                    $day = 1;
                }

                if ($m > 12) {
                    $y++;
                    $m = 1;
                }

                if ($j > 12) {
                    $j = 1;
                    $i++;
                }

                $total_eDays--;
            }
            $numDay = $day;
            $this->_nep_date['year'] = $nepali_format ? $this->convert_to_nepali_number($y) : $y;
            $this->_nep_date['month'] = $nepali_format ? $this->convert_to_nepali_number($m) : $m;
            $this->_nep_date['date'] = $nepali_format ? $this->convert_to_nepali_number($total_nDays) : $total_nDays;
            $this->_nep_date['day'] = $this->_get_day_of_week($day);
            $this->_nep_date['nmonth'] = $this->_get_nepali_month($m);
            $this->_nep_date['num_day'] = $nepali_format ? $this->convert_to_nepali_number($numDay) : $numDay;
            return $this->_nep_date;
        }
    }

    /**
     * Currently can only calculate the date between BS 2000-2089
     *
     * @param int $yy
     * @param int $mm
     * @param int $dd
     * @return array
     */
    public function nepToEng($yy, $mm, $dd)
    {
        $def_eyy = 1943;
        $def_emm = 4;
        $def_edd = 14 - 1;    // initial english date.
        $def_nyy = 2000;
        $def_nmm = 1;
        $def_ndd = 1;        // iniital equivalent nepali date.
        $total_eDays = 0;
        $total_nDays = 0;
        $a = 0;
        $day = 4 - 1;
        $m = 0;
        $y = 0;
        $i = 0;
        $k = 0;
        $numDay = 0;
        $month = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $lmonth = array(0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        // Check for date range
        $chk = $this->_is_in_range_nep($yy, $mm, $dd);
        if ($chk !== TRUE) {
            die($chk);
        } else {
            // Count total days in-terms of year
            for ($i = 0; $i < ($yy - $def_nyy); $i++) {
                for ($j = 1; $j <= 12; $j++) {
                    $total_nDays += $this->bs_array[$k][$j];
                }
                $k++;
            }
            // Count total days in-terms of month
            for ($j = 1; $j < $mm; $j++) {
                $total_nDays += $this->bs_array[$k][$j];
            }
            // Count total days in-terms of dat
            $total_nDays += $dd;
            // Calculation of equivalent english date...
            $total_eDays = $def_edd;
            $m = $def_emm;
            $y = $def_eyy;
            while ($total_nDays != 0) {
                if ($this->is_leap_year($y)) {
                    $a = $lmonth[$m];
                } else {
                    $a = $month[$m];
                }
                $total_eDays++;
                $day++;
                if ($total_eDays > $a) {
                    $m++;
                    $total_eDays = 1;
                    if ($m > 12) {
                        $y++;
                        $m = 1;
                    }
                }
                if ($day > 7) {
                    $day = 1;
                }
                $total_nDays--;
            }

            $numDay = $day;
            $this->_eng_date['year'] = $y;
            $this->_eng_date['month'] = $m;
            $this->_eng_date['date'] = $total_eDays;
            $this->_eng_date['day'] = $this->_get_day_of_week($day);
            $this->_eng_date['nmonth'] = $this->_get_english_month($m);
            $this->_eng_date['num_day'] = $numDay;
            return $this->_eng_date;
        }
    }

    private function convert_to_nepali_number($value)
    {
        $nepali_numbers = [
            '0' => '०',
            '1' => '१',
            '2' => '२',
            '3' => '३',
            '4' => '४',
            '5' => '५',
            '6' => '६',
            '7' => '७',
            '8' => '८',
            '9' => '९'
        ];
        return strtr($value, $nepali_numbers);
    }
}
