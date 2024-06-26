# Distributed Systems Project App

## Requirements

- The only requirement is to have Docker installed on your machine.

## How to run

- Clone the repository

- Run the following command to build the Docker image:

```bash
./setup.sh
```

- Run the migrations to set up the database:

```bash
./vendor/bin/sail artisan migrate && ./vendor/bin/sail artisan migrate --database=mysql2
```

## How to use

Run the application:

```bash
./vendor/bin/sail up -d
```

Stop the application:

```bash
./vendor/bin/sail down
```

The app locally works at `http://localhost:9000` and exposes the following endpoints:

- `POST api/transaction`: Create a new transaction and stores it in the database.
  - Schema:

    ```json
    {
        "amount": 25.0,
        "currency": "EUR",
        "description": "Some payment",
        "userId": "1"
    }
    ```

The address stated above is the intended entry point for requests, as this is actually a load balancer in Node.js that forwards the requests to one random instance out of the 3 Laravel containers running.

The above endpoint was tested in Postman and in the response headers you can see the Host header randomly changing between the 3 Laravel containers. Stopping a container will make the load balancer to avoid sending requests to it.

The main file for the load balancer is `./loadbalancer/index.js`.

## Health checks

The load balancer periodically checks the health of the Laravel containers by sending a `GET` request to the `/hello` endpoint on each service. If the container responds with a `200` status code, it is considered healthy. Otherwise, it is marked as unhealthy and the load balancer stops sending requests to it until it's detected as live again.

## Circuit Breaker

The load balancer also implements a circuit breaker mechanism. In the application cache a boolean is stored indicating if the `cloudcomputing` service is live. If the service is down, no requests will be sended to it until it's detected as live again.

In order to update the status of the service, a scheduled task is run every minute that checks the health of the service by sending a request to it. If the service is down, the circuit breaker is activated until the scheduled task detects the service as live again.

By default the circuit breaker is ignored. In order to enable it you need to start the scheduled task by running the following command:

```bash
./vendor/bin/sail artisan schedule:work
```

## Database shards

The application uses two different databases to store the transactions. The first database is the default one and the second one is a shard. When a transaction is going to be stored, its id is hashed and the result is used to determine in which database the transaction is going to be stored.  
More databases instances can be added. The necessary changes must be:

- Add the new database in the `docker-compose.yml` file
- Add the new database in the `.env` file
- Add the new database in the `config/database.php` file
- Run the migrations for the new database
