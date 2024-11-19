<?php
session_start();

// Reset game if requested
if (isset($_POST['reset'])) {
    session_unset();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Initialize game after difficulty selection
if (!isset($_SESSION['target']) && isset($_POST['level'])) {
    $level = $_POST['level'];
    $maxNumber = $level == 2 ? 100 : 50;
    $_SESSION['target'] = rand(1, $maxNumber);
    $_SESSION['level'] = $level;
    $_SESSION['maxNumber'] = $maxNumber;
    $_SESSION['attempts'] = 0;
    $_SESSION['history'] = [];
}

$feedback = '';
$gameWon = false;
if (isset($_POST['guess'])) {
    $guess = (int)$_POST['guess'];
    $_SESSION['attempts']++;
    $_SESSION['history'][] = $guess;

    if ($guess < $_SESSION['target']) {
        $feedback = ($guess < $_SESSION['target'] - 10) ? "Too low!" : "Low!";
    } elseif ($guess > $_SESSION['target']) {
        $feedback = ($guess > $_SESSION['target'] + 10) ? "Too high!" : "High!";
    } else {
        $feedback = "🎉 Congratulations! You guessed the right number in {$_SESSION['attempts']} attempts.";
        $feedback .= "<br>Your guess history: " . implode(", ", $_SESSION['history']);
        $gameWon = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game</title>
</head>
<body>
    <h1>Guess the Number Game</h1>

    <?php if (!isset($_SESSION['target']) || (isset($gameWon) && $gameWon)): ?>
        <!-- Difficulty selection screen or after winning -->
        <?php if ($gameWon): ?>
            <p><?= $feedback ?></p>
            <form method="post">
                <button type="submit" name="reset">Play Again</button>
                <button type="button" onclick="window.close();">Exit</button>
            </form>
        <?php else: ?>
            <form method="post">
                <label for="level">Choose Difficulty Level:</label>
                <select name="level" id="level">
                    <option value="1">Level 1 (1-50)</option>
                    <option value="2">Level 2 (1-100)</option>
                </select>
                <button type="submit">Start Game</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <!-- Game screen -->
        <p>Level <?= $_SESSION['level'] ?>: Guess a number between 1 and <?= $_SESSION['maxNumber'] ?></p>

        <form method="post">
            <input type="number" name="guess" min="1" max="<?= $_SESSION['maxNumber'] ?>" required>
            <button type="submit">Submit Guess</button>
        </form>

        <form method="post">
            <button type="submit" name="reset">Reset Game</button>
        </form>

        <p><?= $feedback ?></p>

        <?php if (!empty($_SESSION['history'])): ?>
            <p>Guess history: <?= implode(", ", $_SESSION['history']) ?></p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
