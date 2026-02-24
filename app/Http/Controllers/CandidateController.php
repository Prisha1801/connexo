<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname'   => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'required|string|max:20',
            'position'   => 'required|string',
            'experience' => 'required|string',
            'salary'     => 'required|string',
            'message'    => 'nullable|string',
            'resume'     => 'nullable|mimes:pdf,doc,docx|max:10240', 
        ]);

        // Store resume in public/resume folder using candidate name + unique ID
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');

            // Clean candidate name for file system
            $cleanName = Str::slug($validated['fullname'], '_');

            // Generate unique ID
            $uniqueId = uniqid();

            // Combine name + unique ID
            $filename = $cleanName . '_' . $uniqueId . '.' . $file->getClientOriginalExtension();

            // Move to public/resume
            $file->move(public_path('resume'), $filename);

            $validated['resume'] = 'resume/' . $filename; 
        }

        // Save candidate
        $candidate = Candidate::create($validated);

        // Generate WhatsApp message with resume URL
        $resumeUrl = $validated['resume'] ?? 'Not uploaded';
        if($resumeUrl != 'Not uploaded'){
            $resumeUrl = url($resumeUrl); // full public URL
        }

        $whatsappMessage = urlencode(
            "*Candidate Application – Anantkamal*\n\n" .
            "• Full Name: {$candidate->fullname}\n" .
            "• Email: {$candidate->email}\n" .
            "• Phone: {$candidate->phone}\n" .
            "• Position: {$candidate->position}\n" .
            "• Experience: {$candidate->experience}\n" .
            "• Salary Expectation: {$candidate->salary}\n" .
            "• Resume: {$resumeUrl}\n" .
            "• Why Hire Me:\n{$candidate->message}"
        );

        $waUrl = "https://wa.me/917620237235?text={$whatsappMessage}";
        
        // Redirect to thank-you page that opens WhatsApp
        return view('frontend.candidate-thanks', compact('waUrl'));
    }
}
