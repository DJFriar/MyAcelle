<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Chatgpt\Chatgpt;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $chatgpt = Chatgpt::initialize();
        $messages = $request->messages;

        // reduce messages length
        $messages = array_slice($messages, -1, 1);

        // slice long messages
        $messages = array_map(function ($message) {
            $message['content'] = substr($message['content'], 0, 180);
            return $message;
        }, $messages);

        try {
            // try to get response message
            $newMessage = $chatgpt->ask($messages);
            return response()->json($newMessage);
        } catch (\Throwable $e) {
            return response()->json([
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
