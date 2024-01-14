# check if vendor folder exists, if not run composer install
if [ ! -d vendor ]; then
    echo "vendor folder not found!"
    echo "Running composer install"
    composer install
else
    echo "vendor folder found!"
fi

# check if .env file exists if not copy from .env.example
if [ ! -f .env ]; then
    echo ".env file not found!"
    echo "Copying .env.example to .env"
    cp .env.example .env

    # run artisan key:generate and storage:link
    php artisan key:generate
    php artisan storage:link

else
    echo ".env file found!"

    # check if APP_KEY is set in .env else run artisan key:generate
    # format is APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    if grep -q "APP_KEY=base64:" .env; then
        echo "APP_KEY found!"
    else
        echo "APP_KEY not found!"
        echo "Running php artisan key:generate"
        php artisan key:generate
    fi

    # check if storage link exists
    if [ ! -L public/storage ]; then
        echo "Storage link not found!"
        echo "Running php artisan storage:link"
        php artisan storage:link
    else
        echo "Storage link found!"
    fi

    echo "You are now ready to go!"
fi
