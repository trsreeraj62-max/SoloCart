<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminReplyMail;

class AdminContactController extends ApiController
{
    /**
     * Get all contact messages (Admin only)
     */
    public function index()
    {
        // Order by created_at desc (newest first), or maybe by status 'new' first?
        // User req: "new first" - so I can order by status desc (since 'new' > 'replied' alphabetically? No. 'new' < 'replied').
        // Let's just do latest() for now, or custom sort.
        // If I want 'new' first, I can orderByRaw("CASE WHEN status = 'new' THEN 1 ELSE 2 END").
        
        $messages = Contact::orderByRaw("CASE WHEN status = 'new' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(20);
            
        return $this->success($messages, "Contact messages retrieved");
    }

    /**
     * Get single contact message details
     */
    public function show($id)
    {
        $message = Contact::find($id);

        if (!$message) {
            return $this->error("Contact message not found", 404);
        }

        return $this->success($message, "Contact message details");
    }

    /**
     * Reply to a contact message
     */
    public function reply(Request $request, $id)
    {
        try {
            $request->validate([
                'reply' => 'required|string|min:5'
            ]);

            $message = Contact::find($id);
            if (!$message) {
                return $this->error("Message not found", 404);
            }

            // Update DB
            $message->admin_reply = $request->reply;
            $message->status = 'replied';
            $message->save();

            // Send Email Safely
            try {
                Mail::to($message->email)->send(new AdminReplyMail($message, $request->reply));
                $emailSent = true;
            } catch (\Exception $e) {
                // Log mail error but don't fail the request
                \Illuminate\Support\Facades\Log::error("Mail send failed for ID $id: " . $e->getMessage());
                $emailSent = false;
            }

            return $this->success([
                'message' => $message,
                'email_sent' => $emailSent
            ], "Reply saved" . ($emailSent ? " and email sent." : " but email failed to send."));

        } catch (\Illuminate\Validation\ValidationException $e) {
             return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error("An error occurred while replying", 500);
        }
    }
}
