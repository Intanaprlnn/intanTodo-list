<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: tasks.php");
    exit;
}

$task_id = $_GET['id'];

// Ambil data tugas berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    header("Location: tasks.php");
    exit;
}

// Update tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);

    $updateStmt = $pdo->prepare("UPDATE tasks SET title = ? WHERE id = ? AND user_id = ?");
    $updateStmt->execute([$title, $task_id, $user_id]);

    header("Location: tasks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Task</h1>
        <form method="POST" action="">
            <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
            <button type="submit">Simpan Perubahan</button>
        </form>
        <a href="tasks.php" class="btn">Kembali</a>
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
