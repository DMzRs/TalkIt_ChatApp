<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel — anyone logged in can listen
Broadcast::channel('chat', function ($user) {
    return auth()->check();
});
