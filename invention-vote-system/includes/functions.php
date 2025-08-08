<?php

/**
 * Redirect to another URL and terminate the script.
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

?>
