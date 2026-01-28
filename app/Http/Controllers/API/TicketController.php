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
         $user = $request->user(); 
        try {
            // 🔹 VALIDASI
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
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // 🔹 SIMPAN FILE (opsional)
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('tickets', 'public');
            }

            // 🔹 CREATE TICKET
            $ticket = Ticket::create([
                'subject'     => $request->subject,
                'category'    => $request->category,
                'priority'    => $request->priority,
                'description' => $request->description,
                'status'      => 'Open',
                'attachment'  => $attachmentPath,
                'user_id'     => auth()->id(), // pastikan pakai auth middleware
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket berhasil dibuat',
                'data'    => $ticket,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat ticket',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
