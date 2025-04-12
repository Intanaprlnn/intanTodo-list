<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['task_id'])) {
    header("Location: login.php");
    exit;
}

$task_id = $_GET['task_id'];

// Ambil judul task berdasarkan task_id
$stmt = $pdo->prepare("SELECT title FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die("Error: Task tidak ditemukan.");
}

// Tambah subtask
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];

    $stmt = $pdo->prepare("INSERT INTO subtasks (task_id, description, deadline, priority) VALUES (?, ?, ?, ?)");
    $stmt->execute([$task_id, $description, $deadline, $priority]);

    header("Location: subtasks.php?task_id=$task_id");
    exit;
}

// Update subtask
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $subtask_id = $_POST['subtask_id'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];

    // Periksa apakah deadline sudah lewat
    if (strtotime($deadline) < time()) {
        echo "Deadline sudah lewat dan tidak dapat diedit.";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE subtasks SET description = ?, deadline = ?, priority = ? WHERE id = ?");
    $stmt->execute([$description, $deadline, $priority, $subtask_id]);

    header("Location: subtasks.php?task_id=$task_id");
    exit;
}

// Menandai subtask sebagai selesai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subtask_id'])) {
    $subtask_id = $_POST['subtask_id'];
    // Periksa apakah subtask sudah selesai atau belum
    $stmt = $pdo->prepare("UPDATE subtasks SET completed = NOT completed WHERE id = ?");
    $stmt->execute([$subtask_id]);

    header("Location: subtasks.php?task_id=$task_id");
    exit;
}

// Hapus subtask
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM subtasks WHERE id = ?");
    $stmt->execute([$delete_id]);

    header("Location: subtasks.php?task_id=$task_id");
    exit;
}

// Ambil semua subtasks
$subtasks = $pdo->prepare("SELECT * FROM subtasks WHERE task_id = ? ORDER BY priority DESC, deadline ASC");
$subtasks->execute([$task_id]);

// Ambil data untuk edit jika ada parameter edit_id
$edit_subtask = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM subtasks WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_subtask = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Menentukan apakah deadline sudah lewat untuk formulir edit
$is_past_deadline = false;
if ($edit_subtask) {
    $is_past_deadline = strtotime($edit_subtask['deadline']) < time();
}

// Definisikan label untuk prioritas
$priority_labels = [
    3 => 'Sangat Penting',
    2 => 'Penting',
    1 => 'Biasa',
   
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subtasks - <?= htmlspecialchars($task['title']) ?></title>
    <style>
        body {
            font-family: Palatino;
            background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color:rgb(0, 195, 255);
            color: white;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        input, select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color:rgb(40, 142, 167);
            color: black;
            cursor: pointer;
        }

        .edit-form {
            margin-top: 20px;
            background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
            padding: 10px;
            border-radius: 5px;
        }

        .completed {
            text-decoration: line-through;
            color: #888;
        }

        .checkbox-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: rgb(0, 195, 255);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($task['title']) ?></h1> <!-- Tampilkan judul task di sini -->

    <?php if ($edit_subtask): ?>
        <div class="edit-form">
            <h3>Edit Subtask</h3>
            <form method="POST">
                <input type="hidden" name="subtask_id" value="<?= $edit_subtask['id'] ?>">
                <input type="text" name="description" value="<?= htmlspecialchars($edit_subtask['description']) ?>" required>
                <input type="date" name="deadline" value="<?= $edit_subtask['deadline'] ?>" required <?php echo $is_past_deadline ? 'disabled' : ''; ?>>

                <select name="priority" required <?php echo $is_past_deadline ? 'disabled' : ''; ?>>
                    <option value="3" <?= $edit_subtask['priority'] == 3 ? 'selected' : '' ?>>Sangat Penting</option>
                    <option value="2" <?= $edit_subtask['priority'] == 2 ? 'selected' : '' ?>>Penting</option>
                    <option value="1" <?= $edit_subtask['priority'] == 1 ? 'selected' : '' ?>>Biasa</option>
                    
                </select>
                
                <!-- Tombol Simpan dinonaktifkan jika deadline sudah lewat -->
                <button type="submit" name="update" <?php echo $is_past_deadline ? 'disabled' : ''; ?>>Simpan</button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="text" name="description" placeholder="Deskripsi Subtask" required>
            <input type="date" name="deadline" required min="<?= date("Y-m-d") ?>">
            <select name="priority" required>
                <option value="3">Sangat Penting</option>
                <option value="2">Penting</option>
                <option value="1">Biasa</option>
                
            </select>
            <button type="submit" name="add">Tambah</button>
        </form>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Prioritas</th>
                <th>Deskripsi</th>
                <th>Deadline</th>
                <th>Selesai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subtasks as $subtask): ?>
                <tr>
                    <td><?= $priority_labels[$subtask['priority']] ?? 'Tidak Diketahui' ?></td>
                    <td class="<?= $subtask['completed'] ? 'completed' : '' ?>"><?= htmlspecialchars($subtask['description']) ?></td>
                    <td><?= date("d M Y", strtotime($subtask['deadline'])) ?></td>
                    <td>
                        <form method="POST">
                            <div class="checkbox-container">
                                <input type="checkbox" name="subtask_id" value="<?= $subtask['id'] ?>" <?= $subtask['completed'] ? 'checked' : '' ?> onclick="this.form.submit()">
                            </div>
                        </form>
                    </td>
                    <td>
                        <a href="?task_id=<?= $task_id ?>&edit_id=<?= $subtask['id'] ?>">Edit</a>
                        <a href="?task_id=<?= $task_id ?>&delete_id=<?= $subtask['id'] ?>" onclick="return confirm('Apakah anda akan menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tombol untuk kembali ke halaman tasks -->
    <a href="tasks.php" class="back-btn">Kembali</a>
</div>

</body>
</html>
