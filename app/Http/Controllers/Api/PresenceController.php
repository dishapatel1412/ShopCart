<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\Firestore\FirestoreClient;

class PresenceController extends Controller
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
            'keyFilePath' => base_path('storage/app/firebase/firebase-credentials.json'),
            'projectId'   => 'shopcart-laravel01',
            'transport' => 'rest',
            'credentials' => $credentials,
        ]);
    }

    public function sendToAdmin(Request $request)
    {
        $fromUserId = $request->input('from_user_id');
        $message = $request->input('message');

        // Save message
        $docRef = $this->firestore->collection('messages')->document("user_{$fromUserId}");
        $docRef->collection('chats')->add([
            'from' => $fromUserId,
            'to' => 'admin',
            'text' => $message,
            'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
            'status' => 'sent'
        ]);

        // Fetch admin presence
        $snapshot = $docRef->snapshot();
        $adminStatus = $snapshot['admin_status'] ?? 'offline';
        $adminLastSeen = $snapshot['admin_last_seen'] ?? null;

        return response()->json([
            'success' => true,
            'message' => 'Message sent to admin',
            'admin_status' => [
                'status' => $adminStatus,
                'last_seen' => ($adminStatus === 'online') ? null : $adminLastSeen
            ]
        ]);
    }
}
