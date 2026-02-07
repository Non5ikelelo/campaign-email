<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;

Route::post('/campaign',[CampaignController::class, 'store']);