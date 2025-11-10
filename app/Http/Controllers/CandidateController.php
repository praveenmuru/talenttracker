<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Opening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Changed from 'use Log;' to the full facade
use SharpAPI\ResumeParser\ResumeParserService;


class CandidateController extends Controller
{
    protected ResumeParserService $resumeParserService;

    public function __construct(ResumeParserService $resumeParserService)
    {
        $this->resumeParserService = $resumeParserService;
    }

    
    public function index(Request $request)
    {
        $query = Candidate::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('job_id') && $request->job_id != '') {
            $query->where('job_id', $request->job_id);
        }

        $candidates = $query->with('opening')->latest()->paginate(10);
        $openings = Opening::all();

        return view('candidates.index', compact('candidates', 'openings'));
    }

public function create()
    {
        // 1. Create a new, empty Candidate model
        $candidate = new \App\Models\Candidate();
        
        // 2. Check for parsed data from the session and fill the model
        if (session('parsedData')) {
            $candidate->fill(session('parsedData'));
        } else {
            // 3. Set defaults for a blank form
            $candidate->status = 'New';
        }

        // 4. Get openings
        $openings = \App\Models\Opening::all();
        
        // 5. Pass the (now populated) $candidate model to the view
        return view('candidates.create', compact('candidate', 'openings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:candidates,email',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = $request->all();

        // --- FIX: Removed duplicate logic ---
        if ($request->hasFile('resume')) {
            // Handle manual upload on the form
            $data['resume_path'] = $request->file('resume')->store('resumes', 'public');
        } elseif ($request->filled('resume_path')) {
            // Keep the path that was set by the parser
            $data['resume_path'] = $request->resume_path;
        }
        // --- END FIX ---

        Candidate::create($data);

        return redirect()->route('candidates.index')->with('success', 'Candidate added successfully.');
    }

    public function edit(Candidate $candidate)
    {
        $openings = Opening::all();
        return view('candidates.edit', compact('candidate', 'openings'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:candidates,email,' . $candidate->id,
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048', // Added resume validation
        ]);

        $data = $request->all();

        if ($request->hasFile('resume')) {
            if ($candidate->resume_path) {
                Storage::disk('public')->delete($candidate->resume_path);
            }
            $data['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $candidate->update($data);

        return redirect()->route('candidates.index')->with('success', 'Candidate updated successfully.');
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }
        $candidate->delete();

        return redirect()->route('candidates.index')->with('success', 'Candidate deleted.');
    }

    public function show(Candidate $candidate)
    {
        // Redirect to the edit page, since we don't have a separate "show" view
        return redirect()->route('candidates.edit', $candidate);
    }

    protected function validateAndStoreFile(Request $request)
    {
        $request->validate(['resume_file' => 'required|mimes:pdf,doc,docx|max:2048']);
        $file = $request->file('resume_file');
        $resumePath = $file->store('resumes', 'public');
        
        return [
            'path' => $resumePath,
            'fullPath' => storage_path('app/public/' . $resumePath),
            'originalName' => $file->getClientOriginalName()
        ];
    }

    protected function initializeParser()
    {
        $apiKey = "5r1QHWTeWdLCSIED8FJClWxaM0Rf1a4B1zFmjcQi";
        // $apiKey = env('SHARP_API_KEY');

            Log::info('sharp api key', ['api key' => $apiKey]);
            // exit;
        if (empty($apiKey)) {
            throw new \Exception('SHARP_API_KEY is not set');
        }
        return new ResumeParserService();
    }

    protected function pollForResults($statusUrl, $apiKey)
    {

        print("in poll rsult exit");
        
        $startTime = time();
        while (time() - $startTime < 60) {
            $statusResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->timeout(10)->withoutVerifying()->get($statusUrl);

            if ($statusResponse->failed()) {
                print("in failed exit");
                // exit;
                // throw new \Exception('Failed to poll status URL: ' . $statusResponse->status());
            }

            $statusData = $statusResponse->json();
            $jobStatus = $statusData['data']['attributes']['status'] ?? 'failed';

            if ($jobStatus === 'success') {
                print("in success exit");
                // exit;
                // var_dump($statusData);
                // exit;
                return $statusData['data']['attributes']['result'];
            }

            if ($jobStatus === 'failed') {
                print("in failed2 exit");
                // exit;
                $apiError = $statusData['data']['attributes']['error'] ?? 'Unknown error';
                throw new \Exception('API job failed: ' . $apiError);
            }

            sleep(3);
        }
        throw new \Exception('Job timed out after 115 seconds.');
    }

    protected function mapResumeData($resumeData, $resumePath)
    {
        $nameParts = explode(' ', $resumeData['candidate_name'] ?? '', 2);
        $allSkills = [];

        if (!empty($resumeData['positions']) && is_array($resumeData['positions'])) {
            foreach ($resumeData['positions'] as $position) {
                if (!empty($position['skills']) && is_array($position['skills'])) {
                    $allSkills = array_merge($allSkills, $position['skills']);
                }
            }
        }

        return [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'email' => $resumeData['candidate_email'] ?? '',
            'phone' => $resumeData['candidate_phone'] ?? '',
            'skills' => implode(', ', array_unique($allSkills)),
            'resume_path' => $resumePath,
            'status' => 'New'
        ];
    }

    public function parseResume(Request $request)
    {
        $request->validate([
            'resume_file' => 'required|file|mimes:pdf,docx', // This is the file from your HTML form
        ]);

        $file = $request->file('resume_file');

        // --- Handle DOCX to PDF Conversion First ---
        // (Add your LibreOffice or DomPDF conversion logic here)
        // For this example, we'll assume it's already a PDF or converted.
        // $pdfPath = 'path/to/your/converted.pdf';
        
        // We'll use the original file path for this example
        $filePath = $file->path();
        $fileName = $file->getClientOriginalName();


        // --- Send to Open Resume API ---
        try {
            $response = Http::timeout(60) // Increase timeout for parsing
                ->attach(
                    'resume', // This is the required API field name
                    file_get_contents($filePath),
                    $fileName
                )
                ->post('http://localhost:3000/api/parse');

            // --- Handle the Response ---
            if ($response->successful()) {
                // Success! $parsedData is an array.
                $parsedData = $response->json();

                // Log the successful result to see the structure
                Log::info('Resume parsed successfully: ', $parsedData);

                // Now you can use the data:
                // $name = $parsedData['name'];
                // $email = $parsedData['email'];
                // $skills = $parsedData['skills']; // This will be an array

                return back()->with('success', 'Resume parsed!')
                             ->with('data', $parsedData);

            } else {
                // The API returned an error (e.g., 400, 500)
                Log::error('Open Resume API Error: ' . $response->body());
                return back()->with('error', 'The parser service failed.');
            }

        } catch (\Exception $e) {
            // The request itself failed (e.g., connection refused)
            // This is the most common error.
            Log::error('Could not connect to Open Resume API: ' . $e->getMessage());
            return back()->with('error', 'Could not connect to parser. Is the Docker container running?');
        }



    }
}