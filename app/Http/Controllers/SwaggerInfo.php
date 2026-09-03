<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Mini IELTS Speaking API',
    version: '1.0.0',
    description: 'API for the IELTS Speaking practice platform. Allows users to register, login, and get AI-powered feedback on their IELTS speaking answers.',
    contact: new OA\Contact(email: 'admin@example.com', name: 'API Support')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'API Server')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter the Bearer token obtained from /api/login or /api/register.'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
#[OA\Tag(name: 'Speaking', description: 'IELTS Speaking endpoints')]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'Question',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'part', type: 'integer', example: 1, description: 'IELTS Speaking part number (1, 2, or 3)'),
        new OA\Property(property: 'topic', type: 'string', example: 'Hometown'),
        new OA\Property(property: 'prompt', type: 'string', example: 'Where are you from and what do you like about it?'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'Attempt',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'question_id', type: 'integer', example: 2),
        new OA\Property(property: 'answer_text', type: 'string', example: 'I am from Jakarta, a very busy city. I love the food and culture there.'),
        new OA\Property(property: 'estimated_band', type: 'number', format: 'float', example: 6.5, description: 'AI-estimated IELTS band score (0–9, increments of 0.5)'),
        new OA\Property(property: 'strengths', type: 'array', items: new OA\Items(type: 'string'), example: ['Clear sentence structure', 'Good use of vocabulary']),
        new OA\Property(property: 'areas_to_improve', type: 'array', items: new OA\Items(type: 'string'), example: ['Add more details', 'Use more complex grammar']),
        new OA\Property(property: 'question', ref: '#/components/schemas/Question'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'AuthResponse',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: '1|abc123def456...'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The email field is required.'),
        new OA\Property(property: 'errors', type: 'object'),
    ]
)]
class SwaggerInfo {}

