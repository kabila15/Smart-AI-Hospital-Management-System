<?php
// train_model.php
// A simple Multinomial Naive Bayes implementation in PHP

$dataset = [
    // Cardiology (Critical)
    ["symptoms" => "severe chest pain and shortness of breath", "label" => "Cardiology", "priority" => 1],
    ["symptoms" => "tightness in chest, left arm pain, sweating", "label" => "Cardiology", "priority" => 1],
    ["symptoms" => "heart palpitations and dizzy spells", "label" => "Cardiology", "priority" => 1],
    ["symptoms" => "chest pressure, breathing difficulty", "label" => "Cardiology", "priority" => 1],
    ["symptoms" => "rapid heartbeat, angina, chest pain", "label" => "Cardiology", "priority" => 1],
    ["symptoms" => "irregular heartbeat and palpitations", "label" => "Cardiology", "priority" => 1],

    // Neurology
    ["symptoms" => "severe headache and blurry vision", "label" => "Neurology", "priority" => 0],
    ["symptoms" => "migraine, nausea, sensitivity to light", "label" => "Neurology", "priority" => 0],
    ["symptoms" => "numbness in fingers, dizziness, headache", "label" => "Neurology", "priority" => 0],
    ["symptoms" => "brain fog, chronic headache, memory loss", "label" => "Neurology", "priority" => 0],
    ["symptoms" => "frequent seizures and severe headache", "label" => "Neurology", "priority" => 1],
    ["symptoms" => "stroke symptoms, facial drooping, slurred speech", "label" => "Neurology", "priority" => 1],

    // Orthopedics
    ["symptoms" => "joint pain and back pain", "label" => "Orthopedics", "priority" => 0],
    ["symptoms" => "fracture in arm, bone pain", "label" => "Orthopedics", "priority" => 1],
    ["symptoms" => "knee pain and shoulder pain difficulty moving", "label" => "Orthopedics", "priority" => 0],
    ["symptoms" => "muscle pain and joint swelling", "label" => "Orthopedics", "priority" => 0],

    // Pulmonology
    ["symptoms" => "severe cough and breathing difficulty", "label" => "Pulmonology", "priority" => 1],
    ["symptoms" => "asthma attack, wheezing, shortness of breath", "label" => "Pulmonology", "priority" => 1],
    ["symptoms" => "chest tightness and persistent cough", "label" => "Pulmonology", "priority" => 0],
    ["symptoms" => "tuberculosis symptoms, coughing blood", "label" => "Pulmonology", "priority" => 1],

    // Dermatology
    ["symptoms" => "skin rash and itching all over", "label" => "Dermatology", "priority" => 0],
    ["symptoms" => "skin infection, acne, eczema flare up", "label" => "Dermatology", "priority" => 0],
    ["symptoms" => "hair loss and scalp irritation", "label" => "Dermatology", "priority" => 0],
    ["symptoms" => "burning skin sensation and redness", "label" => "Dermatology", "priority" => 0],

    // Gastroenterology
    ["symptoms" => "stomach pain, vomiting and diarrhea", "label" => "Gastroenterology", "priority" => 0],
    ["symptoms" => "severe constipation and nausea", "label" => "Gastroenterology", "priority" => 0],
    ["symptoms" => "bloating, acid reflux, heartburn", "label" => "Gastroenterology", "priority" => 0],
    ["symptoms" => "stomach ache and frequent stools", "label" => "Gastroenterology", "priority" => 0],

    // Ophthalmology
    ["symptoms" => "eye pain and blurred vision", "label" => "Ophthalmology", "priority" => 0],
    ["symptoms" => "redness in eye, watery eyes", "label" => "Ophthalmology", "priority" => 0],
    ["symptoms" => "double vision and eye strain", "label" => "Ophthalmology", "priority" => 0],

    // ENT
    ["symptoms" => "ear pain and hearing loss", "label" => "ENT", "priority" => 0],
    ["symptoms" => "sore throat, nasal congestion, sinusitis", "label" => "ENT", "priority" => 0],
    ["symptoms" => "tonsil swelling and difficulty swallowing", "label" => "ENT", "priority" => 0],

    // Pediatrics
    ["symptoms" => "child has high fever and crying continuously", "label" => "Pediatrics", "priority" => 0],
    ["symptoms" => "baby vomiting and diarrhea", "label" => "Pediatrics", "priority" => 1],
    ["symptoms" => "toddler with child rash and growth issues", "label" => "Pediatrics", "priority" => 0],
    ["symptoms" => "child asthma, trouble breathing", "label" => "Pediatrics", "priority" => 1],

    // Endocrinology
    ["symptoms" => "diabetes symptoms, high blood sugar", "label" => "Endocrinology", "priority" => 0],
    ["symptoms" => "thyroid issues, weight gain, fatigue", "label" => "Endocrinology", "priority" => 0],
    ["symptoms" => "unexplained weight loss, excessive thirst", "label" => "Endocrinology", "priority" => 0],

    // General
    ["symptoms" => "mild fever, cough, runny nose", "label" => "General", "priority" => 0],
    ["symptoms" => "stomach ache and feeling tired", "label" => "General", "priority" => 0],
    ["symptoms" => "throat pain, cough, cold, fever", "label" => "General", "priority" => 0],
    ["symptoms" => "body ache, fatigue, low grade fever", "label" => "General", "priority" => 0]
];

function tokenize($text)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9 ]/', ' ', $text);
    $words = explode(' ', $text);
    $tokens = [];
    foreach ($words as $word) {
        $word = trim($word);
        if (strlen($word) > 2) { // Basic stop word removal
            $tokens[] = $word;
        }
    }
    return $tokens;
}

$classes = [];
$vocab = [];
$class_word_counts = [];
$class_total_words = [];
$class_priority = []; // to track if a class is generally high priority, or we can map keywords to priority

$total_docs = count($dataset);

// Training Phase
foreach ($dataset as $data) {
    $class = $data['label'];
    $priority = $data['priority'];
    $tokens = tokenize($data['symptoms']);

    if (!isset($classes[$class])) {
        $classes[$class] = 0;
        $class_word_counts[$class] = [];
        $class_total_words[$class] = 0;
        $class_priority[$class] = [];
    }

    $classes[$class]++;
    $class_priority[$class][] = $priority;

    foreach ($tokens as $token) {
        $vocab[$token] = true;
        if (!isset($class_word_counts[$class][$token])) {
            $class_word_counts[$class][$token] = 0;
        }
        $class_word_counts[$class][$token]++;
        $class_total_words[$class]++;
    }
}

// Prepare model for serialization
$model = [
    'vocab_size' => count($vocab),
    'total_docs' => $total_docs,
    'classes' => $classes,
    'class_word_counts' => $class_word_counts,
    'class_total_words' => $class_total_words,
    // Store avg priority per class to guess if it's a priority case
    'class_avg_priority' => []
];

foreach ($class_priority as $class => $priorities) {
    $avg = array_sum($priorities) / count($priorities);
    $model['class_avg_priority'][$class] = $avg;
}

// Add simple priority keyword flags for the API to use for exact-match emergency overrides
$model['critical_keywords'] = ['chest pain', 'shortness of breath', 'heart attack', 'breathing difficulty', 'seizures', 'stroke', 'fracture', 'heavy bleeding', 'unconscious', 'poisoning'];

file_put_contents('model_data.json', json_encode($model));

echo "Model trained successfully and saved to model_data.json\n";
?>