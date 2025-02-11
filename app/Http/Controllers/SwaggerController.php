<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function serveSwagger()
    {
        $filePath = public_path('swagger/swagger.yaml');
        $swaggerContent = File::get($filePath);

        $processedContent = $this->replaceEnvironmentVariables($swaggerContent);
        File::put($filePath, $processedContent);
        return view('swagger');
    }

    private function replaceEnvironmentVariables($content)
    {
        $content = str_replace('{{baseUrl}}', env('APP_URL', 'http://localhost:8085'), $content);

        return $content;
    }
}
