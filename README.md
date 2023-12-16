# api.sandercokart.com

## Installation

### Local
```bash
composer install && cp .env.local.example .env && php artisan key:generate && php artisan migrate --seed && php artisan storage:link
```

### Production
```bash
composer install --no-dev --optimize-autoloader && php artisan key:generate && php artisan migrate --seed && php artisan storage:link 
```
