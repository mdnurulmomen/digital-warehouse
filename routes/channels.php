<?php

use App\Models\Requisition;
use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/*
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/

Broadcast::channel('new-requisition', function ($user) {
    return true;
}, ['guards' => ['admin', 'manager', 'warehouse']]);

Broadcast::channel('new-dispatch.{merchantId}', function ($user, $merchantId) {
    return (int) $user->id === (int) $merchantId;
}, ['guards' => ['merchant']]);

Broadcast::channel('product-received', function ($user) {
    return true;
}, ['guards' => ['admin', 'manager', 'warehouse']]);
