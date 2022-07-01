set -e

echo "Deploying application ..."

set -e

echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true
    # Update codebase
    git pull origin main
# Exit maintenance mode
php artisan up

php artisan config:clear

php artisan view:clear

php artisan cache:clear

echo "Application deployed!"
