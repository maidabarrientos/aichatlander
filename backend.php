<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the configuration file
require_once 'config.php';

// Retrieve the configuration values
$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];
$openai_key = $config['openai_key'];
$domain = $config['domain'];

// Create a new PDO instance
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Get user message from AJAX request
$message = $_POST['message'];

// Process user message and get AI response
$assistantResponse = processUserMessage($message);

// Store the updated conversation history in the database
storeConversationHistory($conversationHistory);

// Return the assistant response to the front-end
echo $assistantResponse;

// Function to process user message and generate AI response
function processUserMessage($message) {
    global $openai_key, $domain, $db;

    // Retrieve conversation history from the database
    $conversationHistory = getConversationHistory();

    // Append the user message to the conversation history
    $conversationHistory[] = "User: $message";

    // Retrieve the assistant's knowledge from the database
    $knowledge = getAssistantKnowledge();

    // Construct the prompt by combining conversation history and knowledge
    $prompt = implode('\n', $knowledge) . '\n' . implode('\n', $conversationHistory) . '\nAssistant:';

    // Set up the OpenAI API endpoint
    $url = 'https://api.openai.com/v1/engines/davinci-codex/completions';

    // Prepare the request payload
    $data = array(
        'prompt' => $prompt,
        'max_tokens' => 50
    );

    // Encode the payload as JSON
    $payload = json_encode($data);

    // Set up the request headers
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_key
    );

    // Create a new cURL resource
    $curl = curl_init();

    // Set the cURL options
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    // Send the request and get the response
    $response = curl_exec($curl);

    // Close the cURL resource
    curl_close($curl);

    // Extract the AI response from the JSON response
    $responseData = json_decode($response, true);
    $aiResponse = $responseData['choices'][0]['text'] ?? '';

    // Remove any leading or trailing newline characters
    $aiResponse = trim($aiResponse);

    // Generate the contribution message with the link
    $contribLink = 'https://contrib.com/?domain=' . urlencode($domain);
    $contributionMessage = "If you would like to contribute to $domain, please visit $contribLink";

    // Append the contribution message to the AI response
    $aiResponse .= '\nAssistant: ' . $contributionMessage;

    // Store

User
continue
ChatGPT

the updated conversation history in the database
storeConversationHistory($conversationHistory);

// Return the AI response
return $aiResponse;
}

// Function to retrieve conversation history from the database
function getConversationHistory() {
global $db;

php

$stmt = $db->query("SELECT user_message FROM conversation_history ORDER BY id DESC LIMIT 5");
$conversationHistory = $stmt->fetchAll(PDO::FETCH_COLUMN);
$conversationHistory = array_reverse($conversationHistory);

return $conversationHistory;

}

// Function to store conversation history in the database
function storeConversationHistory($conversationHistory) {
global $db;

php

$stmt = $db->prepare("INSERT INTO conversation_history (user_message) VALUES (?)");
foreach ($conversationHistory as $message) {
    $stmt->execute([$message]);
}

}

// Function to store user message and assistant response in the conversations table
function storeConversation($message, $assistantResponse) {
global $db;

bash

$stmt = $db->prepare("INSERT INTO conversations (user_message, assistant_response) VALUES (?, ?)");
$stmt->execute([$message, $assistantResponse]);

}

// Function to retrieve the assistant's knowledge from the database
function getAssistantKnowledge() {
global $db;

php

$stmt = $db->query("SELECT prompt, response FROM assistant_data");
$knowledge = $stmt->fetchAll(PDO::FETCH_ASSOC);

$knowledgeArray = [];
foreach ($knowledge as $row) {
    $prompt = $row['prompt'];
    $response = $row['response'];
    $knowledgeArray[] = "User: $prompt\nAssistant: $response";
}

return $knowledgeArray;

}
