<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'chat-media' => 'required|file|mimes:jpg,jpeg,png,gif,webm,mp4,mp3,wav,pdf|max:51200'
        ]);

        if ($request->hasFile('chat-media')) {
            $file = $request->file('chat-media');
            $path = $file->store('chat-media', 'public');
            return response()->json(['url' => asset('storage/'.$path)]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
