<?php

namespace Tests\Unit;

use App\Support\Fundraising;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FundraisingTest extends TestCase
{
    public static function progressCases(): array
    {
        return [
            'quarter' => [500000, 125000, 25],
            'rounds down, never overstates' => [300000, 199999, 66],
            'just under the goal is not 100' => [1000, 999.99, 99],
            'exactly the goal' => [1000, 1000, 100],
            'over the goal is capped' => [1000, 2500, 100],
            'nothing raised' => [1000, 0, 0],
            'tiny amount' => [1000000, 500, 0],
        ];
    }

    #[DataProvider('progressCases')]
    public function test_percent(float $goal, float $raised, int $expected): void
    {
        $this->assertSame($expected, (new Fundraising($goal, $raised, null))->percent());
    }

    public function test_no_goal_means_no_bar(): void
    {
        foreach ([null, 0.0, -5.0] as $goal) {
            $fund = new Fundraising($goal, 5000, null);
            $this->assertFalse($fund->hasGoal());
            $this->assertSame(0, $fund->percent());
            $this->assertSame(0.0, (float) $fund->remaining());
        }
    }

    public function test_remaining_never_goes_negative(): void
    {
        $this->assertSame(375000.0, (new Fundraising(500000, 125000, null))->remaining());
        $this->assertSame(0.0, (float) (new Fundraising(1000, 2000, null))->remaining());
    }
}
