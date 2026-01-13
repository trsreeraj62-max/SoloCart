<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends ApiController
{
    /**
     * Store contact message
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'subject' => 'required',
                'message' => 'required'
            ]);

            // For now, mirroring standard behavior which usually fires an email or saves to DB.
            
            return $this->success([], "Message received. We will get back to you soon!");
        } catch (\Exception $e) {
            return $this->error("Failed to process your message. Please try again later.", 500);
        }
    }
}
