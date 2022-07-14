<?php

use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Money as BrickMoney;
use Threls\LaravelMoney\Casts\Money;

beforeEach(function () {
    config()->set('app.locale', 'en_MT');
});

it('locale', function () {
    $locale = config('money.locale');
    expect($locale)->toBe('en_MT');
    expect(Money::getLocale())->toBe('en_MT');
});

it('create money', function () {
    $minorAmount = 50;
    $amount = 0.5;

    $money = new Money($minorAmount, 'EUR');

    expect($money->amount)->toBe($minorAmount);
    expect($money->currency)->toBe('EUR');
    expect($money->formatted)->toBe('€0.50');

    expect($money->getCurrency())->toBe('EUR');
    expect($money->getMinorAmount())->toBe($minorAmount);

    expect(
        $money->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe($minorAmount);
    expect(
        $money->getBrickMoney()
            ->getAmount()
            ->toFloat()
    )->toBe($amount);
    expect(
        $money->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');
});

it('create money with negative amount', function () {
    $minorAmount = -50;
    $amount = -0.5;

    $money = new Money($minorAmount, 'EUR');

    expect($money->amount)->toBe($minorAmount);
    expect($money->currency)->toBe('EUR');
    expect($money->formatted)->toBe('-€0.50');

    expect($money->getCurrency())->toBe('EUR');
    expect($money->getMinorAmount())->toBe($minorAmount);

    expect(
        $money->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe($minorAmount);
    expect(
        $money->getBrickMoney()
            ->getAmount()
            ->toFloat()
    )->toBe($amount);
    expect(
        $money->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');
});

it('create money with an invalid numeric value', function () {
    new Money(0.5, 'EUR');
})->throws(RoundingNecessaryException::class);

it('create money trying to pass the value as string', function () {
    new Money('a', 'EUR');
})->throws(\Brick\Math\Exception\NumberFormatException::class);

it('negate', function () {
    $money = new Money(50, 'EUR');

    $negated = $money->negated();

    $negatedMinorAmount = -50;
    $negatedAmount = -0.50;

    expect($negated->amount)->toBe($negatedMinorAmount);
    expect($negated->currency)->toBe('EUR');
    expect($negated->formatted)->toBe('-€0.50');

    expect($negated->getCurrency())->toBe('EUR');
    expect($negated->getMinorAmount())->toBe($negatedMinorAmount);

    expect(
        $negated->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe($negatedMinorAmount);
    expect(
        $negated->getBrickMoney()
            ->getAmount()
            ->toFloat()
    )->toBe($negatedAmount);
    expect(
        $negated->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');
});

it('negate negative', function () {
    $money = new Money(-50, 'EUR');

    $negated = $money->negated();

    $resultShouldBe = 50;
    expect($negated->amount)->toBe($resultShouldBe);
    expect($negated->getMinorAmount())->toBe($resultShouldBe);
    expect(
        $negated->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe($resultShouldBe);
});

it('abs', function () {
    $money = new Money(50, 'EUR');
    $moneyNegated = new Money(-50, 'EUR');

    expect($money->abs()->amount)->toBe(50);
    expect($moneyNegated->abs()->amount)->toBe(50);
});

it('is negative', function () {
    $money = new Money(-50, 'EUR');

    expect($money->isNegative())->toBeTrue();
});

it('is not negative', function () {
    $money = new Money(50, 'EUR');

    expect($money->isNegative())->toBeFalse();
});

it('is positive', function () {
    $money = new Money(50, 'EUR');

    expect($money->isPositive())->toBeTrue();
});

it('is not positive', function () {
    $money = new Money(-50, 'EUR');

    expect($money->isPositive())->toBeFalse();
});

it('is zero', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->isZero())->toBeFalse();
    expect($zero->isZero())->toBeTrue();
    expect($positive->isZero())->toBeFalse();
});

it('is greater than', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->isGreaterThan($negative))->toBeFalse();
    expect($negative->isGreaterThan($zero))->toBeFalse();
    expect($negative->isGreaterThan($positive))->toBeFalse();

    expect($zero->isGreaterThan($negative))->toBeTrue();
    expect($zero->isGreaterThan($zero))->toBeFalse();
    expect($zero->isGreaterThan($positive))->toBeFalse();

    expect($positive->isGreaterThan($negative))->toBeTrue();
    expect($positive->isGreaterThan($zero))->toBeTrue();
    expect($positive->isGreaterThan($positive))->toBeFalse();
});

it('is greater than or equal to', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->isGreaterThanOrEqualTo($negative))->toBeTrue();
    expect($negative->isGreaterThanOrEqualTo($zero))->toBeFalse();
    expect($negative->isGreaterThanOrEqualTo($positive))->toBeFalse();

    expect($zero->isGreaterThanOrEqualTo($negative))->toBeTrue();
    expect($zero->isGreaterThanOrEqualTo($zero))->toBeTrue();
    expect($zero->isGreaterThanOrEqualTo($positive))->toBeFalse();

    expect($positive->isGreaterThanOrEqualTo($negative))->toBeTrue();
    expect($positive->isGreaterThanOrEqualTo($zero))->toBeTrue();
    expect($positive->isGreaterThanOrEqualTo($positive))->toBeTrue();
});

it('is less than', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->isLessThan($negative))->toBeFalse();
    expect($negative->isLessThan($zero))->toBeTrue();
    expect($negative->isLessThan($positive))->toBeTrue();

    expect($zero->isLessThan($negative))->toBeFalse();
    expect($zero->isLessThan($zero))->toBeFalse();
    expect($zero->isLessThan($positive))->toBeTrue();

    expect($positive->isLessThan($negative))->toBeFalse();
    expect($positive->isLessThan($zero))->toBeFalse();
    expect($positive->isLessThan($positive))->toBeFalse();
});

