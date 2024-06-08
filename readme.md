# About this repo

This is an app made in Laravel 10, with MySQL and Redis. the following techniques are implented:

- Test Driven Development (TDD)
- Cache system
- Repository pattern
- Form Request
- Exception handling
- Eloquent Api Resource
- Code coverage
- Sonaqube (Externally implemented)
- JSON Web Token (JWT)

# Installation

- Download this repo on your computer

<pre>$ git clone git@github.com:soymiguelfigueroa/monoma-app.git</pre>

- Get into the downloaded directory

<pre>$ cd monoma-app</pre>

- Create the .env file duplicating the .env.example file

<pre>$ cp public_html/.env.example public_html/.env</pre>

- Up services with docker. Your need to build the image for the first time

<pre>$ docker-compose up -d --build</pre>

- after that you only need to run this command without --build flag

<pre>$ docker-compose up -d</pre>

# Endpoints

This app have 4 endpoints

## POST /api/auth

### Request

<pre>
{
    "username": "tester",
    "password": "PASSWORD"
}
</pre>

### Response 200 OK

<pre>
{
    "meta": {
        "success": true,
        "errors": []
    },
    "data": {
        "token": "TOOOKEN",
        "minutes_to_expires": 1440 
    }
}
</pre>

### Response 401 Unauthorized

<pre>
{
    "meta": {
        "success": false,
        "errors": [
            "Password incorrect for: tester"
        ]
    }
}
</pre>

## POST /lead

### Request

<pre>
{
    "name": "Mi candidato",
    "source": "Fotocasa",
    "owner": 2
}
</pre>

### Response 201 OK

<pre>
{
    "meta": {
        "success": true,
        "errors": []
    },
    "data": {
        "id": "1",
        "name": "Mi candidato",
        "source": "Fotocasa",
        "owner": 2,
        "created_at": "2020-09-01 16:16:16",
        "created_by": 1
    }
}
</pre>

### Response 401 Unauthorized

<pre>
{
    "meta": { 
        "success": false,
        "errors": [
            "Token expired"
        ]
    }
}
</pre>

## GET /lead/{id}

### Response 200 OK

<pre>
{
    "meta": { 
        "success": true,
        "errors": []
    },
    "data": { 
        "id": "1",
        "name": "Mi candidato",
        "source": "Fotocasa",
        "owner": 2,
        "created_at": "2020-09-01 16:16:16",
        "created_by": 1
    }
}
</pre>

### Response 401 Unauthorized

<pre>
{
    "meta": {
        "success": false,
        "errors": [
            "Token expired"
        ]
    }
}
</pre>

### Response 404 Not found

<pre>
{
    "meta": {
        "success": false,
        "errors": [
            "No lead found"
        ]
    }
}
</pre>

## GET /leads

### Response 200 OK

<pre>
{
    "meta": { 
        "success": true,
        "errors": []
    },
    "data": [
        {
            "id": "1",
            "name": "Mi candidato",
            "source": "Fotocasa",
            "owner": 2,
            "created_at": "2020-09-01 16:16:16",
            "created_by": 1
        },
        {
            "id": "2",
            "name": "Mi candidato 2",
            "source": "Habitaclia",
            "owner": 2,
            "created_at": "2020-09-01 16:16:16",
            "created_by": 1
        }
    ]
}
</pre>

### Response 401 Unauthorized

<pre>
{
    "meta": { 
        "success": false,
        "errors": [ 
            "Token expired"
        ]
    }
}
</pre>

# Testing

You need to stay on public_html directory to execute these comands

## Unit tests

To run unit tests you need to execute the artisan command

<pre>$ php artisan test</pre>

Alternatively, you can execute

<pre>$ php ./vendor/bin/phpunit</pre>

## Code coverage

To run code coverage you need to execute the artisan command

<pre>$ php artisan test --coverage</pre>