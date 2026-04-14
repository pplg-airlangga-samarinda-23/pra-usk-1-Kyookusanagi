<?php
include '../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

// Check if user is admin
if ($user['Role'] != 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$user_id = intval($_GET['id']);
$target_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE UserID = $user_id"));

if (!$target_user) {
    header("Location: index.php?error=User tidak ditemukan");
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'petugas';
    
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username tidak boleh kosong';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username minimal 3 karakter';
    }
    
    if ($role != 'admin' && $role != 'petugas') {
        $errors[] = 'Role tidak valid';
    }
    
    // Check if username already exists (exclude current user)
    $check = mysqli_query($conn, "SELECT UserID FROM user WHERE Username = '$username' AND UserID != $user_id");
    if (mysqli_num_rows($check) > 0) {
        $errors[] = 'Username sudah terdaftar';
    }
    
    if (empty($errors)) {
        $password_part = '';
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            } else {
                $password_hash = md5($password);
                $password_part = ", Password = '$password_hash'";
            }
        }
        
        if (empty($errors)) {
            $update = "UPDATE user SET Username = '$username', Role = '$role' $password_part WHERE UserID = $user_id";
            
            if (mysqli_query($conn, $update)) {
                header("Location: index.php?success=User berhasil diubah");
                exit;
            } else {
                $message = 'Error: ' . mysqli_error($conn);
                $message_type = 'error';
            }
        } else {
            $message = implode('<br>', $errors);
            $message_type = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Kasir</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            padding: 2rem;
        }

        .form-wrapper {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 400px;
        }

        .form-title {
            text-align: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.875rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-submit, .btn-cancel {
            flex: 1;
            padding: 0.875rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-cancel:hover {
            background: #cbd5e1;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .message.success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .message.error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .password-hint {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.3rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Kasir</div>
        <div class="navbar-menu">
            <a href="../dashboard.php">Dashboard</a>
            <span><?= htmlspecialchars($user['Role']); ?></span>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </nav>

    <div class="form-container">
        <div class="form-wrapper">
            <h2 class="form-title">Edit User</h2>

            <?php if ($message): ?>
                <div class="message <?= $message_type; ?>">
                    <span><?= $message; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Minimal 3 karakter" value="<?= htmlspecialchars($target_user['Username']); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter">
                    <div class="password-hint">Tidak wajib diisi jika tidak ingin mengganti password</div>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="petugas" <?= $target_user['Role'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                        <option value="admin" <?= $target_user['Role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    <a href="index.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Sistem Kasir</p>
    </footer>
</body>
</html>
