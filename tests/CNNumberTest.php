<?php

namespace HughCube\CNNumber\Tests;

use HughCube\CNNumber\CNNumber;
use PHPUnit\Framework\TestCase;

class CNNumberTest extends TestCase
{
    public function testToLower()
    {
        $this->assertSame('零点一', CNNumber::toLower(0.1));
        $this->assertSame('零点零一', CNNumber::toLower(0.01));
        $this->assertSame('零点零一零一', CNNumber::toLower(0.0101));

        $this->assertSame('一点一', CNNumber::toLower(1.1));
        $this->assertSame('一点零一', CNNumber::toLower(1.01));
        $this->assertSame('一点零一零一', CNNumber::toLower(1.0101));

        $this->assertSame('一', CNNumber::toLower(1));
        $this->assertSame('一万零一', CNNumber::toLower(10001));
        $this->assertSame('一亿零一', CNNumber::toLower(100000001));
        $this->assertSame('一亿零一百万零一百零一', CNNumber::toLower(101000101));
        $this->assertSame('一百一十亿零一万零一', CNNumber::toLower(11000010001));

        $this->assertSame('二', CNNumber::toLower(2));
        $this->assertSame('二十', CNNumber::toLower(20));
        $this->assertSame('二百', CNNumber::toLower(200));
        $this->assertSame('两千', CNNumber::toLower(2000));
        $this->assertSame('两万', CNNumber::toLower(20000));
        $this->assertSame('二十万', CNNumber::toLower(200000));
        $this->assertSame('二百万', CNNumber::toLower(2000000));
        $this->assertSame('两千万', CNNumber::toLower(20000000));
        $this->assertSame('两亿', CNNumber::toLower(200000000));
        $this->assertSame('二十亿', CNNumber::toLower(2000000000));
        $this->assertSame('二百亿', CNNumber::toLower(20000000000));
        $this->assertSame('两千亿', CNNumber::toLower(200000000000));

        $this->assertSame('一', CNNumber::toLower(1));
        $this->assertSame('十', CNNumber::toLower(10));
        $this->assertSame('一百', CNNumber::toLower(100));
        $this->assertSame('一千', CNNumber::toLower(1000));
        $this->assertSame('一万', CNNumber::toLower(10000));
        $this->assertSame('十万', CNNumber::toLower(100000));
        $this->assertSame('一百万', CNNumber::toLower(1000000));
        $this->assertSame('一千万', CNNumber::toLower(10000000));
        $this->assertSame('一亿', CNNumber::toLower(100000000));
        $this->assertSame('十亿', CNNumber::toLower(1000000000));
        $this->assertSame('一百亿', CNNumber::toLower(10000000000));
        $this->assertSame('一千亿', CNNumber::toLower(100000000000));

        $this->assertSame('二', CNNumber::toLower(2));
        $this->assertSame('二十二', CNNumber::toLower(22));
        $this->assertSame('二百二十二', CNNumber::toLower(222));
        $this->assertSame('两千二百二十二', CNNumber::toLower(2222));
        $this->assertSame('两万两千二百二十二', CNNumber::toLower(22222));
        $this->assertSame('二十二万两千二百二十二', CNNumber::toLower(222222));
        $this->assertSame('二百二十二万两千二百二十二', CNNumber::toLower(2222222));
        $this->assertSame('两千二百二十二万两千二百二十二', CNNumber::toLower(22222222));
        $this->assertSame('两亿两千二百二十二万两千二百二十二', CNNumber::toLower(222222222));
        $this->assertSame('二十二亿两千二百二十二万两千二百二十二', CNNumber::toLower(2222222222));
        $this->assertSame('二百二十二亿两千二百二十二万两千二百二十二', CNNumber::toLower(22222222222));
        $this->assertSame('两千二百二十二亿两千二百二十二万两千二百二十二', CNNumber::toLower(222222222222));

        $this->assertSame('一', CNNumber::toLower(1));
        $this->assertSame('十一', CNNumber::toLower(11));
        $this->assertSame('一百一十一', CNNumber::toLower(111));
        $this->assertSame('一千一百一十一', CNNumber::toLower(1111));
        $this->assertSame('一万一千一百一十一', CNNumber::toLower(11111));
        $this->assertSame('十一万一千一百一十一', CNNumber::toLower(111111));
        $this->assertSame('一百一十一万一千一百一十一', CNNumber::toLower(1111111));
        $this->assertSame('一千一百一十一万一千一百一十一', CNNumber::toLower(11111111));
        $this->assertSame('一亿一千一百一十一万一千一百一十一', CNNumber::toLower(111111111));
        $this->assertSame('十一亿一千一百一十一万一千一百一十一', CNNumber::toLower(1111111111));
        $this->assertSame('一百一十一亿一千一百一十一万一千一百一十一', CNNumber::toLower(11111111111));
        $this->assertSame('一千一百一十一亿一千一百一十一万一千一百一十一', CNNumber::toLower(111111111111));

        $this->assertSame('一千七百兆零九亿两千二百七十六万一千二百三十四', CNNumber::toLower(1700000922761234));
    }

    public function testToCapital()
    {
        $this->assertSame('壹佰柒拾兆零两仟零贰拾柒万零叁拾肆', CNNumber::toCapital(170000020270034));
    }

    public function testToRmb()
    {
        $this->assertSame('两仟零贰拾柒万零叁拾肆元玖角叁分壹厘', CNNumber::toRmb(20270034.9310));
    }
}
