document.addEventListener('DOMContentLoaded', () => {
    // Load messages from localStorage
    const savedMessages = JSON.parse(localStorage.getItem('chatMessages')) || [];
    const chatBody = document.getElementById('chatBody');
    savedMessages.forEach(msg => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${msg.sender}-message`;
        msgDiv.innerHTML = msg.text;
        chatBody.appendChild(msgDiv);
    });
    chatBody.scrollTop = chatBody.scrollHeight;
});

function toggleChatbot() {
    const chatbot = document.getElementById('chatbot');
    chatbot.style.display = chatbot.style.display === 'flex' ? 'none' : 'flex';
}

function saveMessageToStorage(sender, text) {
    const messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
    messages.push({ sender, text });
    localStorage.setItem('chatMessages', JSON.stringify(messages));
}

async function sendMessage() {
    const inputField = document.getElementById('userInput');
    const chatBody = document.getElementById('chatBody');
    const userText = inputField.value.trim();
    if (!userText) return;

    const userMessage = document.createElement('div');
    userMessage.className = 'chat-message user-message';
    userMessage.textContent = userText;
    chatBody.appendChild(userMessage);
    saveMessageToStorage('user', userText);
    inputField.value = '';
    chatBody.scrollTop = chatBody.scrollHeight;

    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'chat-message bot-message typing-indicator';
    typingIndicator.textContent = 'Bot is typing...';
    chatBody.appendChild(typingIndicator);
    chatBody.scrollTop = chatBody.scrollHeight;

    try {
        const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
method: "POST",
headers: {
"Authorization": "Bearer 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",
"HTTP-Referer": "https://www.multilingualchatbot",
"X-Title": "multilingualchatbot",
"Content-Type": "application/json"
},
body: JSON.stringify({
model: "deepseek/deepseek-r1:free",
messages: [
{
role: "system",
content: `You are the official chatbot assistant of Barangay Bucandala 1.

This website has multiple pages:

1. **Home (index.html)** – Welcomes visitors and introduces Barangay Bucandala 1.
2. **Services (services.html)** – Lists services like barangay clearance, ID processing, community certifications, and more.
3. **About (about.html)** – Describes the barangay’s history, mission, vision, and officials.
4. **Contact (contact.html)** – Provides contact information and a form to reach out to the barangay office.
5. **FAQ (faq.html)** – Answers frequently asked questions from residents and visitors.

You are located at the bottom-right corner of every page and are here to help visitors find information, answer their questions, and assist with navigation.

If someone asks about what this website offers, help guide them to the correct page or provide a summarized answer. Always stay polite, helpful, and multilingual.`
},
{
role: "user",
content: userText
}
]

})
});

        const data = await response.json();
        chatBody.removeChild(typingIndicator);
        const responseText = data.choices?.[0]?.message?.content || 'No response received.';
        const botMessage = document.createElement('div');
        botMessage.className = 'chat-message bot-message';
        botMessage.innerHTML = marked.parse(responseText);
        chatBody.appendChild(botMessage);
        saveMessageToStorage('bot', marked.parse(responseText));
        chatBody.scrollTop = chatBody.scrollHeight;
    } catch (error) {
        chatBody.removeChild(typingIndicator);
        const errorMessage = document.createElement('div');
        errorMessage.className = 'chat-message bot-message';
        errorMessage.textContent = 'Error: ' + error.message;
        chatBody.appendChild(errorMessage);
    }
}

document.getElementById('userInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function startNewChat() {
    localStorage.removeItem('chatMessages');
    document.getElementById('chatBody').innerHTML = '';
}
