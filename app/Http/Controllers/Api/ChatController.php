<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FieldValue;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{   
    protected $firestore;

    public function __construct()
    {
        putenv('GOOGLE_CLOUD_DISABLE_GRPC=true');

        $credentialsPath = 'C:\\laragon\\www\\product_app\\storage\\app\\firebase\\firebase-credentials.json';

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/cloud-platform',
            $credentialsPath
        );

        $this->firestore = new FirestoreClient([
            'keyFilePath' => 'C:\\laragon\\www\\product_app\\storage\\app\\firebase\\firebase-credentials.json',
            'projectId'   => 'shopcart-laravel01',
            'transport' => 'rest',
            'credentials' => $credentials,
        ]);
    }

    public function sendMessage(Request $request, $adminId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'media' => 'nullable|file',
            'sender' => 'required|string|in:user,admin',
        ]);

        $userId = Auth::id();
        $mediaUrl = null;
        $fileName = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');

            $path = $file->store('chat-media', 'public');

            $mediaUrl = asset('storage/' . $path);

            $fileName = basename($path);
        }

        $firestore = $this->firestore
            ->collection('messages')
            ->document('user_' . $userId);
        
        $firestore->set(['user_id' => $userId],['merge' => true]);
            
        $firestore->update([
            [
                'path' => 'chat', 
                'value' => FieldValue::arrayUnion([
                    [
                        'sender' => 'user',
                        'text' => $request->message,
                        'media_url' => $mediaUrl,
                        'file_name' => $fileName,
                        'created_at' => new Timestamp(new \DateTime()),
                        'status' =>'sent'
                    ]
                ])
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'user_id'    => $userId,
                'sender'     => 'user',
                'text'       => $request->message,
                'media_url'  => $mediaUrl,
                'created_at' => now()->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    public function viewMessage(Request $request)
    {
        $userId = Auth::id();
        $documents = $this->firestore
            ->collection('messages')
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'asc')
            ->documents();

        $docRef = $this->firestore
            ->collection('messages')
            ->document('user_' . $userId);

        $snapshot = $docRef->snapshot();

        $messages = [];
        if ($snapshot->exists() && isset($snapshot['chat'])) {
            foreach ($snapshot['chat'] as $data) {
                // $data = $document->data();
                $messages[] = [
                    // 'id' => $document->id(),
                    'sender' => $data['sender'] ?? null,
                    'message' => $data['message'] ?? null,
                    'media_url' => $data['media_url'] ?? null,
                    'status' => $data['status'] ?? null,
                    'created_at' => Carbon::parse($data['created_at'])->format('d M Y, h:i A'),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'count' => count($messages),
            'messages' => $messages,
        ]);
    }

    public function downloadPdf($file)
    {
        $path = 'public/chat-media/' . $file;

        if (!Storage::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        return Storage::download($path);
    }

    public function sendLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $userId = Auth::id();

        try {
            $docRef = $this->firestore->collection('messages')->document("user_{$userId}");
            $docRef->set(['user_id' => $userId], ['merge' => true]);
            $docRef->update([
                [
                    'path' => 'chat',
                    'value' => FieldValue::arrayUnion([
                        [
                            'sender'     => 'user',
                            'type'       => 'location',
                            'latitude'   => $request->latitude,
                            'longitude'  => $request->longitude,
                            'created_at' => new Timestamp(new \DateTime()),
                            'status'     => 'sent'
                        ]
                    ])
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location sent successfully',
                'data' => [
                    'user_id'   => $userId,
                    'sender'    => 'user',
                    'type'      => 'location',
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'created_at'=> now()->format('Y-m-d H:i:s'),
                    'status'    => 'sent'
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}