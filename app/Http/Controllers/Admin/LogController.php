<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    // List available log files in storage/logs
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $files = [];

        if (is_dir($logPath)) {
            foreach (scandir($logPath) as $f) {
                if ($f === '.' || $f === '..') continue;
                $full = $logPath.DIRECTORY_SEPARATOR.$f;
                if (is_file($full)) {
                    $files[] = [
                        'name' => $f,
                        'size' => filesize($full),
                        'modified' => date('Y-m-d H:i:s', filemtime($full)),
                    ];
                }
            }
        }

        return view('admin.logs.index', ['files' => $files]);
    }

    // Show contents (tail) of a log file
    public function show(Request $request, $file)
    {
        $logPath = storage_path('logs');
        $full = realpath($logPath.DIRECTORY_SEPARATOR.$file);
        if (!$full || strpos($full, realpath($logPath)) !== 0 || !is_file($full)) {
            abort(404);
        }

        // Read tail (last 200 lines) to avoid huge responses
        $lines = $this->tailFile($full, 200);

        return view('admin.logs.show', ['file' => $file, 'lines' => $lines]);
    }

    public function download(Request $request, $file)
    {
        $logPath = storage_path('logs');
        $full = realpath($logPath.DIRECTORY_SEPARATOR.$file);
        if (!$full || strpos($full, realpath($logPath)) !== 0 || !is_file($full)) {
            abort(404);
        }

        return Response::download($full, $file);
    }

    protected function tailFile($filepath, $lines = 100)
    {
        $f = fopen($filepath, "rb");
        if ($f === false) return [];
        $buffer = '';
        $chunk = 4096;
        $pos = -1;
        $lineCount = 0;

        fseek($f, 0, SEEK_END);
        $filesize = ftell($f);

        while ($filesize > 0 && $lineCount <= $lines) {
            $seek = max(-$chunk, -$filesize);
            fseek($f, $seek, SEEK_END);
            $buffer = fread($f, -$seek) . $buffer;
            $lineCount = substr_count($buffer, "\n");
            $filesize += $seek;
            if ($seek === -$filesize) break;
        }

        fclose($f);

        $allLines = explode("\n", trim($buffer));
        $last = array_slice($allLines, max(0, count($allLines) - $lines));

        return $last;
    }
}

