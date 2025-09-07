<?php

Broadcast::channel('optimizations.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

