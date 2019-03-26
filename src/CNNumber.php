<?php

namespace HughCube\CNNumber;

use InvalidArgumentException;

class CNNumber
{
    /**
     * @var array 小写数字的对照表
     */
    protected static $lowerDigitLookup = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九'];

    /**
     * @var array 大写数字的对照表
     */
    protected static $capitalDigitLookup = ['', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];

    /**
     * @var array 单位对照表
     */
    protected static $digitUnitLookup = ['', '万', '亿', '兆', '京', '垓', '秭', '穰', '沟', '涧', '正', '载'];

    /**
     * @var array 小写小单位对照表
     */
    protected static $lowerDigitUnitLookup = ['十', '百', '千'];

    /**
     * @var array 大写小单位对照
     */
    protected static $capitalDigitUnitLookup = ['拾', '佰', '仟'];

    /**
     * @var array 人民币的单位对照
     */
    protected static $rmbUnitLookup = ['角', '分', '厘', '毫'];

    /**
     * 转小写
     *
     * @return string
     */
    public static function toLower($number)
    {
        $digitUnitLookup = static::buildUnitLookup(static::$lowerDigitUnitLookup);
        $resultsArray = static::convert($number, static::$lowerDigitLookup, $digitUnitLookup);

        $results = "{$resultsArray['signed']}{$resultsArray['integer']}";
        if (!empty($resultsArray['decimal'])){
            $results .= "点{$resultsArray['decimal']}";
        }

        return $results;
    }

    /**
     * 转大写
     *
     * @return string
     */
    public static function toCapital($number)
    {
        $digitUnitLookup = static::buildUnitLookup(static::$capitalDigitUnitLookup);
        $resultsArray = static::convert($number, static::$capitalDigitLookup, $digitUnitLookup);

        $results = "{$resultsArray['signed']}{$resultsArray['integer']}";
        if (!empty($resultsArray['decimal'])){
            $results .= "点{$resultsArray['decimal']}";
        }

        return $results;
    }

    /**
     * 转成人民币
     *
     * @return string
     */
    public static function toRmb($number)
    {
        $digitUnitLookup = static::buildUnitLookup(static::$capitalDigitUnitLookup);

        $resultsArray = static::convert($number, static::$capitalDigitLookup, $digitUnitLookup, static::$rmbUnitLookup);

        $results = "{$resultsArray['signed']}{$resultsArray['integer']}元";
        if (empty($resultsArray['decimal'])){
            $results .= '整';
        }else{
            $results .= "{$resultsArray['decimal']}";
        }

        return $results;
    }

