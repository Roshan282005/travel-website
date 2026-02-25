<?php
ob_start();
header("Content-Type: application/json; charset=UTF-8");
ob_clean();

// Enhanced Chat AI Handler with intelligent intent detection and fallback mechanisms
// Supports both OpenAI API and sophisticated local simulation

$apiKey = getenv('OPENAI_API_KEY') ?: null;

$rawInput = file_get_contents("php://input");
$input = json_decode($rawInput, true);

if (!$input) {
    ob_clean();
    http_response_code(400);
    echo json_encode(["reply" => "⚠️ Invalid request format.", "voice" => "Invalid request format"]);
    exit;
}

$conversation = $input["conversation"] ?? [];
$userMessage = trim($input["message"] ?? "");

if (empty($userMessage)) {
    ob_clean();
    echo json_encode(["reply" => "📝 Please enter a message to get started!", "voice" => "Please enter a message"]);
    exit;
}

// ===== Enhanced Intent Detection =====
function detect_intent($text) {
    $t = strtolower($text);
    
    // Route/Navigation intent
    if (preg_match('/\b(route|directions?|navigate|show route|go to|take me|path)\b/', $t)) {
        return ['type' => 'route', 'confidence' => 0.9];
    }
    
    // Time/Duration intent
    if (preg_match('/\b(time|eta|how long|duration|how far|distance)\b/', $t)) {
        return ['type' => 'eta', 'confidence' => 0.9];
    }
    
    // Accommodation intent
    if (preg_match('/\b(hotel|hotels|stay|accommodation|booking|resort|lodge|airbnb|inn)\b/', $t)) {
        return ['type' => 'hotels', 'confidence' => 0.95];
    }
    
    // Itinerary intent
    if (preg_match('/\b(itinerary|plan|schedule|3-day|4-day|5-day|day trip|itinerary)\b/', $t)) {
        return ['type' => 'itinerary', 'confidence' => 0.9];
    }
    
    // Save/Wishlist intent
    if (preg_match('/\b(save|wishlist|bookmark|favorite|like|add to list)\b/', $t)) {
        return ['type' => 'save_wishlist', 'confidence' => 0.95];
    }
    
    // Nearby/Points of Interest intent
    if (preg_match('/\b(nearby|near|around|close|restaurants?|attractions?|museums?|markets?|shopping|food)\b/', $t)) {
        return ['type' => 'nearby', 'confidence' => 0.85];
    }
    
    // Translation intent
    if (preg_match('/\b(translate|translation|in \w+|language)\b/', $t)) {
        return ['type' => 'translate', 'confidence' => 0.8];
    }
    
    // Weather intent
    if (preg_match('/\b(weather|rain|sunny|climate|temperature|forecast|cold|hot)\b/', $t)) {
        return ['type' => 'weather', 'confidence' => 0.85];
    }
    
    // Budget/Cost intent
    if (preg_match('/\b(price|cost|budget|expensive|cheap|affordable|how much|payment)\b/', $t)) {
        return ['type' => 'budget', 'confidence' => 0.8];
    }
    
    // General chat
    return ['type' => 'chat', 'confidence' => 0.5];
}

