<?php

namespace App\Services;

use App\Models\User;

class GeminiChatbotService
{
    protected AiGatewayService $gateway;

    public function __construct(?AiGatewayService $gateway = null)
    {
        $this->gateway = $gateway ?? app(AiGatewayService::class);
    }

    /**
     * Delegasikan percakapan Asisten Virtual SIPABIS ke AiGatewayService (Multi-Provider & Fallback).
     */
    public function chat(string $userMessage, ?User $user = null): array
    {
        return $this->gateway->chat($userMessage, $user, 'chatbot');
    }
}
