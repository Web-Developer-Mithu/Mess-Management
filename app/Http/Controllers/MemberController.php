<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function create()
    {
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'join_date' => ['nullable', 'date'],
        ]);

        Member::create($validated);

        return redirect()->route('dashboard')->with('success', 'Member added successfully.');
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'join_date' => ['nullable', 'date'],
        ]);

        $member->update($validated);

        return redirect()->route('dashboard')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('dashboard')->with('success', 'Member moved to trash.');
    }

    public function restore($memberId)
    {
        $memberRecord = Member::withTrashed()->findOrFail($memberId);
        $memberRecord->restore();

        return redirect()->route('dashboard')->with('success', 'Member restored from trash.');
    }

    public function trash()
    {
        $trashedMembers = Member::onlyTrashed()->get();

        return view('members.trash', compact('trashedMembers'));
    }
}
