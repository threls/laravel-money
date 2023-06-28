<?php

namespace Threls\LaravelMoney\Casts;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Money as BrickMoney;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Money implements Castable, MoneyContract
{
    protected BrickMoney $money;

    public string        $currency;

    public int           $amount;

    public string        $formatted;

    public static function getLocale()
    {
        return config('money.locale') ?? 'en_MT';
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function __construct(
        BigNumber|int|float|string $minorAmount,
        BrickCurrency|string|int $currency = 'EUR'
    ) {
        $this->money = BrickMoney::ofMinor($minorAmount, $currency);
        $this->currency = $this->money->getCurrency()
            ->getCurrencyCode();
        $this->amount = $this->money->getMinorAmount()
            ->toInt();
        $this->formatted = $this->money->formatTo($this->getLocale());
    }

    public function getBrickMoney(): BrickMoney
    {
        return $this->money;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getMinorAmount(): int
    {
        return $this->amount;
    }

    public function getAmount(): BigDecimal
    {
        return $this->money->getAmount();
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function negated(): self
    {
        return self::ofBrick($this->money->negated());
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function abs(): self
    {
        return self::ofBrick($this->money->abs());
    }

    public function isNegative(): bool
    {
        return $this->money->isNegative();
    }

    public function isZero(): bool
    {
        return $this->money->isZero();
    }

    public function isPositive(): bool
    {
        return $this->money->isPositive();
    }

    /**
     * @param  self  $money
     *
     * @return bool
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function isGreaterThan(self $money): bool
    {
        return $this->money->isGreaterThan($money->getBrickMoney());
    }

    /**
     * @param  self  $money
     *
     * @return bool
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function isLessThan(self $money): bool
    {
        return $this->money->isLessThan($money->getBrickMoney());
    }

    /**
     * @param  self  $money
     *
     * @return bool
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function isLessThanOrEqualTo(self $money): bool
    {
        return $this->money->isLessThanOrEqualTo($money->getBrickMoney());
    }

    /**
     * @param  self  $money
     *
     * @return bool
     * @throws \Brick\Money\Exception\MoneyMismatchException
     */
    public function isGreaterThanOrEqualTo(self $money): bool
    {
        return $this->money->isGreaterThanOrEqualTo($money->getBrickMoney());
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function multipliedBy(BigNumber|int|float|string $multiplier, int $roundingMode = RoundingMode::UNNECESSARY): self
    {
        return self::ofBrick($this->money->multipliedBy($multiplier, $roundingMode));
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function dividedBy(BigNumber|int|float|string $multiplier, int $roundingMode = RoundingMode::UNNECESSARY): self
    {
        return self::ofBrick($this->money->dividedBy($multiplier, $roundingMode));
    }

    /**
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function plus(self $that): self
    {
        return self::ofBrick($this->money->plus($that->getBrickMoney()));
    }

    /**
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public function minus(self $that): self
    {
        return self::ofBrick($this->money->minus($that->getBrickMoney()));
    }

    public function allocate(int ...$ratios): array
    {
        return collect($this->money->allocate(...$ratios))
            ->map(fn (BrickMoney $value) => self::ofBrick($value))
            ->toArray();
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->money->getCurrency()
                ->getCurrencyCode(),
            'amount' => $this->money->getMinorAmount()
                ->toInt(),
            'formatted' => $this->money->formatTo($this->getLocale()),
        ];
    }

    /**
     * @throws \Exception
     */
    public function __set(string $name, $value): void
    {
        throw new Exception('Money is immutable');
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public static function ofMinor(
        BigNumber|int|float|string $minorAmount,
        BrickCurrency|string|int $currency
    ): self {
        return new self($minorAmount, $currency);
    }

    /**
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
    public static function ofBrick(BrickMoney $money): self
    {
        return new self($money->getMinorAmount(), $money->getCurrency());
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class () implements CastsAttributes {
            protected ?string $currency;

            public function __construct(string $currency = null)
            {
                $this->currency = $currency;
            }

            public function get($model, string $key, $value, array $attributes): ?Money
            {
                if ($value === null) {
                    return null;
                }

                return new Money($value, $this->getCurrency($attributes));
            }

            public function set($model, string $key, $value, array $attributes): array
            {
                if ($value === null) {
                    return [$key => $value];
                }

                $currency = $this->getCurrency($attributes);

                if ($value instanceof Money) {
                    $money = $value;
                } else {
                    $money = new Money($value, $currency);
                }

                $amount = $money->amount;

                if (array_key_exists($this->currency, $attributes)) {
                    return [$key => $amount, $this->currency => $money->currency];
                } else {
                    return [$key => $amount];
                }
            }

            protected function getCurrency(array $attributes): BrickCurrency
            {
                if ($this->currency === null || ! array_key_exists($this->currency, $attributes)) {
                    return BrickCurrency::of('EUR');
                }

                if ($attributes[$this->currency] instanceof BrickCurrency) {
                    return $attributes[$this->currency];
                }

                return BrickCurrency::of($attributes[$this->currency]);
            }
        };
    }
}