    /**
     * @param float
     * @param array $digitLookup
     * @param array $digitUnitLookup
     * @param array|null $decimalUnitLookup
     * @return array return [
     *                      'signed' => '符号',
     *                      'integer' => '整数部分',
     *                      'decimal' => '小数部分',
     *               ];
     */
    protected static function convert($number, array $digitLookup, array $digitUnitLookup, array $decimalUnitLookup = null)
    {
        /** 必须是一个数字 */
        if (!is_numeric($number)){
            throw new InvalidArgumentException('$number必须是一个数字.');
        }

        /** @var bool $isNegative 是否负数 */
        $isNegative = 0 > $number;

        /** 如果传递进来的是科学计数法的, 转换成正常计数, 保留4位小数, 所以这里会存在一个精度的问题 */
        $_ = strval($number);
        $_ = (false === strpos($_, 'E')) ? $_ : number_format($number, 4, '.', '');


        $numberArray = explode('.', $_);

        /** @var string $integerString 整数部分, 无符号, 清除左边的0 */
        $integerString = isset($numberArray[0]) ? ltrim($numberArray[0], '0') : '0';
        $integerString = ltrim($integerString, '-');
        $integerString = empty($integerString) ? '0' : $integerString;

        /** @var string $decimalString 小数部分, 无0., 清除右边的0 */
        $decimalString = isset($numberArray[1]) ? rtrim($numberArray[1], '0') : '';


        /** 如果设置了小数的单位, 截取到最大单位 */
        $decimalString = null === $decimalUnitLookup ? $decimalString : substr($decimalString, 0, count($decimalUnitLookup));
        $decimalString = rtrim($decimalString, '0');

        /** 不能超过最大的单位 */
        if ((strlen($integerString)) > max(array_keys($digitUnitLookup)) + 1){
            throw new InvalidArgumentException('$number too large.');
        }

        // 整数部分
        $integerResults = '';
        $integerStringLength = strlen($integerString);
        for($index = 0; $index < $integerStringLength; $index++){
            /** 上一个数字 */
            $pDigit = isset($digit) ? $digit : null;

            /** 上一个单位 */
            $pUnitIndex = isset($unitIndex) ? $unitIndex : null;

            /** 单个的数字 */
            $digit = $integerString[$index];

            /** 假设的单位数组的index */
            $guessUnitIndex = $integerStringLength - $index - 1;

            /** 零的读法, 非零的数据的上一位是零才记录, 避免重复的零记录 */
            $isZero = '0' === $integerString                      // 整个整数部分就是零, 就是零
                      || ('0' !== $digit && '0' === $pDigit);  // 当前非零, 上一个数字是零
            $zero = $isZero ? '零' : '';

            /** 数字的读法 */
            $num = $digitLookup[$digit];

            /** 单位读法, 判断是否需要单位 */
            $isEmptyUnit = ('0' === $digit && (0 != $guessUnitIndex % 4))                        //  刚好处于单位升级, 四位升一级
                           || '0000' === substr($integerString, $index - 3, 4);  //  连续的四个零  100001000, 万是不需要读出来的
            $unitIndex = $isEmptyUnit ? null : $guessUnitIndex;
            $unit = null === $unitIndex ? '' : $digitUnitLookup[$unitIndex];

            /** 10 如果在起始位置, 并且单位是十, 读法不是一十, 而是十 */
            if (0 == $index
                && '1' === $digit
                && (1 == $unitIndex % 4)
            ){
                $num = '';
            }

            /**
             * 二需要特殊处理  2, 20, 200   不能读两, 两十,  两百
             * 22       二十二
             * 222      二百二十二
             * 2222     两千二百二十二
             * 22222    两万两千二百二十二
             * 其他的则要读两
             */
            if ('2' === $digit
                && ((3 == $unitIndex % 4) || ((0 === $unitIndex % 4) && ($unitIndex >= 4)))
                && (1 != $pUnitIndex % 4)
            ){
                $num = '两';
            }

            //var_dump("{$unit} --- {$unitIndex}");

            /** 拼接 */
            $integerResults .= ($zero . $num . $unit);
        }

        // 小数部分拼接
        $decimalResults = '';
        $decimalStringLength = strlen($decimalString);
        for($index = 0; $index < $decimalStringLength; $index++){
            /** 传递了小数的单位, 但是已经超过了 */
            if (null !== $decimalUnitLookup && !isset($decimalUnitLookup[$index])){
                break;
            }

            /** 单个的数字 */
            $digit = $decimalString{$index};

            /** 零的读法, 零的直接读零 */
            $zero = '0' === $digit ? '零' : '';

            /** 数字的读法 */
            $num = $digitLookup[$digit];

            /** 单位读法 */
            $unit = isset($decimalUnitLookup[$index]) ? $decimalUnitLookup[$index] : '';

            /** 拼接 */
            $decimalResults .= ($zero . $num . $unit);
        }

        return [
            'signed' => $isNegative ? '负' : '',
            'integer' => $integerResults,
            'decimal' => $decimalResults,
        ];
    }

    /**
     * 合并单位的对照表
     *
     * @param $lowerDigitUnitLookup
     * @return array
     */
    protected static function buildUnitLookup(array $lowerDigitUnitLookup)
    {
        $lookup = [];
        foreach(static::$digitUnitLookup as $item1){
            $lookup[] = $item1;
            foreach($lowerDigitUnitLookup as $item2){
                $lookup[] = $item2;
            }
        }

        return $lookup;
    }
}
