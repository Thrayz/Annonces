<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function showException(\Throwable $exception): Response
    {
        // Customize the response based on the exception
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;

        return $this->render('Exception/' . $statusCode . '.html.twig', [
            'exception' => $exception,
            'status_code' => $statusCode,
        ]);
    }
}