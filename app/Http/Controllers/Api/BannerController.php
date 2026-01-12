<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends ApiController
{
    /**
     * Get active banners
     */
    public function index()
    {
        $banners = Banner::where(function($q) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', now());
        })->where(function($q) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
        })->get();

        return $this->success($banners, "Banners retrieved");
    }
}
