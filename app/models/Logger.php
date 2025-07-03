<?php
/**
 * Logger class for centralized logging
 * Handles error logging, debug logging, and activity tracking
 */
class Logger {
    
    private static $instance = null;
    private $logDir;
    private $logFile;
    private $errorFile;
    private $debugMode;
    private $enableLogging;
    private $enableErrorLogging;
    private $enableDebugLogging;
    
    /**
     * Private constructor for singleton pattern
     * @return void
     */
    private function __construct() {
        $this->logDir = BASE_PATH . '/logs';
        $this->logFile = $this->logDir . '/app_' . date('Y-m-d') . '.log';
        $this->errorFile = $this->logDir . '/error_' . date('Y-m-d') . '.log';
        
        // Load logging configuration from environment
        $this->enableLogging = filter_var($_ENV['ENABLE_LOGGING'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->enableErrorLogging = filter_var($_ENV['ENABLE_ERROR_LOGGING'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->enableDebugLogging = filter_var($_ENV['ENABLE_DEBUG_LOGGING'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->debugMode = ($_ENV['ENVIRONMENT'] ?? 'production') === 'development';
        
        // Ensure logs directory exists if logging is enabled
        if ($this->enableLogging && !is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }
    
    /**
     * Get singleton instance
     * @return Logger
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log general information
     * @param string $message Message to log
     * @param array $context Additional context data
     * @return void
     */
    public function info($message, $context = []) {
        if ($this->enableLogging) {
            $this->log('INFO', $message, $context);
        }
    }
    
    /**
     * Log errors
     * @param string $message Error message
     * @param array $context Additional context data
     * @return void
     */
    public function error($message, $context = []) {
        if ($this->enableErrorLogging) {
            $this->log('ERROR', $message, $context);
            
            // Also write to error-specific file
            $this->writeToFile($this->errorFile, $this->formatLog('ERROR', $message, $context));
        }
    }
    
    /**
     * Log debug information (only when enabled)
     * @param string $message Debug message
     * @param array $context Additional context data
     * @return void
     */
    public function debug($message, $context = []) {
        if ($this->enableDebugLogging) {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    /**
     * Log webhook activity
     * @param string $eventType Event type
     * @param array $data Webhook data
     * @param string $status Status (received, stored, slack_sent, slack_failed)
     * @return void
     */
    public function webhook($eventType, $data, $status) {
        if ($this->enableLogging) {
            $context = [
                'event_type' => $eventType,
                'status' => $status,
                'webhook_id' => $data['webhook_id'] ?? null,
                'source_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $this->log('WEBHOOK', "Webhook $status: $eventType", $context);
        }
    }
    
    /**
     * Log Slack integration events
     * @param string $eventType Event type
     * @param bool $success Success status
     * @param string $error Error message if failed
     * @return void
     */
    public function slack($eventType, $success, $error = null) {
        if ($this->enableLogging || (!$success && $this->enableErrorLogging)) {
            $level = $success ? 'INFO' : 'ERROR';
            $message = $success ? "Slack notification sent for $eventType" : "Slack notification failed for $eventType";
            
            $context = [
                'event_type' => $eventType,
                'success' => $success
            ];
            
            if ($error) {
                $context['error'] = $error;
            }
            
            $this->log($level, $message, $context);
        }
    }
    
    /**
     * Core logging method
     * @param string $level Log level
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void
     */
    private function log($level, $message, $context = []) {
        if (!$this->enableLogging) {
            return;
        }
        
        $logEntry = $this->formatLog($level, $message, $context);
        $this->writeToFile($this->logFile, $logEntry);
        
        // In debug mode, also output to error log
        if ($this->debugMode && $level === 'ERROR') {
            error_log($logEntry);
        }
    }
    
    /**
     * Format log entry
     * @param string $level Log level
     * @param string $message Message
     * @param array $context Context data
     * @return string Formatted log entry
     */
    private function formatLog($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        
        return "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
    }
    
    /**
     * Write to log file
     * @param string $file File path
     * @param string $content Content to write
     * @return void
     */
    private function writeToFile($file, $content) {
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
        
        // Rotate logs if file is too large (10MB)
        if (filesize($file) > 10 * 1024 * 1024) {
            $this->rotateLog($file);
        }
    }
    
    /**
     * Rotate log file when it gets too large
     * @param string $file File to rotate
     * @return void
     */
    private function rotateLog($file) {
        $rotatedFile = $file . '.' . time();
        rename($file, $rotatedFile);
        
        // Keep only last 5 rotated files
        $pattern = $file . '.*';
        $files = glob($pattern);
        if (count($files) > 5) {
            // Sort by modification time and delete oldest
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $toDelete = array_slice($files, 0, count($files) - 5);
            foreach ($toDelete as $oldFile) {
                unlink($oldFile);
            }
        }
    }
}