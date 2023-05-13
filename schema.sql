-- Create the conversation_history table
CREATE TABLE conversation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_message TEXT NOT NULL
);

-- Create the conversations table
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_message TEXT NOT NULL,
    assistant_response TEXT NOT NULL
);

-- Create the assistant_data table
CREATE TABLE assistant_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prompt TEXT NOT NULL,
    response TEXT NOT NULL
);
