<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Auth;

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
    
    public function replyToUser(Request $request, $userId)
    {
        $message = $request->input('message');

        // Save reply
        $docRef = $this->firestore->collection('messages')->document("user_{$userId}");
        $docRef->collection('chats')->add([
            'from' => 'admin',
            'to' => $userId,
            'text' => $message,
            'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
            'status' => 'sent'
        ]);

        // Fetch user presence
        $snapshot = $docRef->snapshot();
        $userStatus = $snapshot['user_status'] ?? 'offline';
        $userLastSeen = $snapshot['user_last_seen'] ?? null;

        return response()->json([
            'success' => true,
            'message' => "Reply sent to user {$userId}",
            'user_status' => [
                'status' => $userStatus,
                'last_seen' => ($userStatus === 'online') ? null : $userLastSeen
            ]
        ]);
    }
}
