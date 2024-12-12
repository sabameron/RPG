<?php
class StoryGame {
    private $baseDir;
    private $currentPath;
    
    public function __construct($baseDir = 'game_story') {
        $this->baseDir = rtrim($baseDir, '/');
        $this->currentPath = '';
    }
    
    public function getCurrentOptions() {
        $fullPath = $this->baseDir . '/' . $this->currentPath;
        
        if (!is_dir($fullPath)) {
            return ['error' => 'ストーリーが見つかりません'];
        }
        
        $items = scandir($fullPath);
        $options = [];
        $question = null;
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            if (strpos($item, '？') !== false) {
                $question = str_replace('？', '', $item);
            } else if (is_dir($fullPath . '/' . $item)) {
                $options[] = $item;
            }
        }
        
        return [
            'question' => $question,
            'options' => $options,
            'current_path' => $this->currentPath
        ];
    }
    
    public function selectOption($option) {
        $newPath = $this->currentPath ? $this->currentPath . '/' . $option : $option;
        $fullPath = $this->baseDir . '/' . $newPath;
        
        if (!is_dir($fullPath)) {
            return false;
        }
        
        $this->currentPath = $newPath;
        return true;
    }
    
    public function restart() {
        $this->currentPath = '';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>フォルダストーリーゲーム</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .question {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .option {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            background-color: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .option:hover {
            background-color: #e0e0e0;
        }
        .restart {
            margin-top: 20px;
            padding: 10px;
            background-color: #ff9999;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    
    if (!isset($_SESSION['game'])) {
        $_SESSION['game'] = new StoryGame();
    }
    
    if (isset($_POST['option'])) {
        $_SESSION['game']->selectOption($_POST['option']);
    }
    
    if (isset($_POST['restart'])) {
        $_SESSION['game']->restart();
    }
    
    $state = $_SESSION['game']->getCurrentOptions();
    ?>
    
    <div class="question">
        <?php echo $state['question'] ?? 'ストーリーを選択してください'; ?>
    </div>
    
    <form method="post">
        <?php foreach ($state['options'] as $option): ?>
            <button type="submit" name="option" value="<?php echo htmlspecialchars($option); ?>" class="option">
                <?php echo htmlspecialchars($option); ?>
            </button>
        <?php endforeach; ?>
        
        <button type="submit" name="restart" class="restart">最初から始める</button>
    </form>
</body>
</html>
