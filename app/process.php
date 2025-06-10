<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Config
$upload_dir = __DIR__ . '/uploads/';
$output_dir = __DIR__ . '/processed/';

// Create directories
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
if (!file_exists($output_dir)) mkdir($output_dir, 0777, true);

// Function to setup Python and check LaTeX
function setupEnvironment() {
    $python_commands = ['py', 'python', 'python3'];
    $python_cmd = null;
    
    // Find Python
    foreach ($python_commands as $cmd) {
        $version_check = shell_exec("\"$cmd\" --version 2>&1");
        if ($version_check && strpos(strtolower($version_check), 'python') !== false) {
            // Check dependencies
            $dep_check = shell_exec("\"$cmd\" -c \"import docx, fitz; print('OK')\" 2>&1");
            if (strpos($dep_check, 'OK') !== false) {
                $python_cmd = $cmd;
                break;
            } else {
                // Try to install dependencies
                shell_exec("\"$cmd\" -m pip install python-docx PyMuPDF 2>&1");
                $dep_recheck = shell_exec("\"$cmd\" -c \"import docx, fitz; print('OK')\" 2>&1");
                if (strpos($dep_recheck, 'OK') !== false) {
                    $python_cmd = $cmd;
                    break;
                }
            }
        }
    }
    
    if (!$python_cmd) {
        return ['success' => false, 'error' => 'Python not found or dependencies missing'];
    }
    
    // Check if pdflatex is available
    $latex_check = shell_exec('pdflatex --version 2>&1');
    $has_latex = $latex_check && strpos($latex_check, 'pdfTeX') !== false;
    
    return [
        'success' => true,
        'python_cmd' => $python_cmd,
        'has_latex' => $has_latex,
        'latex_info' => $has_latex ? 'LaTeX available - will generate PDF' : 'LaTeX not available - will create DOCX fallback'
    ];
}

// Validate upload
if (!isset($_FILES['journal']) || !isset($_FILES['template'])) {
    echo json_encode(['error' => 'File jurnal dan template harus diupload']);
    exit;
}

$allowed_journal = ['pdf', 'docx'];
$allowed_template = ['docx', 'tex', 'json']; // Added .tex support
$max_size = 10 * 1024 * 1024; // 10MB

$journal_ext = strtolower(pathinfo($_FILES['journal']['name'], PATHINFO_EXTENSION));
$template_ext = strtolower(pathinfo($_FILES['template']['name'], PATHINFO_EXTENSION));

if (!in_array($journal_ext, $allowed_journal)) {
    echo json_encode(['error' => 'Format jurnal harus PDF atau DOCX']);
    exit;
}

if (!in_array($template_ext, $allowed_template)) {
    echo json_encode(['error' => 'Format template harus DOCX, TEX, atau JSON']);
    exit;
}

if ($_FILES['journal']['size'] > $max_size || $_FILES['template']['size'] > $max_size) {
    echo json_encode(['error' => 'Ukuran file terlalu besar (maksimal 10MB)']);
    exit;
}

// Save uploaded files
$journal_path = $upload_dir . 'journal_' . uniqid() . '.' . $journal_ext;
$template_path = $upload_dir . 'template_' . uniqid() . '.' . $template_ext;

if (!move_uploaded_file($_FILES['journal']['tmp_name'], $journal_path)) {
    echo json_encode(['error' => 'Gagal menyimpan file jurnal']);
    exit;
}

if (!move_uploaded_file($_FILES['template']['tmp_name'], $template_path)) {
    echo json_encode(['error' => 'Gagal menyimpan file template']);
    exit;
}

// Setup environment
$env_setup = setupEnvironment();
if (!$env_setup['success']) {
    echo json_encode([
        'error' => 'Environment setup failed',
        'details' => $env_setup,
        'solutions' => [
            '1. Install Python dari https://python.org/downloads/',
            '2. Install LaTeX distribution:',
            '   - Windows: MiKTeX atau TeX Live',
            '   - Pastikan pdflatex tersedia di PATH',
            '3. Restart web server setelah install'
        ]
    ]);
    unlink($journal_path);
    unlink($template_path);
    exit;
}

$python_cmd = $env_setup['python_cmd'];

// Determine output format based on template and LaTeX availability
$output_extension = ($template_ext === 'tex' && $env_setup['has_latex']) ? 'pdf' : 'docx';
$output_filename = 'jurnal_latex_' . date('Y-m-d_H-i-s') . '.' . $output_extension;
$output_path = $output_dir . $output_filename;

// Run LaTeX parser
$parser_script = __DIR__ . "/parser.py";

if (!file_exists($parser_script)) {
    echo json_encode(['error' => 'File parser.py tidak ditemukan']);
    unlink($journal_path);
    unlink($template_path);
    exit;
}

// Build command
$command = sprintf(
    '"%s" "%s" "%s" "%s" "%s" 2>&1',
    $python_cmd,
    $parser_script,
    $journal_path,
    $template_path,
    $output_path
);

// Execute
$old_dir = getcwd();
chdir(__DIR__);
putenv('PYTHONIOENCODING=utf-8');

error_log("LaTeX command: " . $command);
$output = shell_exec($command);
chdir($old_dir);

error_log("LaTeX output: " . $output);

// Parse result
$result = json_decode($output, true);

if ($result === null) {
    echo json_encode([
        'error' => 'LaTeX processor failed',
        'raw_output' => $output,
        'command' => $command,
        'environment' => $env_setup
    ]);
    unlink($journal_path);
    unlink($template_path);
    exit;
}

if (isset($result['error'])) {
    echo json_encode([
        'error' => 'LaTeX processing error: ' . $result['error'],
        'details' => $result,
        'raw_output' => $output,
        'environment' => $env_setup
    ]);
    unlink($journal_path);
    unlink($template_path);
    exit;
}

// Check if output file exists
$actual_output = $result['output_path'] ?? $output_path;
if (!file_exists($actual_output)) {
    echo json_encode([
        'error' => 'Output file not found',
        'expected_path' => $output_path,
        'actual_path' => $actual_output,
        'result' => $result
    ]);
    unlink($journal_path);
    unlink($template_path);
    exit;
}

// Success response
$relative_path = 'app/processed/' . basename($actual_output);
echo json_encode([
    'success' => true,
    'message' => 'Jurnal berhasil diproses dengan LaTeX',
    'download_url' => $relative_path,
    'metadata' => $result['metadata'] ?? null,
    'file_size' => filesize($actual_output),
    'output_format' => $result['format'] ?? $output_extension,
    'latex_used' => $result['latex_used'] ?? false,
    'environment_info' => $env_setup,
    'processing_info' => [
        'sections_processed' => $result['sections_processed'] ?? [],
        'template_type' => $template_ext,
        'journal_type' => $journal_ext
    ]
]);

// Cleanup
unlink($journal_path);
unlink($template_path);
?>