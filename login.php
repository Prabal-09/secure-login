<<?php
session_start();
$conn = new mysqli("localhost", "root", "", "login_demo");

// Handle CAPTCHA refresh request
if (isset($_GET['refresh_captcha'])) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha_text = '';
    $length = 6;
    
    for ($i = 0; $i < $length; $i++) {
        $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    $_SESSION['captcha_text'] = $captcha_text;
    
    // Random color scheme selection
    $color_schemes = [
        'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 50%, #fbc2eb 100%)',
        'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
        'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
    ];
    
    $_SESSION['captcha_bg'] = $color_schemes[array_rand($color_schemes)];
    
    header('Content-Type: application/json');
    echo json_encode([
        'captcha' => $captcha_text,
        'bg' => $_SESSION['captcha_bg']
    ]);
    exit();
}

// Always generate CAPTCHA when loading the login page
if (!isset($_SESSION['captcha_text']) || !isset($_SESSION['captcha_bg'])) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha_text = '';
    $length = 6;
    
    for ($i = 0; $i < $length; $i++) {
        $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    $_SESSION['captcha_text'] = $captcha_text;
    
    // Set initial random color scheme
    $color_schemes = [
        'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 50%, #fbc2eb 100%)',
        'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
        'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
    ];
    $_SESSION['captcha_bg'] = $color_schemes[array_rand($color_schemes)];
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_captcha = trim($_POST['captcha_input'] ?? '');
    $real_captcha = $_SESSION['captcha_text'] ?? '';

    // Case-sensitive comparison
    $captcha_valid = ($user_captcha === $real_captcha);

    if (!$captcha_valid) {
        $error = "❌ Incorrect CAPTCHA. Please try again.";
        // Regenerate CAPTCHA
        unset($_SESSION['captcha_text']);
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $captcha_text = '';
        $length = 6;
        
        for ($i = 0; $i < $length; $i++) {
            $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
        }
        $_SESSION['captcha_text'] = $captcha_text;
        
        // Set new color scheme
        $color_schemes = [
            'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 50%, #fbc2eb 100%)',
            'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
            'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
        ];
        $_SESSION['captcha_bg'] = $color_schemes[array_rand($color_schemes)];
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            unset($_SESSION['captcha_text']);
            unset($_SESSION['captcha_bg']);
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "🚫 Invalid Username or Password";
            // Regenerate CAPTCHA
            unset($_SESSION['captcha_text']);
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $captcha_text = '';
            $length = 6;
            
            for ($i = 0; $i < $length; $i++) {
                $captcha_text .= $chars[rand(0, strlen($chars) - 1)];
            }
            $_SESSION['captcha_text'] = $captcha_text;
            
            // Set new color scheme
            $color_schemes = [
                'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 50%, #fbc2eb 100%)',
                'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)',
                'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)'
            ];
            $_SESSION['captcha_bg'] = $color_schemes[array_rand($color_schemes)];
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            text-align: center;
            padding-top: 80px;
        }
        .container {
            background: white;
            width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #ccc;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .message {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .error {
            color: red;
        }
        .captcha-label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #333;
        }
        .captcha-display {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            letter-spacing: 3px;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            color: white;
            user-select: none;
            border: 1px solid #999;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: inline-block;
            position: relative;
            overflow: hidden;
            background: <?= $_SESSION['captcha_bg'] ?? 'linear-gradient(135deg, #ff9a9e 0%, #fad0c4 50%, #fbc2eb 100%)' ?>;
            transition: background 0.5s ease;
        }
        .captcha-display::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.1),
                rgba(255,255,255,0.1) 5px,
                rgba(0,0,0,0.05) 5px,
                rgba(0,0,0,0.05) 10px
            );
        }
        .captcha-char {
            display: inline-block;
            transform: rotate(calc(-5deg + (var(--i) * 10deg)));
            margin: 0 2px;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            font-weight: bold;
            position: relative;
            z-index: 1;
        }
        .refresh-captcha {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
            font-size: 12px;
            margin-bottom: 10px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .refresh-captcha:hover {
            color: darkblue;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="post" id="loginForm">
        <input type="text" name="username" placeholder="Username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <label class="captcha-label">
            Enter the text you see below:
        </label>
        <div class="captcha-display" id="captchaDisplay">
            <?php if (isset($_SESSION['captcha_text'])): ?>
                <?php 
                $chars = str_split($_SESSION['captcha_text']);
                foreach ($chars as $i => $char): ?>
                    <span class="captcha-char" style="--i: <?= $i ?>"><?= htmlspecialchars($char) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                CAPTCHA Error
            <?php endif; ?>
        </div>
        <div class="refresh-captcha" id="refreshCaptcha">⟳ Refresh CAPTCHA</div>
        <input type="text" name="captcha_input" placeholder="Enter CAPTCHA text" required autocomplete="off"><br>

        <input type="submit" name="login" value="Login">
    </form>
</div>

<script>
document.getElementById('refreshCaptcha').addEventListener('click', function() {
    const refreshBtn = this;
    refreshBtn.innerHTML = '⟳ Loading...';
    refreshBtn.style.pointerEvents = 'none';
    
    fetch('login.php?refresh_captcha=1')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            const captchaDisplay = document.getElementById('captchaDisplay');
            captchaDisplay.innerHTML = '';
            captchaDisplay.style.background = data.bg;
            
            data.captcha.split('').forEach((char, i) => {
                const charSpan = document.createElement('span');
                charSpan.className = 'captcha-char';
                charSpan.style.setProperty('--i', i);
                charSpan.textContent = char;
                captchaDisplay.appendChild(charSpan);
            });
            
            refreshBtn.innerHTML = '⟳ Refresh CAPTCHA';
            refreshBtn.style.pointerEvents = 'auto';
        })
        .catch(error => {
            console.error('Error:', error);
            refreshBtn.innerHTML = '⚠ Try Again';
            setTimeout(() => {
                refreshBtn.innerHTML = '⟳ Refresh CAPTCHA';
                refreshBtn.style.pointerEvents = 'auto';
            }, 2000);
        });
});
</script>
</body>
</html>
