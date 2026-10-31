<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Carbon;

/** The fundraising goal and progress, as entered in the admin. */
class Fundraising
{
    public function __construct(
        public readonly ?float $goal,
        public readonly float $raised,
        public readonly ?Carbon $updatedAt,
    ) {}

    public static function current(): self
    {
        $goal = Setting::get('goal_amount');
        $updated = Setting::get('progress_updated_at');

        return new self(
            goal: is_numeric($goal) ? (float) $goal : null,
            raised: (float) Setting::get('raised_amount', 0),
            updatedAt: $updated ? Carbon::parse($updated) : null,
        );
    }

    /** The progress bar is hidden entirely when there is no goal. */
    public function hasGoal(): bool
    {
        return $this->goal !== null && $this->goal > 0;
    }

    /** Whole-number percent for the bar, capped at 100. */
    public function percent(): int
    {
        if (! $this->hasGoal()) {
            return 0;
        }

        return (int) min(100, max(0, floor($this->raised / $this->goal * 100)));
    }

    public function remaining(): float
    {
        return $this->hasGoal() ? max(0, $this->goal - $this->raised) : 0;
    }

    public static function money(float|string|null $amount): string
    {
        $amount = (float) $amount;
        $decimals = fmod($amount, 1.0) === 0.0 ? 0 : 2;

        return Setting::get('currency_symbol', '₱').number_format($amount, $decimals);
    }
}
