<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Models\Contact;
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
            // Force JSON response for API
            if (!$request->expectsJson()) {
                // This check is good but headers should be set by frontend. 
                // We proceed anyway to return JSON from the controller.
            }

            $validated = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'nullable|string|max:255', // Optional from frontend
                'message' => 'required|string',
            ]);

            // Prepare data with default subject
            $data = $validated;
            if (empty($data['subject'])) {
                $data['subject'] = 'General Inquiry';
            }
            $data['status'] = 'new'; // Explicit default

            $contact = Contact::create($data);

            return $this->success($contact, "Message received. We will contact you soon.", 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Contact DB Error: ' . $e->getMessage());
            return $this->error("Database error. Please try again later.", 500);
        } catch (\Exception $e) {
            Log::error('Contact General Error: ' . $e->getMessage());
            return $this->error("An unexpected error occurred.", 500);
        }
    }
}
