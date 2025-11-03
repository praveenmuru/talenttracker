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

        if ($request->has('opening_id') && $request->opening_id != '') {
            $query->where('opening_id', $request->opening_id);
        }

        $candidates = $query->with('opening')->latest()->paginate(10);
        $openings = Opening::all();

        return view('candidates.index', compact('candidates', 'openings'));
    }

    public function create()
    {
        $openings = Opening::all();
        // Get the parsed data from the session, if it exists
        $candidate = (object) session('parsedData', []);
        return view('candidates.create', compact('openings', 'candidate'));
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
// public function parseResume(Request $request)
//     {
//         Log::info('Starting resume parsing process');

//         $request->validate([
//             'resume_file' => 'required|mimes:pdf,doc,docx|max:2048'
//         ]);
//         Log::info('File validation passed');

//         $file = $request->file('resume_file');
//         $originalName = $file->getClientOriginalName();
//         $resumePath = null; // Initialize to null

//         try {
//             // 1. Save the file to its permanent path first.
//             $resumePath = $file->store('resumes', 'public');
//             $fullPath = storage_path('app/public/' . $resumePath);
//             Log::info('File stored successfully', ['path' => $fullPath]);

//             // 2. Load the API key from config.
            
//             // --- SECURITY WARNING: Move this to your .env file ---
//             // $apiKey = config('sharpapi-resume-parser.api_key');
//             $apiKey = "5r1QHWTeWdLCSIED8FJClWxaM0Rf1a4B1zFmjcQi";
//             if (empty($apiKey)) {
//                 throw new \Exception('SHARP_API_KEY is not set in .env or config/sharpapi-resume-parser.php');
//             }
            
//             // 3. Initialize the service.
//             Log::info('Initializing parser service');
//             $parserService = new ResumeParserService();

//             Log::info('Sending file for parsing');
//             $statusUrl = $parserService->parseResume($fullPath, 'English', $originalName);
//             Log::info('Received status URL', ['url' => $statusUrl]);

//             Log::info('Starting to poll for results...');
            
//             $jobStatus = '';
//             $parsedJson = null;
//             $startTime = time();

//             // 4. Poll for up to 60 seconds
//             while (time() - $startTime < 60) {
//                 Log::info('Polling status URL...');

//                 $statusResponse = Http::withHeaders([
//                     'Authorization' => 'Bearer ' . $apiKey,
//                     'Accept' => 'application/json',
//                 ])
//                 ->withoutVerifying() // <-- Keep this for your local XAMPP
//                 ->get($statusUrl);

//                 Log::info('Got status response');

//                 if ($statusResponse->failed()) {
//                     Log::error('Failed to poll status URL', ['response_body' => $statusResponse->body()]);
//                     throw new \Exception('Failed to poll status URL: ' . $statusResponse->status());
//                 }

//                 $statusData = $statusResponse->json();
                
//                 // Check for auth error first
//                 if (isset($statusData['error']['message']) && $statusData['error']['message'] === 'Unauthorized') {
//                     Log::error('Authorization failed during polling.', ['url' => $statusUrl]);
//                     throw new \Exception('API authentication failed (Unauthorized). Check your API key.');
//                 }

//                 $jobStatus = $statusData['data']['attributes']['status'] ?? 'failed';

//                 if ($jobStatus === 'success') {
//                     Log::info('Job completed, results received.');
//                     $parsedJson = $statusData['data']['attributes']['result']; 
//                     Log::info('Parsed JSON data', ['data' => $parsedJson]);
//                     break; // Exit the loop
//                 }

//                 if ($jobStatus === 'failed') {
//                     $apiError = $statusData['data']['attributes']['error'] ?? 'Unknown error';
//                     $apiErrorText = is_array($apiError) ? json_encode($apiError) : (string) $apiError;
                    
//                     Log::error('SharpAPI job failed', ['api_error' => $apiError, 'response_body' => $statusData]);
//                     throw new \Exception('API job failed: ' . $apiErrorText);
//                 }

//                 // Job is 'new' or 'processing', so we continue
//                 Log::info("Job is still $jobStatus, waiting 3 seconds...");
//                 sleep(3);
//             }

//             // --- FIX 1: Check for 'success' (not 'completed') ---
//             if ($jobStatus !== 'success' || is_null($parsedJson)) {
//                 throw new \Exception('Job timed out after 60 seconds.');
//             }

//             // 5. Map the results
//             Log::info('Received parsed data, mapping to fields...');
            
//             $nameParts = explode(' ', $parsedJson['candidate_name'] ?? '', 2);

//             $parsedData['first_name'] = $nameParts[0] ?? '';
//             $parsedData['last_name'] = $nameParts[1] ?? '';
//             $parsedData['email'] = $parsedJson['candidate_email'] ?? '';
//             $parsedData['phone'] = $parsedJson['candidate_phone'] ?? '';
            
//             // --- FIX 2: Added correct skill mapping logic ---
//             $allSkills = [];
//             if (!empty($parsedJson['positions']) && is_array($parsedJson['positions'])) {
//                 foreach ($parsedJson['positions'] as $position) {
//                     if (!empty($position['skills']) && is_array($position['skills'])) {
//                         $allSkills = array_merge($allSkills, $position['skills']);
//                     }
//                 }
//             }
//             $parsedData['skills'] = implode(', ', array_unique($allSkills));
//             // --- End of skill mapping ---

//             $parsedData['resume_path'] = $resumePath; // The path we saved earlier
//             $parsedData['status'] = 'New';
            
//             Log::info('Data mapping complete', ['mappedData' => $parsedData]);

//             // 6. Redirect with data
//             Log::info('Redirecting to create form with parsed data');
//             return redirect()->route('candidates.create')
//                             ->with('parsedData', $parsedData)
//                             ->with('success', 'Resume parsed! Please review and save.');

//         } catch (\Exception $e) {
//             // 7. Handle all errors
//             Log::error('Resume parsing failed', [
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);
            
