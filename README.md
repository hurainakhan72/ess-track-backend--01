# ESS TRACKER Backend API

A professional PHP backend API for ESS TRACKER inquiry management system.

## Features

- Inquiry submission with validation
- Spam protection (max 3 inquiries per phone)
- Email notifications
- Database integration with environment variables
- CORS restriction
- Rate limiting
- JSON and Form data support
- HTTP status codes
- Error handling (hidden in production)

## Project Structure

See the tree in the code.

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure
4. Set up your database
5. Point your web server to `public/index.php`

## API Endpoints

- `POST /api/inquiries` - Submit inquiry
- `GET /api/status` - API status
