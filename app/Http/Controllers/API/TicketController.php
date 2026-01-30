<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        // 🔥 pastikan user login
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'subject'     => 'required|string|max:255',
            'category'    => 'required|string',
            'priority'    => 'required|in:low,medium,high',
            'description' => 'required|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'subject'     => $request->subject,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'description' => $request->description,
            'status'      => 'Open',
            'attachment'  => $attachmentPath,
            'user_id'     => $user->id, // 🔥 AMAN
        ]);

        return response()->json([
            'success' => true,
            'data'    => $ticket,
        ], 201);
    }
}