//             // If API fails, delete the stored file and show an error
//             if ($resumePath) { // Only delete if it was successfully stored
//                 Storage::disk('public')->delete($resumePath);
//                 Log::info('Cleaned up stored resume file.', ['path' => $resumePath]);
//             }
            
//             return redirect()->route('candidates.index')
//                             ->with('parse_error', 'Could not parse resume: ' . $e->getMessage());
//         }
//     }

    // public function parseResumeTemp(Request $request)
    // {
    //     Log::info('Starting resume parsing process (TEST MODE)');

    //     $request->validate([
    //         'resume_file' => 'required|mimes:pdf,doc,docx|max:2048'
    //     ]);
    //     Log::info('File validation passed');

    //     $file = $request->file('resume_file');
    //     $resumePath = null; // Initialize to null

    //     try {
    //         // 1. We still save the file, because the path is needed for the form
    //         $resumePath = $file->store('resumes', 'public');
    //         $fullPath = storage_path('app/public/' . $resumePath);
    //         Log::info('File stored successfully (TEST MODE)', ['path' => $fullPath]);


    //         // 2. --- MOCK DATA BLOCK ---
    //         // All API logic is commented out.
    //         Log::info('--- USING MOCK DATA (TESTING) ---');
    //         $mockJsonString = '{
    //             "candidate_name": "Rathish Kuppusamy",
    //             "candidate_email": "rathish.k1995@gmail.com",
    //             "candidate_phone": "7373891901",
    //             "candidate_address": "Mugalivakkam, 600125 Chennai",
    //             "candidate_language": "",
    //             "candidate_spoken_languages": [],
    //             "candidate_honors_and_awards": [],
    //             "candidate_courses_and_certifications": [
    //                 "ISTQB Certified Tester",
    //                 "Guidewire Policy Center Certification Intermediate",
    //                 "Business English Certification - Preliminary"
    //             ],
    //             "positions": [
    //                 {
    //                     "position_name": "Consultant",
    //                     "company_name": "Capgemini Technology Services India",
    //                     "country": "India",
    //                     "start_date": "2021-12-01",
    //                     "end_date": null,
    //                     "skills": [
    //                         "Test strategies", "Functional testing", "System testing",
    //                         "Regression testing", "Guidewire Platform", "Selenium",
    //                         "BDD frameworks", "End-to-End testing", "Integration testing",
    //                         "XML comparison", "Agile", "Guidewire Policy Center", "Guidewire Claim Center"
    //                     ],
    //                     "job_details": "Developed and implemented comprehensive test strategies..."
    //                 },
    //                 {
    //                     "position_name": "Associate",
    //                     "company_name": "Cognizant Technology Solutions India",
    //                     "country": "India",
    //                     "start_date": "2016-10-01",
    //                     "end_date": "2021-12-01",
    //                     "skills": [],
    //                     "job_details": "Client: Leading insurance Provider in US"
    //                 }
    //             ],
    //             "education_qualifications": [
    //                 {
    //                     "school_name": "Karpagam College of Engineering",
    //                     "school_type": "College or equivalent",
    //                     "degree_type": "Bachelor\u2019s Degree or equivalent",
    //                     "faculty_department": "",
    //                     "specialization_subjects": "Information Technology",
    //                     "country": "India",
    //                     "start_date": "2012-08-01",
    //                     "end_date": "2016-05-01",
    //                     "learning_mode": "In-person learning",
    //                     "education_details": ""
    //                 }
    //             ]
    //         }';
            
    //         // Decode the string into an associative array (true)
    //         $parsedJson = json_decode($mockJsonString, true); 
    //         Log::info('--- MOCK DATA LOADED ---');
    //         // --- END OF MOCK DATA BLOCK ---


    //         /*
    //         // --- ALL REAL API LOGIC IS COMMENTED OUT ---
            
    //         $apiKey = config('sharpapi-resume-parser.api_key'); 
    //         if (empty($apiKey)) {
    //             throw new \Exception('SHARP_API_KEY is not set in .env or config/sharpapi-resume-parser.php');
    //         }
    //         Log::info('Initializing parser service');
    //         $parserService = new ResumeParserService();

    //         Log::info('Sending file for parsing');
    //         $statusUrl = $parserService->parseResume($fullPath, 'English', $originalName); 
    //         Log::info('Received status URL', ['url' => $statusUrl]);
    //         Log::info('Starting to poll for results...');
    //         $jobStatus = '';
    //         $parsedJson = null;
    //         $startTime = time();
    //         while (time() - $startTime < 60) {
    //             // ... polling logic ...
    //         }
    //         if ($jobStatus !== 'success' || is_null($parsedJson)) {
    //             throw new \Exception('Job timed out or failed.');
    //         }
            
    //         // --- END OF COMMENTED OUT API LOGIC ---
    //         */


    //         // 5. Map the results (This is what you are testing)
    //         Log::info('Received parsed data, mapping to fields...');
            
    //         // Split the full name into first and last
    //         $nameParts = explode(' ', $parsedJson['candidate_name'] ?? '', 2);
    //         $parsedData['first_name'] = $nameParts[0] ?? '';
    //         $parsedData['last_name'] = $nameParts[1] ?? '';

    //         // Map direct fields
    //         $parsedData['email'] = $parsedJson['candidate_email'] ?? '';
    //         $parsedData['phone'] = $parsedJson['candidate_phone'] ?? '';

    //         // Collect all skills from all positions
    //         $allSkills = [];
    //         if (!empty($parsedJson['positions']) && is_array($parsedJson['positions'])) {
    //             foreach ($parsedJson['positions'] as $position) {
    //                 if (!empty($position['skills']) && is_array($position['skills'])) {
    //                     $allSkills = array_merge($allSkills, $position['skills']);
    //                 }
    //             }
    //         }
            
    //         // Join unique skills with a comma
    //         $parsedData['skills'] = implode(', ', array_unique($allSkills));
            
    //         // Add the fields we already know
    //         $parsedData['resume_path'] = $resumePath; // The path we saved earlier
    //         $parsedData['status'] = 'New';
            
    //         Log::info('Data mapping complete (TEST MODE)', ['mappedData' => $parsedData]);

    //         // 6. Redirect with data
    //         Log::info('Redirecting to create form with parsed data (TEST MODE)');
    //         return redirect()->route('candidates.create')
    //                         ->with('parsedData', $parsedData)
    //                         ->with('success', 'Resume parsed! Please review and save.');

    //     } catch (\Exception $e) {
    //         // 7. Handle all errors
    //         Log::error('Resume parsing failed (TEST MODE)', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         if ($resumePath) { 
    //             Storage::disk('public')->delete($resumePath);
    //             Log::info('Cleaned up stored resume file.', ['path' => $resumePath]);
    //         }
            
    //         return redirect()->route('candidates.index')
    //                         ->with('parse_error', 'Could not parse resume: ' . $e->getMessage());
    //     }
    // }


    public function parseResume(Request $request)
    {
        Log::info('Starting resume parsing process');

        $request->validate([
            'resume_file' => 'required|mimes:pdf,doc,docx|max:2048'
        ]);
        Log::info('File validation passed');

        $file = $request->file('resume_file');
        $originalName = $file->getClientOriginalName();
        $resumePath = null; // Initialize to null

        try {
            // 1. Save the file to its permanent path first.
            $resumePath = $file->store('resumes', 'public');
            $fullPath = storage_path('app/public/' . $resumePath);
            Log::info('File stored successfully', ['path' => $fullPath]);

            // 2. Load the API key.
            // --- SECURITY WARNING: Move this key to your .env file ---
            // $apiKey = config('sharpapi-resume-parser.api_key'); 
            $apiKey = "5r1QHWTeWdLCSIED8FJClWxaM0Rf1a4B1zFmjcQi";
            
            if (empty($apiKey)) {
                throw new \Exception('SHARP_API_KEY is not set in .env or config/sharpapi-resume-parser.php');
            }
            
            // 3. Initialize the service.
            Log::info('Initializing parser service');
            $parserService = new ResumeParserService();

            Log::info('Sending file for parsing');
            $statusUrl = $parserService->parseResume($fullPath, 'English', $originalName);
            Log::info('Received status URL', ['url' => $statusUrl]);

            Log::info('Starting to poll for results...');
            
            $jobStatus = '';
            $parsedJson = null; // This will hold the result from data.attributes.result
            $startTime = time();

            // 4. Poll for up to 60 seconds
            while (time() - $startTime < 60) {
                Log::info('Polling status URL...');

                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey, // Fixes "Unauthorized"
                    'Accept' => 'application/json',
                ])
                ->withoutVerifying() // Fixes local XAMPP SSL issue
                ->get($statusUrl);

                Log::info('Got status response');

                if ($statusResponse->failed()) {
                    Log::error('Failed to poll status URL', ['response_body' => $statusResponse->body()]);
                    throw new \Exception('Failed to poll status URL: ' . $statusResponse->status());
                }

                $statusData = $statusResponse->json();
                
                if (isset($statusData['error']['message']) && $statusData['error']['message'] === 'Unauthorized') {
                    Log::error('Authorization failed during polling.', ['url' => $statusUrl]);
                    throw new \Exception('API authentication failed (Unauthorized). Check your API key.');
                }

                // Reads status from: data.attributes.status
                $jobStatus = $statusData['data']['attributes']['status'] ?? 'failed';

                if ($jobStatus === 'success') {
                    Log::info('Job completed, results received.');
                    // Reads result from: data.attributes.result
                    $parsedJson = $statusData['data']['attributes']['result']; 
                    Log::info('Parsed JSON data wrapper received', ['data' => $parsedJson]);
                    break; // Exit the loop
                }

                if ($jobStatus === 'failed') {
                    $apiError = $statusData['data']['attributes']['error'] ?? 'Unknown error';
                    $apiErrorText = is_array($apiError) ? json_encode($apiError) : (string) $apiError;
                    
                    Log::error('SharpAPI job failed', ['api_error' => $apiError, 'response_body' => $statusData]);
                    throw new \Exception('API job failed: ' . $apiErrorText);
                }

                Log::info("Job is still $jobStatus, waiting 3 seconds...");
                sleep(3);
            }

            // Check for timeout
            if ($jobStatus !== 'success' || is_null($parsedJson)) {
                throw new \Exception('Job timed out after 60 seconds.');
            }

            // 5. Map the results
            Log::info('Received parsed data, mapping to fields...');

            // --- FIX FOR DOUBLE-ENCODED JSON ---
            if (!isset($parsedJson['data']) || !is_string($parsedJson['data'])) {
                Log::error('Unexpected JSON format from API', ['raw_result' => $parsedJson]);
                throw new \Exception('Parsed JSON is in an unexpected format. Missing "data" key or it is not a string.');
            }

            // Decode the inner JSON string (the one that starts with "{"candidate_name":...")
            $resumeData = json_decode($parsedJson['data'], true);

            if (is_null($resumeData)) {
                throw new \Exception('Failed to decode the inner JSON data string.');
            }
            // --- END OF DOUBLE-ENCODING FIX ---
            

            // NOW, we map using the decoded $resumeData variable
            $nameParts = explode(' ', $resumeData['candidate_name'] ?? '', 2);

            $parsedData['first_name'] = $nameParts[0] ?? '';
            $parsedData['last_name'] = $nameParts[1] ?? '';
            $parsedData['email'] = $resumeData['candidate_email'] ?? '';
            $parsedData['phone'] = $resumeData['candidate_phone'] ?? '';
            
            // Collect all skills from all positions
            $allSkills = [];
            if (!empty($resumeData['positions']) && is_array($resumeData['positions'])) {
                foreach ($resumeData['positions'] as $position) {
                    if (!empty($position['skills']) && is_array($position['skills'])) {
                        $allSkills = array_merge($allSkills, $position['skills']);
                    }
                }
            }
            $parsedData['skills'] = implode(', ', array_unique($allSkills));

            $parsedData['resume_path'] = $resumePath; // The path we saved earlier
            $parsedData['status'] = 'New';
            
            Log::info('Data mapping complete', ['mappedData' => $parsedData]);

            // 6. Redirect with data
            Log::info('Redirecting to create form with parsed data');
            return redirect()->route('candidates.create')
                            ->with('parsedData', $parsedData)
                            ->with('success', 'Resume parsed! Please review and save.');

        } catch (\Exception $e) {
            // 7. Handle all errors
            Log::error('Resume parsing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If API fails, delete the stored file and show an error
            if ($resumePath) { // Only delete if it was successfully stored
                Storage::disk('public')->delete($resumePath);
                Log::info('Cleaned up stored resume file.', ['path' => $resumePath]);
            }
            
            return redirect()->route('candidates.index')
                            ->with('parse_error', 'Could not parse resume: ' . $e->getMessage());
        }
    }

}