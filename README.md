##Instalación del proyecto:
-- Requerimientos
- PHP 8.0
- LARAVEL 9
- PostgreSQL 15

-- Comandos

	`git clone https://sairjcode@bitbucket.org/saircode1/ecommerce.git`
	`composer install`
	`npm install`

-- Configuración variables de entorno .env

	DB_CONNECTION=pgsql
	DB_HOST=localhost
	DB_PORT=5433
	DB_DATABASE=ecommerce
	DB_USERNAME=postgres
	DB_PASSWORD=981129

-- Luego

	`php artisan migrate`
	`php artisan serve`
	`npm run dev`
