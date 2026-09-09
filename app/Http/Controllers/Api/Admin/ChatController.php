<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FieldValue;

class ChatController extends Controller
{
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

    public function adminReply(Request $request, $userId)
    {
        $adminId = Auth::id();
        $request->validate([
            'message' => 'nullable|string',
            'media'   => 'nullable|file',
        ]);

        $mediaUrl = null;
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('chat-media', 'public');
            $mediaUrl = asset('storage/' . $path);
        }

        $firestore = $this->firestore
            ->collection('messages')
            ->document('user_' . $userId);
        
        $firestore->set(['user_id' => $userId], ['merge' => true]);
        
        $messageId = uniqid('msg_', true);

        $firestore->update([
            [
                'path' => 'chat', 
                'value' => FieldValue::arrayUnion([
                    [
                        'id' => $messageId,
                        'sender' => 'admin',
                        'text' => $request->message,
                        'media_url' => $mediaUrl,
                        'status' => 'sent',
                        'created_at' => new Timestamp(new \DateTime())
                    ]
                ]) 
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => [
                'id' => $messageId,
                'user_id'    => $userId,
                'admin_id' => $adminId,
                'sender' => 'admin',
                'text' => $request->message,
                'media_url' => $mediaUrl,
                'status' => $request->status,
                'created_at' => now()->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    public function adminViewAll()
    {
        $request->validate([
            'message_id' => 'required|string',
            'status'     => 'required|string|in:sent,delivered,seen',
        ]);

        $docRef = $this->firestore->collection('messages')->document('user_' . $userId);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            return response()->json(['error' => 'User chat not found'], 404);
        }

        $chat = $snapshot->data()['chat'] ?? [];

        foreach ($chat as &$msg) {
            if (isset($msg['id']) && $msg['id'] === $request->message_id) {
                $msg['status'] = $request->status;
                $msg['updated_at'] = now()->toDateTimeString();
            }
        }

        $docRef->set(['chat' => $chat], ['merge' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => [
                'user_id' => $userId,
                'message_id' => $request->message_id,
                'status' => $request->status,
            ],
        ]);
    }

    public function updateStatus(Request $request, $userId)
    {
        $request->validate([
            'message_id' => 'required|string',
            'status'     => 'required|string|in:sent,delivered,seen',
        ]);

        $docRef = $this->firestore->collection('messages')->document('user_' . $userId);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            return response()->json(['error' => 'User chat not found'], 404);
        }

        $chat = $snapshot->data()['chat'] ?? [];

        foreach ($chat as &$msg) {
            if (isset($msg['id']) && $msg['id'] === $request->message_id) {
                $msg['status'] = $request->status;
                $msg['updated_at'] = now()->toDateTimeString();
            }
        }

        $docRef->set(['chat' => $chat], ['merge' => true]);

        // $firestore->update([
        //     [
        //         'path' => 'chat',
        //         'value' => FieldValue::arrayUnion([
        //             [
        //                 'id' => $request->message_id,
        //                 'status' => $request->status,
        //                 'updated_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
        //             ]
        //         ])
        //     ]
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => [
                'user_id' => $userId,
                'message_id' => $request->message_id,
                'status' 
                    => $request->status,
            ],
        ], 200);
    }

    public function replyToUser(Request $request, $userId)
    {
        $message = $request->input('message');

        // Save reply
        $docRef = $this->firestore->collection('messages')->document("user_{$userId}");
        $docRef->set([
            'messages' => FieldValue::arrayUnion([
                [
                    'from' => 'admin',
                    'to' => $userId,
                    'text' => $message,
                    'created_at' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                    'status' => 'sent'
                ]
            ])
        ], ['merge' => true]);

        // Fetch user presence
        $snapshot = $docRef->snapshot();
        $userStatus = $snapshot['user_status'] ?? 'offline';
        $userLastSeen = $snapshot['user_last_seen'] ?? null;

        return response()->json([
            'success' => true,
            'message' => "Reply Sent Successfully!",
            'user_status' => [
                'status' => $userStatus,
                'last_seen' => ($userStatus === 'online') ? null : $userLastSeen
            ]
        ]);
    }

    public function adminSendLocation(Request $request, $userId)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $adminId = Auth::id();

        try {
            $docRef = $this->firestore->collection('messages')->document("user_{$userId}");
            $messageId = uniqid('msg_', true);

            $docRef->set(['user_id' => $userId], ['merge' => true]);
            $docRef->update([
                [
                    'path' => 'chat',
                    'value' => FieldValue::arrayUnion([
                        [
                            'id'         => $messageId,
                            'sender'     => 'admin',
                            'type'       => 'location',
                            'latitude'   => $request->latitude,
                            'longitude'  => $request->longitude,
                            'status'     => 'sent',
                            'created_at' => new Timestamp(new \DateTime())
                        ]
                    ])
                ]
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Location sent successfully',
                'data' => [
                    'id'        => $messageId,
                    'user_id'   => $userId,
                    'admin_id'  => $adminId,
                    'sender'    => 'admin',
                    'type'      => 'location',
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'status'    => 'sent',
                    'created_at'=> now()->format('Y-m-d H:i:s'),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
