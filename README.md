# ArchiConnect App

<p align="center">
<img src="public/images/logo.png" width="400" alt="ArchiConnect Logo">
</p>

## About ArchiConnect

ArchiConnect is a web application designed for architectural firms to manage projects, track time, and collaborate with clients and freelancers. The application provides a comprehensive platform for:

- Project management and tracking
- Time logging and reporting
- Client communication and collaboration
- Freelancer assignment and management
- Budget tracking and appeals
- Work submission and review
- Payment processing

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: TailwindCSS, Alpine.js
- **Database**: MySQL
- **Build Tool**: Vite
- **Authentication**: Laravel Breeze

## Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and npm

## Local Development

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/archiconnect-app.git
   cd archiconnect-app
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Set up environment:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in the `.env` file.

5. Run migrations:

   ```bash
   php artisan migrate
   ```

6. Start the development server:

   ```bash
   npm run dev
   php artisan serve
   ```

## Deployment

For detailed deployment instructions, please see the [DEPLOYMENT.md](DEPLOYMENT.md) file.

## Features

- **User Management**
  - Role-based access control (Admin, Client, Freelancer)
  - User profiles and settings

- **Project Management**
  - Job creation and assignment
  - Task tracking and progress reporting
  - Deadline management

- **Time Tracking**
  - Start/stop timers
  - Time log review and approval
  - Reporting and analytics

- **Communication**
  - In-app messaging
  - File sharing
  - Comment threads on jobs

- **Payment Processing**
  - Budget management
  - Payment tracking
  - Invoice generation

## License

The ArchiConnect App is proprietary software. All rights reserved.
