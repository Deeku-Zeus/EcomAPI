<?php

declare(strict_types=1);

namespace App\Http\Controllers;
/**
 * Base Controller
 * API Super Class define common functions.
 */
class ApiBaseController extends Controller
{
    /**
     * Constructer
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
}
