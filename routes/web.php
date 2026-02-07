<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignEmailController;

Route::get('/', function () {
    return view('welcome');
});
