<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends ApiController
{
    /**
     * Store contact message
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            ContactMessage::create($validated);

            return $this->success([], "Message received. We will contact you soon.");
        } catch (\Exception $e) {
            Log::error('Contact message error: ' . $e->getMessage());

            return $this->error(
                "Failed to process your message. Please try again later.",
                500
            );
        }
    }
}
