<!DOCTYPE html>
<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login-page/index.php");
    exit();
}

$myUserId = $_SESSION['user_id'];

$sql = "SELECT m.message, m.user_id, u.username FROM messages m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.created_at ASC";
$result = $conn->query($sql);
?>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .chat-container {
            width: 100%;
            max-width: 800px;
            height: 90vh;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            padding: 15px 20px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h1 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .logout-link {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        #messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #eef1f5;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            align-self: flex-start;
            word-wrap: break-word;
        }

        .message.my-message {
            align-self: flex-end;
            background: #dcf8c6;
        }

        .message strong {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 5px;
        }

        #form {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }

        #input {
            flex: 1;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 20px;
            outline: none;
            font-size: 1rem;
        }

        #input:focus {
            border-color: #007bff;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>

    <div class="chat-container">
        <div class="chat-header">
            <h1>Telegram Clone Chat</h1>
            <a href="/login-page/index.php" class="logout-link">Chiqish</a>
        </div>

        <div id="messages">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $class = ($row['user_id'] == $myUserId) ? 'my-message' : '';
                    $uName = htmlspecialchars($row['username'] ?? 'User');
                    $msgText = htmlspecialchars($row['message']);
                    echo "<div class='message $class'><strong>$uName</strong>$msgText</div>";
                }
            } else {
                echo "<p style='text-align:center; color:#999;'>Hozircha xabarlar yo'q</p>";
            }
            ?>
        </div>

        <form id="form" action="">
            <input id="input" autocomplete="off" placeholder="Xabar yozing..." />
            <button>Yuborish</button>
        </form>
    </div>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3000');
        const form = document.getElementById('form');
        const input = document.getElementById('input');
        const messages = document.getElementById('messages');
        
        const myUserId = <?= $myUserId ?>;

        messages.scrollTop = messages.scrollHeight;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (input.value) {
                const msgData = {
                    text: input.value,
                    userId: myUserId
                };
                socket.emit('chat message', msgData);
                input.value = '';
            }
        });

        socket.on('chat message', (msg) => {
            window.location.reload();
        });

        socket.on('connect', () => {
            console.log('Connected to server');
        });
    </script>
</body>

</html>