it('is less than or equal to', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->isLessThanOrEqualTo($negative))->toBeTrue();
    expect($negative->isLessThanOrEqualTo($zero))->toBeTrue();
    expect($negative->isLessThanOrEqualTo($positive))->toBeTrue();

    expect($zero->isLessThanOrEqualTo($negative))->toBeFalse();
    expect($zero->isLessThanOrEqualTo($zero))->toBeTrue();
    expect($zero->isLessThanOrEqualTo($positive))->toBeTrue();

    expect($positive->isLessThanOrEqualTo($negative))->toBeFalse();
    expect($positive->isLessThanOrEqualTo($zero))->toBeFalse();
    expect($positive->isLessThanOrEqualTo($positive))->toBeTrue();
});

it('multiplied by', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->multipliedBy(-2)->amount)->toBe(100);
    expect($negative->multipliedBy(0)->amount)->toBe(0);
    expect($negative->multipliedBy(2)->amount)->toBe(-100);

    expect($zero->multipliedBy(-2)->amount)->toBe(0);
    expect($zero->multipliedBy(0)->amount)->toBe(0);
    expect($zero->multipliedBy(2)->amount)->toBe(0);

    expect($positive->multipliedBy(-2)->amount)->toBe(-100);
    expect($positive->multipliedBy(0)->amount)->toBe(0);
    expect($positive->multipliedBy(2)->amount)->toBe(100);
});

it('divided by', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->dividedBy(-2)->amount)->toBe(25);
    expect($negative->dividedBy(2)->amount)->toBe(-25);

    expect($zero->dividedBy(-2)->amount)->toBe(0);
    expect($zero->dividedBy(2)->amount)->toBe(0);

    expect($positive->dividedBy(-2)->amount)->toBe(-25);
    expect($positive->dividedBy(2)->amount)->toBe(25);
});

it('divided by - division by zero', function () {
    $negative = new Money(-50, 'EUR');
    $negative->dividedBy(0);
})->throws(\Brick\Math\Exception\DivisionByZeroException::class);

it('plus', function () {
    $money = new Money(50, 'EUR');
    $money2 = new Money(10, 'EUR');

    $result = $money->plus($money2);

    expect($result->amount)->toBe(60);
    expect($money->currency)->toBe('EUR');
});

it('plus mismatch', function () {
    $money = new Money(50, 'EUR');
    $money2 = new Money(10, 'GBP');

    $money->plus($money2);
})->throws(\Brick\Money\Exception\MoneyMismatchException::class);

it('minus', function () {
    $money50 = new Money(50, 'EUR');
    $money10 = new Money(10, 'EUR');

    $result40 = $money50->minus($money10);
    $result40n = $money10->minus($money50);

    expect($result40->amount)->toBe(40);
    expect($result40n->amount)->toBe(-40);
    expect($result40->currency)->toBe('EUR');
    expect($result40n->currency)->toBe('EUR');
});

it('minus mismatch', function () {
    $money = new Money(50, 'EUR');
    $money2 = new Money(10, 'GBP');

    $money->minus($money2);
})->throws(\Brick\Money\Exception\MoneyMismatchException::class);

it('allocate', function () {
    $money = new Money(100, 'EUR');

    [$a50_50_1, $a50_50_2] = $money->allocate(50, 50);
    [$a70_20_10_1, $a70_20_10_2, $a70_20_10_3] = $money->allocate(70, 20, 10);

    expect($a50_50_1->amount)->toBe(50);
    expect($a50_50_2->amount)->toBe(50);
    expect($a50_50_1->currency)->toBe('EUR');
    expect($a50_50_2->currency)->toBe('EUR');

    expect($a70_20_10_1->amount)->toBe(70);
    expect($a70_20_10_2->amount)->toBe(20);
    expect($a70_20_10_3->amount)->toBe(10);
    expect($a70_20_10_1->currency)->toBe('EUR');
    expect($a70_20_10_2->currency)->toBe('EUR');
    expect($a70_20_10_3->currency)->toBe('EUR');
});

