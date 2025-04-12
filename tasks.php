<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Tambah tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, status) VALUES (?, ?, 'Not Done')");
    $stmt->execute([$user_id, $title]);

    header("Location: tasks.php");
    exit;
}

// Ambil tugas berdasarkan status, termasuk jumlah subtask yang belum selesai
$tasks_not_done = $pdo->prepare("
    SELECT t.*, 
        (SELECT COUNT(*) FROM subtasks s WHERE s.task_id = t.id AND s.completed = 'Not Done') AS pending_subtasks 
    FROM tasks t 
    WHERE t.user_id = ? AND t.status = 'Not Done'
");
$tasks_not_done->execute([$user_id]);

$tasks_done = $pdo->prepare("
    SELECT t.*, 
        (SELECT COUNT(*) FROM subtasks s WHERE s.task_id = t.id AND s.completed = 'Not Done') AS pending_subtasks 
    FROM tasks t 
    WHERE t.user_id = ? AND t.status = 'Done'
");
$tasks_done->execute([$user_id]);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <script>
    function showNotification(message) {
        const notification = document.getElementById('notification');
        notification.innerText = message;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    function updateStatus(taskId, checkbox, pendingSubtasks) {
        if (pendingSubtasks > 0) {
            showNotification('Masih ada subtask yang belum selesai!');
            checkbox.checked = false;
            return;
        }

        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `task_id=${taskId}&status=${checkbox.checked ? 'Done' : 'Not Done'}`
        }).then(() => location.reload());
    }
</script>

<div id="notification" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: red; color: white; padding: 10px; border-radius: 5px;"></div>
</head>

<body>
    
    <div class="container">

        <h1>To-Do List</h1>

        <form method="POST" action="tasks.php">
            <input type="text" name="title" placeholder="Masukan Task" required>
            <button type="submit">Tambah Task</button>
        </form>

        <h2>Active Tasks</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Judul</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
    <?php foreach ($tasks_not_done as $task): ?>
        <tr>
            <td>
                <input type="checkbox" 
                       onclick="updateStatus(<?= $task['id'] ?>, this, <?= $task['pending_subtasks'] ?>)">
            

                <span id="warning-<?= $task['id'] ?>"></span>
            </td>
            <td><?= htmlspecialchars($task['title']) ?></td>
            <td>
    <a href="subtasks.php?task_id=<?= $task['id'] ?>" class="btn">Lihat Subtask</a>
    <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn edit">Edit</a>
    <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn delete" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a>
</td>
        </tr>
    <?php endforeach; ?>
</tbody>


        </table>

        <h2>Completed Tasks</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Judul</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
           <?php foreach ($tasks_done as $task): ?>
        <tr><td><input type="checkbox" 
       onclick="updateStatus(<?= $task['id'] ?>, this, <?= $task['pending_subtasks'] ?>)"
       <?= $task['status'] === 'Done' ? 'checked disabled' : '' ?>>
       </td>
            
            <td style="text-decoration: <?= $task['status'] === 'Done' ? 'line-through' : 'none' ?>;">
    <?= htmlspecialchars($task['title']) ?>
</td>
            <td>
                <a href="subtasks.php?task_id=<?= $task['id'] ?>" class="btn">Lihat Subtask</a>
                <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn delete" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>

        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Palatino;
}

body {
    background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    text-align: center;
}

h1 {
    margin-bottom: 20px;
}

h2 {
    margin-top: 20px;
    color: rgb(44, 73, 80);
}

/* Form */
form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    background: rgb(108, 202, 214);
    color: black;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: rgb(44, 73, 80);
    color: white;
}

.btn {
    display: inline-block;
    padding: 5px 10px;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    background: rgb(44, 78, 80);
}

.btn:hover {
    background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
}

.btn.delete {
    background: rgb(60, 183, 231);
}

.btn.delete:hover {
    background: rgb(43, 132, 192);
}

.logout {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: rgb(60, 205, 231);
    font-weight: bold;
    text-decoration: none;
}

.logout:hover {
    text-decoration: underline;
}
</style>