// ===== Enhanced Simulated Responses =====
function enhanced_simulated_reply($intent, $text) {
    $reply = "";
    $action = null;
    $type = $intent['type'];
    
    switch ($type) {
        case 'route':
            $reply = "🗺️ I'll help you with directions! Could you specify:\n• Starting location\n• Destination\n• Preferred mode (car, public transit, walking)\n\nOnce you provide these details, I can plot the best route for you!";
            break;
            
        case 'eta':
            $reply = "⏱️ Typical travel times in this area:\n• By Car: 20-30 mins\n• By Public Transit: 30-45 mins\n• Walking: 1.5-2 hours\n\nProvide more details for accurate ETA calculation!";
            break;
            
        case 'hotels':
            $reply = "🏨 Popular accommodation options here:\n\n⭐ **Luxury** - 5-star beachfront resorts\n💼 **Mid-range** - 3-4 star hotels with great amenities\n💰 **Budget** - Hostels & guesthouses (₹500-1500/night)\n\nWould you like recommendations for a specific budget or style?";
            $action = ['type' => 'show_places', 'payload' => ['places' => [
                ['name' => 'Luxury Beach Resort', 'lat' => 13.083, 'lon' => 80.27],
                ['name' => 'City Center Hotel', 'lat' => 13.09, 'lon' => 80.26],
                ['name' => 'Budget Hostel', 'lat' => 13.05, 'lon' => 80.28]
            ]]];
            break;
            
        case 'itinerary':
            $reply = "📅 **3-Day Itinerary** (customizable):\n\n**Day 1:** City exploration\n• Morning: Local markets\n• Afternoon: Museums & landmarks\n• Evening: Local cuisine tasting\n\n**Day 2:** Adventure activities\n• Water sports or trekking\n• Beach/nature relaxation\n• Sunset viewing\n\n**Day 3:** Cultural immersion\n• Temple/historical sites\n• Local crafts workshop\n• Souvenir shopping\n\nWant a different duration or focus? Let me know!";
            break;
            
        case 'save_wishlist':
            $reply = "❤️ I can save destinations to your wishlist! To save a specific destination:\n• Click the '❤️ Save' button on destination cards\n• Or tell me the destination name/ID\n• Access your wishlist anytime from your profile\n\nWhich destination would you like to save?";
            break;
            
        case 'nearby':
            preg_match('/\b(restaurant|hotel|attraction|museum|temple|park)\b/i', $text, $m);
            $category = strtolower($m[1] ?? 'attraction');
            $reply = "🔍 Searching for nearby **$category**s...\n\nTop results:\n1. **Popular ' . ucfirst($category) . ' A** - ⭐ 4.8 (450 reviews)\n2. **Recommended ' . ucfirst($category) . ' B** - ⭐ 4.6 (320 reviews)\n3. **Local Gem ' . ucfirst($category) . ' C** - ⭐ 4.7 (180 reviews)\n\nWould you like more details about any of these?";
            $action = ['type' => 'nearby', 'payload' => ['category' => $category]];
            break;
            
        case 'weather':
            $reply = "🌤️ **Weather Forecast**:\n\n**Today:** Sunny, 28°C, 40% humidity\n**Tomorrow:** Partly cloudy, 26°C\n**Day After:** Light rain expected, 24°C\n\n💡 Tip: Best time to visit is early morning (6-9 AM) or late evening (5-8 PM) to avoid midday heat!";
            break;
            
        case 'budget':
            $reply = "💰 **Budget Breakdown** (per person, per day):\n\n**Budget Travel:** ₹1500-2500 (hostels, street food)\n**Mid-range:** ₹3000-6000 (decent hotels, good restaurants)\n**Luxury:** ₹7000+ (5-star hotels, fine dining)\n\nTotal trip cost depends on:\n• Duration\n• Accommodation type\n• Dining preferences\n• Activities & sightseeing\n\nWhat's your budget range?";
            break;
            
        case 'translate':
            preg_match('/in\s+(\w+)|to\s+(\w+)/i', $text, $m);
            $lang = $m[1] ?? $m[2] ?? 'English';
            $reply = "🌐 I can help with translations! Common phrases you might need:\n\n**Greetings:**\n• Hello/Hi\n• Thank you\n• Excuse me\n• How much?\n\nWhich language would you like? I support 50+ languages!";
            break;
            
        default:
            $replies = [
                "That's interesting! Tell me more about what you're looking for in your trip.",
                "Great question! Would you like specific recommendations based on your interests?",
                "I'm here to help! Need information about destinations, accommodations, or activities?",
                "Perfect! Let me know how I can assist with your travel plans.",
                "Absolutely! Is there anything specific you'd like to know about travel?",
                "Why you are Gay! Mrs Potta! 😂"
            ];
            $reply = $replies[array_rand($replies)];
    }
    
    return ['reply' => $reply, 'action' => $action];
}

// ===== Smart Fallback Handler =====
function smart_fallback($userMessage, $intent) {
    $sim = enhanced_simulated_reply($intent, $userMessage);
    return [
        'reply' => $sim['reply'],
        'voice' => strip_tags(str_replace(['**', '*', '🗺️', '⏱️', '🏨', '📅', '❤️', '🔍', '🌤️', '💰', '🌐'], '', $sim['reply'])),
        'action' => $sim['action']
    ];
}

// ===== Main Logic =====
try {
    $intent = detect_intent($userMessage);
    
    // If no API key, use enhanced simulation
    if (empty($apiKey)) {
        ob_clean();
        echo json_encode(smart_fallback($userMessage, $intent));
        exit;
    }
    
    // Try to use OpenAI API with fallback
    $messages = [
        [
            "role" => "system",
            "content" => "You are Sneha, an expert travel assistant. You provide detailed, friendly travel advice with emojis and structured formatting. Be concise but informative. Include practical tips and local insights. For specific actions (route, hotels, etc.), indicate them with <!--ACTION:{JSON}--> markers."
        ]
    ];
    
    // Add conversation history
    foreach ($conversation as $msg) {
        if (!empty($msg["role"]) && !empty($msg["content"])) {
            $messages[] = [
                "role" => $msg["role"],
                "content" => $msg["content"]
            ];
        }
    }
    
    $messages[] = ["role" => "user", "content" => $userMessage];
    
    // Call OpenAI API
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "gpt-4o-mini",
        "messages" => $messages,
        "temperature" => 0.7,
        "max_tokens" => 500
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_errno($ch);
    curl_close($ch);
    
    // Fallback if API call fails
    if ($curlError || $httpCode !== 200) {
        ob_clean();
        echo json_encode(smart_fallback($userMessage, $intent));
        exit;
    }
    
    $result = json_decode($response, true);
    
    // Fallback if response is malformed
    if (!isset($result['choices'][0]['message']['content'])) {
        ob_clean();
        echo json_encode(smart_fallback($userMessage, $intent));
        exit;
    }
    
    $reply = $result['choices'][0]['message']['content'];
    $action = null;
    
    // Extract action if present
    if (preg_match('/<!--ACTION:(\{.*?\})-->/s', $reply, $am)) {
        $json = $am[1];
        $decoded = json_decode($json, true);
        if ($decoded) {
            $action = $decoded;
            $reply = preg_replace('/<!--ACTION:.*?-->/s', '', $reply);
        }
    }
    
    // If no action extracted from AI, use enhanced fallback
    if (!$action) {
        $sim = enhanced_simulated_reply($intent, $userMessage);
        $action = $sim['action'];
    }
    
    ob_clean();
    echo json_encode([
        'reply' => trim($reply),
        'voice' => strip_tags(trim($reply)),
        'action' => $action
    ]);
    exit;
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'reply' => '⚠️ Chat error. ' . $e->getMessage(),
        'voice' => 'Chat error'
    ]);
    exit;
}
