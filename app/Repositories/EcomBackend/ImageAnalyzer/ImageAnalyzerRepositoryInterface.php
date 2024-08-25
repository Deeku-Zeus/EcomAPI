<?php

namespace App\Repositories\EcomBackend\ImageAnalyzer;
/*
 * ImageAnalyzerRepositoryInterface
 */

interface ImageAnalyzerRepositoryInterface
{
    /**
     * @param array $request
     * @return array
     */
    public function storeAnalyzeRequest(array $request): array;
}
