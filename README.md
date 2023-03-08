# api.sandercokart.com

## Installation

### Local
```bash
composer install && php artisan key:generate && php artisan migrate --seed 
```

### Production
```bash
composer install --no-dev --optimize-autoloader && php artisan key:generate && php artisan migrate --seed 
```
