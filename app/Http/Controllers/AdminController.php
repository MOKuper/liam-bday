<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\Message;
use App\Models\PartyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Statement;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_guests' => Guest::count(),
            'confirmed_rsvps' => Rsvp::where('status', 'confirmed')->count(),
            'declined_rsvps' => Rsvp::where('status', 'declined')->count(),
            'pending_rsvps' => Rsvp::where('status', 'pending')->count(),
            'total_attendees' => Rsvp::where('status', 'confirmed')->sum('adults_attending') + 
                                Rsvp::where('status', 'confirmed')->sum('children_attending'),
            'total_messages' => Message::count(),
            'unapproved_messages' => Message::where('is_approved', false)->count(),
        ];
        
        $partyDetails = PartyDetail::first();
        
        return view('admin.dashboard', compact('stats', 'partyDetails'));
    }
    
    public function guests()
    {
        $guests = Guest::with('rsvp')->latest()->get();
        return view('admin.guests', compact('guests'));
    }
    
    public function storeGuest(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'preferred_language' => 'nullable|in:en,nl',
            'is_child' => 'boolean',
            'parent_name' => 'nullable|string|max:255',
            'parent_email' => 'nullable|email|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_name_nl' => 'nullable|string|max:255',
            'parent_email_nl' => 'nullable|email|max:255',
            'parent_phone_nl' => 'nullable|string|max:20',
            'expected_attendees' => 'nullable|integer|min:1|max:10',
            'friendship_photo' => 'nullable|image|max:5120', // 5MB max
        ]);
        
        // Handle friendship photo upload
        if ($request->hasFile('friendship_photo')) {
            $validated['friendship_photo_path'] = $request->file('friendship_photo')->store('friendship-photos', 'public');
        }
        
        // Remove the file from validated array since it's not a database field
        unset($validated['friendship_photo']);
        
        $guest = Guest::create($validated);
        
        // Generate QR code for the new guest
        $guest->generateQrCode();
        
        return back()->with('success', 'Guest added successfully!');
    }
    
    public function rsvps()
    {
        $rsvps = Rsvp::with('guest')->latest('responded_at')->get();
        
        $stats = [
            'confirmed' => $rsvps->where('status', 'confirmed')->count(),
            'declined' => $rsvps->where('status', 'declined')->count(),
            'pending' => Guest::doesntHave('rsvp')->count(),
            'adults_total' => $rsvps->where('status', 'confirmed')->sum('adults_attending'),
            'children_total' => $rsvps->where('status', 'confirmed')->sum('children_attending'),
        ];
        
        return view('admin.rsvps', compact('rsvps', 'stats'));
    }
    
    public function messages()
    {
        $messages = Message::with('guest')->latest()->get();
        return view('admin.messages', compact('messages'));
    }
    
    public function approveMessage(Request $request, Message $message)
    {
        $message->update(['is_approved' => !$message->is_approved]);
        
        return back()->with('success', 'Message status updated!');
    }
    
    public function partyDetails()
    {
        $partyDetails = PartyDetail::first();
        return view('admin.party-details', compact('partyDetails'));
    }
    
    public function updatePartyDetails(Request $request)
    {
        $validated = $request->validate([
            'child_name' => 'required|string|max:255',
            'child_age' => 'required|integer|min:1|max:20',
            'party_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string',
            'venue_map_url' => 'nullable|url',
            'parking_info' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'activities' => 'nullable|string',
            'parent_contact_info' => 'required|string',
            'gift_suggestions' => 'nullable|string',
        ]);
        
        $partyDetails = PartyDetail::first();
        $partyDetails->update($validated);
        
        return back()->with('success', 'Party details updated successfully!');
    }

    public function generateQrCode(Guest $guest)
    {
        $guest->generateQrCode();
        return back()->with('success', 'QR code generated for ' . $guest->name);
    }

    public function generateAllQrCodes()
    {
        $guests = Guest::all();
        $count = 0;
        
        foreach ($guests as $guest) {
            $guest->generateQrCode();
            $count++;
        }
        
        return back()->with('success', "QR codes generated for {$count} guests");
    }

    public function importGuests(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();
            
            // Create a CSV reader
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); // First row contains headers
            
            $records = $csv->getRecords();
            $imported = 0;
            $errors = [];
            
            foreach ($records as $offset => $record) {
                try {
                    // Clean up data
                    $data = [
                        'name' => trim($record['name'] ?? ''),
                        'email' => trim($record['email'] ?? '') ?: null,
                        'phone' => trim($record['phone'] ?? '') ?: null,
                        'date_of_birth' => $this->parseDate($record['date_of_birth'] ?? ''),
                        'preferred_language' => in_array($record['preferred_language'] ?? '', ['en', 'nl']) 
                            ? $record['preferred_language'] 
                            : 'en',
                        'expected_attendees' => 1,
                        'is_child' => true, // Default to child for birthday party
                    ];
                    
                    // Skip if name is empty
                    if (empty($data['name'])) {
                        $errors[] = "Row " . ($offset + 2) . ": Name is required";
                        continue;
                    }
                    
                    // Check if guest already exists
                    $existingGuest = Guest::where('name', $data['name'])->first();
                    if ($existingGuest) {
                        $errors[] = "Row " . ($offset + 2) . ": Guest '{$data['name']}' already exists";
                        continue;
                    }
                    
                    // Create guest
                    $guest = Guest::create($data);
                    
                    // Generate QR code
                    $guest->generateQrCode();
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($offset + 2) . ": " . $e->getMessage();
                }
            }
            
            // Build response message
            $message = "Successfully imported {$imported} guests.";
            if (count($errors) > 0) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " and " . (count($errors) - 3) . " more...";
                }
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing CSV file: ' . $e->getMessage());
        }
    }
    
    private function parseBool($value)
    {
        return in_array(strtolower(trim($value)), ['true', '1', 'yes', 'y'], true);
    }

    private function parseDate($value)
    {
        $value = trim($value);
        if (empty($value)) {
            return null;
        }
        
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="guest-import-sample.csv"',
        ];
        
        $columns = [
            'name',
            'email',
            'phone',
            'date_of_birth',
            'preferred_language'
        ];
        
        $sampleData = [
            ['Emma Thompson', 'emma@email.com', '0612345678', '2019-03-15', 'en'],
            ['Max Verstappen', '', '0698765432', '2018-12-22', 'nl'],
            ['Sophie de Vries', 'sophie@email.nl', '', '2019-07-08', 'nl'],
            ['Lucas Johnson', 'parent@email.com', '0687654321', '2020-01-10', 'en'],
        ];
        
        $callback = function() use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write sample data
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportGuests()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="guests-export-' . date('Y-m-d') . '.csv"',
        ];
        
        $guests = Guest::with('rsvp')->get();
        
        $columns = [
            'name',
            'email',
            'phone',
            'date_of_birth',
            'age',
            'preferred_language',
            'unique_url',
            'rsvp_status',
            'adults_attending',
            'children_attending'
        ];
        
        $callback = function() use ($columns, $guests) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write guest data
            foreach ($guests as $guest) {
                $row = [
                    $guest->name,
                    $guest->email ?? '',
                    $guest->phone ?? '',
                    $guest->getFormattedDateOfBirth() ?? '',
                    $guest->getAge() ?? '',
                    $guest->preferred_language ?? 'en',
                    url('/invite/' . $guest->unique_url),
                    $guest->rsvp ? $guest->rsvp->status : 'no_response',
                    $guest->rsvp ? $guest->rsvp->adults_attending : '',
                    $guest->rsvp ? $guest->rsvp->children_attending : ''
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
