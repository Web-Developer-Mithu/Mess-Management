<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create()
    {
        $members = Member::orderBy('name')->get();

        return view('payments.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_type' => ['required', 'in:cash,bkash,bank,other'],
            'note' => ['nullable', 'string'],
        ]);

        Payment::create($validated);

        return redirect()->route('dashboard', ['month' => date('Y-m', strtotime($validated['date']))])
            ->with('success', 'Payment recorded successfully.');
    }
}
