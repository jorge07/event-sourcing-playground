Event Sourcing Playground
=========================

> This repo is a work in progress

Wallet Api following DDD, ES, CQRS and Symfony Flex

### Bounded Contexts

- User
- Balance

### Api

- GET  /api/ping : 200
- POST /api/user : 204|400|409

### Environment

`docker-compose up -d`

`docker-compose exec fpm composer install`

### Tests

`docker-compose exec fpm composer run-script test`