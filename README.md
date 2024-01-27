# Laravel Test Task

This is a  test application Backend API for a blog application using Laravel with PHP 8, MySQL, JWT and MongoDB.

There are posts in the project to which comments can be added. Users create comments to these posts. All users who have left comments are automatically subscribed to receive notifications about new comments. When a new comment is added, a batch mailing is performed.

Pagination optimization is also implemented in case of a large number of posts
## CRUD

## Installation

1. Clone this repository:
```
git clone https://github.com/tailerodessacity/lior.git
```
2. Clone laradock:
```
git clone https://github.com/laradock/laradock.git
```

2. Go to 'laradock' directory:
```
cp .env.example ../../../lior/laradock/.env
```
3. Run your containers:
```
docker-compose up -d --build nginx php-fpm mysql mongo
```
4. Go to container:
```
docker-compose exec workspace bash
```
5. Run following commands:
```
composer install
```
6. Run following commands:
```
php artisan migrate
```
7. Run following commands:
```
php artisan db:seed
```
8. Get jwt token:
```
curl -X POST -H "Content-Type: application/json" -d '{"email":"test@example.com","password":"HAS123456"}' http://localhost/api/auth/login
```

```
Use the jwt token for all requests except get
```

## Testing

To run the unit tests:
```
docker-compose exec workspace php artisan test --testsuite=Feature,Performance
```
