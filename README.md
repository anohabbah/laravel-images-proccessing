# Laravel, Intervention Image and DigitalOcean Spaces
Exercise files for the **Laravel, Intervention Image and DigitalOcean Spaces** screencast.

## Set up project

#### Clone repository and install composer dependencies

```bash
git clone git@github.com:ssd-tutorials/laravel-intervention-image-do-spaces.git

cd laravel-intervention-image-do-spaces

composer install
```

Copy .env file and generate application key

```bash
cp .env.example .env

php artisan key:generate
```

#### Create database and update .env file with credentials, then run migration with seeds

```bash
php artisan migrate --seed
```

#### Install npm dependencies and generate assets

```bash
npm install

npm run dev
```

Once completed, you can open project in the browser.
