<?php

namespace Tests\Feature;

use App\Models\Expense;
use Tests\TestCase;

class FixedExpenseSplitTest extends TestCase
{
    public function test_fixed_shared_expense_is_split_evenly_and_adjustment_is_applied(): void
    {
        $memberIds = [1, 2, 3];
        $amount = 600;

        $this->assertSame(200.0, round(Expense::shareForMembers($amount, $memberIds), 2));
        $this->assertSame(175.0, round(Expense::memberShareForExpense($amount, $memberIds, [1 => -25, 2 => 0, 3 => 25], 1), 2));
        $this->assertSame(225.0, round(Expense::memberShareForExpense($amount, $memberIds, [1 => -25, 2 => 0, 3 => 25], 3), 2));
    }
}
