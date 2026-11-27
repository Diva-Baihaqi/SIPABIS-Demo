<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected GeminiChatbotService $chatbotService;

    public function __construct(GeminiChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Endpoint API untuk memproses percakapan dengan Asisten Virtual SIPABIS.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:500'],
        ], [
            'message.required' => 'Pesan pertanyaan wajib diisi.',
            'message.min' => 'Pesan minimal terdiri dari 2 karakter.',
            'message.max' => 'Pesan maksimal 500 karakter agar hemat token.',
        ]);

        $user = Auth::user();
        $result = $this->chatbotService->chat($validated['message'], $user);

        return response()->json([
            'success' => $result['success'] ?? false,
            'reply' => $result['reply'] ?? 'Maaf, terjadi kesalahan saat memproses respon.',
            'user' => $user ? [
                'name' => $user->name,
                'role' => $user->role,
                'ruangan' => $user->ruangan->nama_ruangan ?? null,
            ] : null,
        ], $result['success'] ? 200 : 503);
    }
}
