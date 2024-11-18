<?php
// Connecting to database
$conn = mysqli_connect("localhost", "root", "", "chatbot") or die("Database Error");

// Including AI API handler
include_once 'openai_api.php';

// Getting user message through AJAX
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);

// Predefined responses for specific queries
$predefined_responses = [
    "hi" => "Hello! How can I assist you today?",
    "hello" => "Hi there! What can I do for you?",
    "how are you" => "I'm here to assist you with any information you need!",
    "bye" => "Goodbye! Have a nice day!",
    "what is the name of this school" => "The name of the school is Dewan High School.",
    "tell me the name of the school" => "The name of the school is Dewan High School.",
    "who are you ?" => "I am the Dewan High School chatbot, here to help you!",
    "who are you" => "I am the Dewan High School chatbot, here to help you!",
    "good" => "I'm glad to hear that! How can I assist you further?",
    "thank you" => "You're welcome! I'm happy to help."
];

// Preprocessing user message
$lowercaseMesg = strtolower(trim($getMesg));

// 1. Check predefined responses first for exact matches
if (array_key_exists($lowercaseMesg, $predefined_responses)) {
    echo $predefined_responses[$lowercaseMesg];
    exit();
}

// 2. Check exact match in the database
$check_exact_match = "SELECT replies FROM chatbot WHERE LOWER(queries) = '$lowercaseMesg'";
$run_exact_match = mysqli_query($conn, $check_exact_match);

if (mysqli_num_rows($run_exact_match) > 0) {
    $fetch_data = mysqli_fetch_assoc($run_exact_match);
    echo $fetch_data['replies'];
    exit();
}

// 3. Advanced Semantic Matching: Search with context
$keywords = explode(" ", $lowercaseMesg);
$query_parts = [];

foreach ($keywords as $word) {
    $query_parts[] = "queries LIKE '%" . $word . "%'";
}

$check_contextual_match = "
    SELECT replies, 
           MATCH(queries) AGAINST ('" . implode(" ", $keywords) . "' IN NATURAL LANGUAGE MODE) AS score
    FROM chatbot
    WHERE " . implode(" OR ", $query_parts) . "
    ORDER BY score DESC, CHAR_LENGTH(queries) ASC
    LIMIT 1";

$run_contextual_match = mysqli_query($conn, $check_contextual_match);

if (mysqli_num_rows($run_contextual_match) > 0) {
    $fetch_data = mysqli_fetch_assoc($run_contextual_match);
    echo $fetch_data['replies'];
    exit();
}

// 4. Fallback to AI for intelligent, formal responses
try {
    $ai_response = callOpenAI($getMesg);
    echo $ai_response;
} catch (Exception $e) {
    echo "I'm sorry, I encountered an issue. Can you try rephrasing your question?";
}
?>
