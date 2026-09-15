<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Member;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'mdmithurahman41@gmail.com')->first();
if (!$user) {
    $mess = \App\Models\Mess::create(['name' => 'Rahman Mess', 'address' => 'Dhaka']);
    $user = User::create([
        'name' => 'Manager',
        'email' => 'mdmithurahman41@gmail.com',
        'password' => bcrypt('password'),
        'role' => 'mess_admin',
        'mess_id' => $mess->id
    ]);
}
$messId = $user->mess_id;
$today = Carbon::today()->format('Y-m-d');
$month = '2026-09'; // Based on the column dates being Sep. The PDF says July-2026 but columns say Sep. The prompt didn't specify month, but "Opening Expense for today" implies current period. Let's use 2026-09 for the meals as per the headers.

// Members to add
$membersData = [
    'Abdullah Al Mamun' => [
        'payment' => 679.16,
        'meals' => [
            1 => 1, 4 => 0, 6 => 1, 7 => 1, 10 => 0, 11 => 1
        ]
    ],
    'Ahsan Anwarul Islam' => [
        'payment' => 1528.47,
        'meals' => [
            1 => 1, 2 => 1, 3 => 1, 4 => 0, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 0, 12 => 1, 13 => 1, 14 => 1, 15 => 1
        ]
    ],
    'Ariyan Biddut (Driver)' => [
        'payment' => 1378.64,
        'meals' => [
            1 => 1, 2 => 1, 3 => 1, 4 => 0, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 0, 12 => 1, 14 => 1, 15 => 1
        ]
    ],
    'Md. Mithu Rahman' => [
        'payment' => 1612.07,
        'meals' => [
            1 => 1, 2 => 1, 3 => 1, 4 => 0, 5 => 1, 6 => 2, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 0, 13 => 2, 14 => 1, 15 => 1
        ]
    ],
    'Md. Shafi' => [
        'payment' => 1320.61,
        'meals' => [
            1 => 1, 2 => 1, 3 => 1, 4 => 0, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 2, 10 => 1, 11 => 0, 12 => 1, 13 => 1, 14 => 1, 15 => 1
        ]
    ],
    'Md. Mirazul Islam +Leal Lens' => [
        'payment' => 1632.00,
        'meals' => [
            2 => 1, 3 => 1, 4 => 0, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 0, 12 => 1, 13 => 1, 14 => 1, 15 => 1
        ]
    ],
    'InterX Gueast' => [
        'payment' => 3.03,
        'meals' => [
            3 => 0, 8 => 0, 9 => 2
        ]
    ],
];

DB::beginTransaction();
try {
    // 1. Insert Total Expense
    Expense::create([
        'mess_id' => $messId,
        'date' => $today,
        'category' => 'Opening',
        'amount' => 8190.00,
        'note' => 'Opening Expense.',
        'type' => 'meal', // Treat it as meal expense so it contributes to meal rate
    ]);

    foreach ($membersData as $name => $data) {
        $member = Member::firstOrCreate([
            'mess_id' => $messId,
            'name' => $name,
        ], [
            'status' => 'active',
            'join_date' => $today,
        ]);

        // 2. Insert Payment
        if ($data['payment'] > 0) {
            Payment::create([
                'mess_id' => $messId,
                'member_id' => $member->id,
                'date' => $today,
                'amount' => $data['payment'],
                'payment_type' => 'cash',
                'note' => 'Opening Balance',
            ]);
        }

        // 3. Insert Meals
        foreach ($data['meals'] as $day => $count) {
            $date = Carbon::create(2026, 9, $day)->format('Y-m-d');
            Meal::create([
                'mess_id' => $messId,
                'member_id' => $member->id,
                'date' => $date,
                'meal_count' => $count,
                'note' => 'Meal Date wise person wise',
            ]);
        }
    }
    
    DB::commit();
    echo "Data imported successfully!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
