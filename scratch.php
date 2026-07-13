<?php
$dir = "C:/Users/ayans/OneDrive/Documents/A_Projects/Aptech/Vision2026/VisionLab/app/Models/";
$models = ["Attendance", "BookIssue", "Certificate", "FeeChallan", "ForumPost", "ForumTopic", "GradingRubric", "LibraryBook", "Payroll", "Question", "Quiz", "QuizAttempt", "Transaction", "Webhook"];

foreach ($models as $model) {
    $file = $dir . $model . '.php';
    if (!file_exists($file)) continue;

    $content = file_get_contents($file);
    if (strpos($content, 'public function') !== false) continue;

    preg_match('/protected\s+\$fillable\s*=\s*\[(.*?)\];/s', $content, $matches);
    if (!$matches) continue;

    $fields = array_map('trim', explode(',', $matches[1]));
    $methods = "";

    foreach ($fields as $field) {
        $field = trim($field, "'\"");
        if (substr($field, -3) === '_id') {
            $relationName = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', substr($field, 0, -3)))));
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', substr($field, 0, -3))));
            
            $methods .= "\n    public function {$relationName}()\n    {\n        return \$this->belongsTo({$className}::class);\n    }\n";
        }
    }

    if ($methods !== "") {
        $content = preg_replace("/\\s*\\/\\/\\s*\\}\\s*$/s", "\n" . $methods . "\n}\n", $content);
        file_put_contents($file, $content);
        echo "Updated $model\n";
    }
}
