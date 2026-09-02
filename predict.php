<?php
// predict.php
// Loads model_data.json to predict department based on symptoms

header('Content-Type: application/json');

if (!isset($_GET['symptoms'])) {
    echo json_encode(["status" => "error", "message" => "No symptoms provided."]);
    exit();
}

$symptoms = $_GET['symptoms'];

if (!file_exists('model_data.json')) {
    echo json_encode(["status" => "error", "message" => "Model not trained yet."]);
    exit();
}

$model = json_decode(file_get_contents('model_data.json'), true);

function tokenize($text)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9 ]/', ' ', $text);
    $words = explode(' ', $text);
    $tokens = [];
    foreach ($words as $word) {
        $word = trim($word);
        if (strlen($word) > 2) {
            $tokens[] = $word;
        }
    }
    return $tokens;
}

$tokens = tokenize($symptoms);

// Check for critical keywords first for exact emergency
$is_critical = 0;
foreach ($model['critical_keywords'] as $cw) {
    if (strpos(strtolower($symptoms), $cw) !== false) {
        $is_critical = 1;
        break;
    }
}

// Naive Bayes Prediction
$scores = [];
foreach ($model['classes'] as $class => $doc_count) {
    // P(Class)
    $prior = log($doc_count / $model['total_docs']);

    // P(Document|Class)
    $likelihood = 0;
    foreach ($tokens as $token) {
        $count_in_class = isset($model['class_word_counts'][$class][$token]) ? $model['class_word_counts'][$class][$token] : 0;

        // Laplace Smoothing
        $prob = ($count_in_class + 1) / ($model['class_total_words'][$class] + $model['vocab_size']);
        $likelihood += log($prob);
    }

    $scores[$class] = $prior + $likelihood;
}

// Find max score
$best_class = "";
$max_score = -INF;
foreach ($scores as $class => $score) {
    if ($score > $max_score) {
        $max_score = $score;
        $best_class = $class;
    }
}

// Check average priority of the predicted class if not already critical
if ($is_critical == 0 && isset($model['class_avg_priority'][$best_class])) {
    if ($model['class_avg_priority'][$best_class] > 0.5) {
        $is_critical = 1;
    }
}

// If no tokens matched anything meaningful, default to General
if (empty($tokens)) {
    $best_class = "General";
    $is_critical = 0;
}

echo json_encode([
    "status" => "success",
    "prediction" => $best_class,
    "priority" => $is_critical,
    "scores" => $scores // For debugging
]);
?>