<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
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
        
        $messages = ContactMessage::orderByRaw("CASE WHEN status = 'new' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(20);
            
        return $this->success($messages, "Contact messages retrieved");
    }

    /**
     * Reply to a contact message
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:5'
        ]);

        $message = ContactMessage::find($id);
        if (!$message) {
            return $this->error("Message not found", 404);
        }

        // Update DB
        $message->admin_reply = $request->reply;
        $message->status = 'replied';
        $message->save();

        // Send Email
        try {
            Mail::to($message->email)->send(new AdminReplyMail($message, $request->reply));
            return $this->success($message, "Reply sent and saved successfully");
        } catch (\Exception $e) {
            // Even if mail fails, we saved the reply. Should we rollback?
            // Usually valid to keep it as replied but warn. 
            // For now, let's treat it as success but maybe log error.
            \Illuminate\Support\Facades\Log::error("Mail send failed: " . $e->getMessage());
            return $this->success($message, "Reply saved but email sending failed. Check logs.");
        }
    }
}
