<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMenu;
use Illuminate\Http\Request;

class WeeklyMenuController extends Controller
{
    private const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function edit()
    {
        $menus = WeeklyMenu::query()->get()->keyBy('weekday');
        $days = self::DAYS;

        return view('menus.weekly', compact('menus', 'days'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'menus' => ['required', 'array'],
            'menus.*' => ['nullable', 'string', 'max:2000'],
            'market_persons' => ['required', 'array'],
            'market_persons.*' => ['nullable', 'string', 'max:255'],
            'market_conditions' => ['required', 'array'],
            'market_conditions.*' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach (self::DAYS as $weekday => $day) {
            $menu = trim($validated['menus'][$weekday] ?? '');
            $marketPerson = trim($validated['market_persons'][$weekday] ?? '');
            $marketCondition = trim($validated['market_conditions'][$weekday] ?? '');

            WeeklyMenu::updateOrCreate(
                ['weekday' => $weekday],
                [
                    'menu' => $menu !== '' ? $menu : null,
                    'market_person' => $marketPerson !== '' ? $marketPerson : null,
                    'market_condition' => $marketCondition !== '' ? $marketCondition : null,
                ]
            );
        }

        return redirect()->route('menus.weekly')->with('success', 'সাপ্তাহিক খাবারের মেনু আপডেট হয়েছে।');
    }
}