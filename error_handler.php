<?hh
set_error_handler(function ($errNum, $msg, $filename, $line) {
    switch ($errNum) {
        case E_ERROR:
            $errorLevel = 'Error';
            break;
        case E_WARNING:
            $errorLevel = 'Warning';
            break;
        case E_NOTICE:
            $errorLevel = 'Notice';
            break;
        default:
            $errorLevel = 'Undefined';
    }

    $errorDate = date("Y-m-d H:i:s");
    $logMsg = "{$errorDate} {$errorLevel}: {$msg} in {$filename} on line {$line}";
    error_log($logMsg);
});

