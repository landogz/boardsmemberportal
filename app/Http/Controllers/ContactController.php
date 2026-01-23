<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
            ], [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a valid string.',
                'name.max' => 'The name may not be greater than 255 characters.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.max' => 'The email may not be greater than 255 characters.',
                'subject.required' => 'The subject field is required.',
                'subject.string' => 'The subject must be a valid string.',
                'subject.max' => 'The subject may not be greater than 255 characters.',
                'message.required' => 'The message field is required.',
                'message.string' => 'The message must be a valid string.',
                'message.max' => 'The message may not be greater than 5000 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $data = $validator->validated();

            // Get admin email from config or use a default
            $adminEmail = config('mail.admin_email', config('mail.from.address'));

            // Send email notification
            try {
                Mail::to($adminEmail)->send(new ContactFormMail($data));
            } catch (\Exception $mailException) {
                \Log::error('Failed to send contact form email: ' . $mailException->getMessage());
                // Continue even if email fails - we still want to acknowledge the submission
            }

            // Log the contact form submission
            \Log::info('Contact form submission', [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again later.'
            ], 500);
        }
    }
}
