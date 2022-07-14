<?php

namespace Threls\LaravelMoney\Casts;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Money as BrickMoney;

interface MoneyContract
{
    public function getBrickMoney(): BrickMoney;

    public function getCurrency(): string;

    public function getMinorAmount(): int;

    public function getAmount(): BigDecimal;

    public function negated(): Money;

    public function abs(): Money;

    public function isNegative(): bool;

    public function isPositive(): bool;

    public function isZero(): bool;

    public function isGreaterThan(Money $money): bool;

    public function isGreaterThanOrEqualTo(Money $money): bool;

    public function isLessThan(Money $money): bool;

    public function isLessThanOrEqualTo(Money $money): bool;

    public function multipliedBy(BigNumber|int|float|string $multiplier): Money;

    public function dividedBy(BigNumber|int|float|string $multiplier): Money;

    public function plus(Money $that): Money;

    public function minus(Money $that): Money;

    public function allocate(int ...$ratios): array;

    public function toArray(): array;

    public static function ofMinor(
        BigNumber|int|float|string $minorAmount,
        BrickCurrency|string|int $currency
    ): Money;

    public static function ofBrick(BrickMoney $money): Money;
}
