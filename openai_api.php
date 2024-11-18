<?php
// openai_api.php
function callOpenAI($message) {
    $api_key = 'your-openai-api-key'; // Replace with your OpenAI API key

    $data = [
        "model" => "gpt-4", // Use GPT-4 for improved quality
        "messages" => [
            ["role" => "system", "content" => "You are a polite and highly accurate assistant for a high school. Respond with clear, formal replies."],
            ["role" => "user", "content" => $message],
        ],
        "temperature" => 0.5, // Lower randomness for more focused responses
        "max_tokens" => 200, // Allow more detailed responses
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key",
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $response_data = json_decode($response, true);
        return $response_data['choices'][0]['message']['content'] ?? "I'm sorry, I didn't quite catch that. Could you clarify?";
    }

    return "Error: Unable to fetch AI response.";
}
?>
