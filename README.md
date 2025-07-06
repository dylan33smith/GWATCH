# Gwatch

Full-stack web application built with Symfony 4 framework, featuring server-side rendering with Twig templates and Python data processing scripts.

## Project Structure

- `src/` - PHP source code (Controllers, Entities, Forms, etc.)
- `config/` - Symfony configuration files
- `templates/` - Twig template files for frontend
- `public/` - Web server document root
- `python/` - Python scripts for data processing
- `var/` - Cache and log files
- `vendor/` - Composer dependencies

## Technology Stack

- **Backend**: Symfony 4 (PHP 7.1+)
- **Database**: Doctrine ORM
- **Templates**: Twig
- **Data Processing**: Python scripts
- **Authentication**: HWIOAuthBundle
- **HTTP Client**: Guzzle/HTTPlug

## Getting Started

### Prerequisites
- PHP 7.1 or higher
- Composer
- Python 3.x
- Web server (Apache/Nginx)

### Installation
```bash
# Install PHP dependencies
composer install

# Set up environment variables
cp .env.example .env
# Edit .env with your database credentials

# Create database and run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Clear cache
php bin/console cache:clear
```

### Running the Application
```bash
# Development server
php bin/console server:start

# Or use Symfony CLI
symfony server:start
```

## Environment Variables

Create a `.env` file in the project root with:
```
DATABASE_URL="mysql://username:password@localhost/gwatch"
# or for MongoDB
DATABASE_URL="mongodb://username:password@localhost:27017/gwatch"
```

## Python Scripts

The `python/` directory contains data processing scripts:
- `mp.py` - Main processing script
- `mpss.py` - Secondary processing script
- `mp.sh` - Shell wrapper script 