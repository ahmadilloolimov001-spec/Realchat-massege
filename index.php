<?php
session_start();
include './real-time-chat/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: /real-time-chat/login-page/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$edit_id = 0;
$edit_message = "";


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM messages WHERE id=$id AND user_id=$user_id");
    header("Location: index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);

    if (!empty($_POST['edit_id'])) {

        $id = $_POST['edit_id'];
        $conn->query("UPDATE messages SET message='$message' WHERE id=$id AND user_id=$user_id");
    } else {

        $conn->query("INSERT INTO messages (user_id, message) VALUES ($user_id, '$message')");
    }
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/real-time-chat/instruments/style.css">
    <style>
        .message-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }

        .message-actions {
            position: relative;
            margin-left: 10px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message-wrapper:hover .message-actions {
            opacity: 1;
        }

        .message-wrapper.mine {
            flex-direction: row-reverse;
        }

        .message-wrapper.mine .message-actions {
            margin-left: 0;
            margin-right: 10px;
        }

        .action-btn {
            background: none;
            border: none;
            color: #ccc;
            cursor: pointer;
            padding: 5px;
        }

        .action-menu {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: #24243e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: none;
            flex-direction: column;
            min-width: 80px;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .action-menu.show {
            display: flex;
        }

        .action-menu button,
        .action-menu a {
            background: none;
            border: none;
            color: white;
            padding: 8px 12px;
            text-align: left;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }

        .action-menu button:hover,
        .action-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body>

    <div class="chat-wrapper">
        <div class="chat-header">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Avatar">
            <div class="chat-info">
                <h3>Dasturchilar Guruhi</h3>
                <span>online</span>
            </div>
            <div class="header-icons" style="margin-left: auto; color: white; cursor: pointer;">
                <a href="/real-time-chat/login-page/index.php"><i class="fa-solid fa-arrow-right-from-bracket"
                        style="color: #74C0FC;"></i></a>
            </div>
        </div>

        <div class="messages-container" id="scrollContainer">
            <?php
            $sql = "SELECT messages.*, users.username FROM messages JOIN users ON messages.user_id = users.id ORDER BY created_at ASC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $is_mine = ($row['user_id'] == $user_id);
                    $class = $is_mine ? "outgoing" : "incoming";
                    $wrapper_class = $is_mine ? "mine" : "";
                    ?>
                    <div class="message-wrapper <?= $wrapper_class ?>">
                        <div class="message <?= $class ?>">
                            <strong><?= htmlspecialchars($row['username']) ?></strong><br>
                            <?= nl2br(htmlspecialchars($row['message'])) ?>
                        </div>
                        <?php if ($is_mine): ?>
                            <div class="message-actions">
                                <button class="action-btn" onclick="toggleMenu(<?= $row['id'] ?>)">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="action-menu" id="menu-<?= $row['id'] ?>">
                                    <button
                                        onclick="editMessage(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['message'])) ?>')">Edit</button>
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('O\'chirilsinmi?')">Delete</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <form class="input-area" action="index.php" method="POST">
            <input type="hidden" name="edit_id" id="edit_id_input">
            <input type="text" name="message" id="message_input" placeholder="Xabar yozing..." required
                autocomplete="off">
            <button type="submit">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                </svg>
            </button>
        </form>
    </div>

    <script>

        var container = document.getElementById("scrollContainer");
        container.scrollTop = container.scrollHeight;

        function toggleMenu(id) {

            document.querySelectorAll('.action-menu').forEach(el => {
                if (el.id !== 'menu-' + id) el.classList.remove('show');
            });

            document.getElementById('menu-' + id).classList.toggle('show');
        }

        function editMessage(id, text) {
            document.getElementById('edit_id_input').value = id;
            document.getElementById('message_input').value = text;
            document.getElementById('message_input').focus();

            document.getElementById('menu-' + id).classList.remove('show');
        }


        document.addEventListener('click', function (e) {
            if (!e.target.closest('.message-actions')) {
                document.querySelectorAll('.action-menu').forEach(el => el.classList.remove('show'));
            }
        });
    </script>

</body>

</html>