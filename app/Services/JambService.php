<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JambService {

    public function send($data) {
        // Mocking the request - in real scenario, this would post to an external API
        // For now, we hit our own mock endpoint or just return true
        // return Http::post('/jamb/confirm', $data); 
        
        // Since we are mocking internally in the same app, let's just log or return valid response
        return [
            'status' => 'confirmed',
            'reference' => 'JAMB-CAPS-OK-' . uniqid()
        ];
    }
}