it('allocate negative', function () {
    $money = new Money(-100, 'EUR');

    [$a50_50_1, $a50_50_2] = $money->allocate(50, 50);
    [$a70_20_10_1, $a70_20_10_2, $a70_20_10_3] = $money->allocate(70, 20, 10);

    expect($a50_50_1->amount)->toBe(-50);
    expect($a50_50_2->amount)->toBe(-50);
    expect($a50_50_1->currency)->toBe('EUR');
    expect($a50_50_2->currency)->toBe('EUR');

    expect($a70_20_10_1->amount)->toBe(-70);
    expect($a70_20_10_2->amount)->toBe(-20);
    expect($a70_20_10_3->amount)->toBe(-10);
    expect($a70_20_10_1->currency)->toBe('EUR');
    expect($a70_20_10_2->currency)->toBe('EUR');
    expect($a70_20_10_3->currency)->toBe('EUR');
});

it('cannot allocate negative ratios', function () {
    $money = new Money(100, 'EUR');

    $money->allocate(50, -50);
})->throws(InvalidArgumentException::class);

it('to array', function () {
    $negative = new Money(-50, 'EUR');
    $zero = new Money(0, 'EUR');
    $positive = new Money(50, 'EUR');

    expect($negative->toArray())->toMatchArray([
                                                   'currency' => 'EUR',
                                                   'amount' => -50,
                                                   'formatted' => '-€0.50',
                                               ]);

    expect($zero->toArray())->toMatchArray([
                                               'currency' => 'EUR',
                                               'amount' => 0,
                                               'formatted' => '€0.00',
                                           ]);
    expect($positive->toArray())->toMatchArray([
                                                   'currency' => 'EUR',
                                                   'amount' => 50,
                                                   'formatted' => '€0.50',
                                               ]);
});

test('of minor', function () {
    $negative = Money::ofMinor(-50, 'EUR');
    $zero = Money::ofMinor(0, 'EUR');
    $positive = Money::ofMinor(50, 'EUR');
    $gbp = Money::ofMinor(50, 'GBP');

    expect($negative->amount)->toBe(-50);
    expect($negative->currency)->toBe('EUR');
    expect(
        $negative->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe(-50);
    expect(
        $negative->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');

    expect($zero->amount)->toBe(0);
    expect($zero->currency)->toBe('EUR');
    expect(
        $zero->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe(0);
    expect(
        $zero->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');

    expect($positive->amount)->toBe(50);
    expect($positive->currency)->toBe('EUR');
    expect(
        $positive->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe(50);
    expect(
        $positive->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('EUR');

    expect($gbp->amount)->toBe(50);
    expect($gbp->currency)->toBe('GBP');
    expect(
        $gbp->getBrickMoney()
            ->getMinorAmount()
            ->toInt()
    )->toBe(50);
    expect(
        $gbp->getBrickMoney()
            ->getCurrency()
            ->getCurrencyCode()
    )->toBe('GBP');
});

test('of brick', function () {
    $eur00_50 = Money::ofBrick(BrickMoney::ofMinor(50, 'EUR'));
    $eur50_00 = Money::ofBrick(BrickMoney::of(50, 'EUR'));
    $eur00_50n = Money::ofBrick(BrickMoney::ofMinor(-50, 'EUR'));
    $eur50_00n = Money::ofBrick(BrickMoney::of(-50, 'EUR'));

    $gbp00_50 = Money::ofBrick(BrickMoney::ofMinor(50, 'GBP'));
    $gbp50_00 = Money::ofBrick(BrickMoney::of(50, 'GBP'));

    expect($eur00_50->amount)->toBe(50);
    expect($eur00_50->currency)->toBe('EUR');

    expect($eur50_00->amount)->toBe(5000);
    expect($eur50_00->currency)->toBe('EUR');

    expect($eur00_50n->amount)->toBe(-50);
    expect($eur00_50n->currency)->toBe('EUR');

    expect($eur50_00n->amount)->toBe(-5000);
    expect($eur50_00n->currency)->toBe('EUR');

    expect($gbp00_50->amount)->toBe(50);
    expect($gbp00_50->currency)->toBe('GBP');

    expect($gbp50_00->amount)->toBe(5000);
    expect($gbp50_00->currency)->toBe('GBP');
});
